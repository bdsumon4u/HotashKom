<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class ResellerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('user')->user();

        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSales = Order::where('user_id', $user->id)
            ->whereIn('status', ['CONFIRMED', 'INVOICED', 'SHIPPING', 'DELIVERED'])
            ->sum('data->subtotal');
        $availableBalance = $user->getAvailableBalance();

        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = $user->wallet->transactions()
            ->latest()
            ->take(5)
            ->get();

        return view('reseller.dashboard', compact(
            'totalOrders',
            'totalSales',
            'availableBalance',
            'recentOrders',
            'recentTransactions'
        ));
    }

    public function products(Request $request)
    {
        $query = Product::with(['variations.options', 'images', 'brand', 'categories'])
            ->whereNull('parent_id')
            ->where('is_active', 1);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', $search);
            });
        }

        // Filter by hot sale
        if ($request->filled('hot_sale')) {
            $query->where('hot_sale', $request->hot_sale);
        }

        // Filter by new arrival
        if ($request->filled('new_arrival')) {
            $query->where('new_arrival', $request->new_arrival);
        }

        // Debug: Log the query and filters
        \Log::info('Reseller products query', [
            'filters' => [
                'search' => $request->search,
                'hot_sale' => $request->hot_sale,
                'new_arrival' => $request->new_arrival,
            ],
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $products = $query->latest()->paginate(20);

        return view('reseller.products', compact('products'));
    }

    public function checkout(Request $request)
    {
        $user = Auth::guard('user')->user();

        // Handle POST request (form submission)
        if ($request->isMethod('post')) {
            return $this->processCheckout($request, $user);
        }

        // Handle GET request (display checkout form)
        return view('reseller.checkout');
    }

    private function processCheckout(Request $request, $user)
    {
        // Validate the request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^1\d{9}$/'],
            'address' => ['required', 'string'],
            'shipping' => ['required', 'in:Inside Dhaka,Outside Dhaka'],
            'note' => ['nullable', 'string'],
        ]);

        // Process the checkout using the existing checkout logic
        // For now, redirect to the main checkout with the form data
        return to_route('checkout')->with([
            'reseller_checkout_data' => $request->all(),
        ]);
    }

    public function thankYou(Request $request)
    {
        if (! $request->has('order')) {
            return view('track-order');
        }
        $order = Order::where(['id' => $request->order])->first();
        if (! $order instanceof Order) {
            return back()->withDanger('Invalid Tracking Info Or Order Record Was Deleted.');
        }

        return view('reseller.thank-you', compact('order'));
    }

    public function orders(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::guard('user')->user();
            $query = Order::where('user_id', $user->id);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Get pagination parameters from DataTables
            $length = $request->input('length', 50);
            $start = $request->input('start', 0);

            // Get total count for pagination
            $totalRecords = $query->count();

            // Get paginated results
            $orders = $query->latest()->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $orders->map(function ($order) {
                    // Calculate total from subtotal, shipping cost, discount, and advanced
                    $subtotal = (float) ($order->data['subtotal'] ?? 0);
                    $shippingCost = (float) ($order->data['shipping_cost'] ?? 0);
                    $discount = (float) ($order->data['discount'] ?? 0);
                    $advanced = (float) ($order->data['advanced'] ?? 0);
                    $total = $subtotal + $shippingCost - $discount - $advanced;

                    // Format customer information with icons
                    $customer = "<div>
                        <div><i class='mr-1 fa fa-user'></i>{$order->name}</div>
                        <div><i class='mr-1 fa fa-phone'></i><a href='tel:{$order->phone}'>{$order->phone}</a></div>
                        <div><i class='mr-1 fa fa-map-marker'></i>{$order->address}</div>".
                        ($order->note ? "<div class='text-danger'><i class='mr-1 fa fa-sticky-note-o'></i>{$order->note}</div>" : '').
                        '</div>';

                    // Format products information
                    $products = '<ul style="list-style: none; padding-left: 1rem;">';
                    if ($order->products) {
                        $productsArray = is_array($order->products) ? $order->products : (array) $order->products;
                        if (! empty($productsArray)) {
                            foreach ($productsArray as $product) {
                                $product = (array) $product;
                                $products .= "<li>{$product['quantity']} x <a class='text-underline' href='".route('products.show', $product['slug'])."' target='_blank'>{$product['name']}</a></li>";
                            }
                        } else {
                            $products .= '<li>No products found</li>';
                        }
                    } else {
                        $products .= '<li>No products found</li>';
                    }
                    $products .= '</ul>';

                    // Courier tracking link parts
                    $courier = $order->data['courier'] ?? null;
                    $consignmentId = $order->data['consignment_id'] ?? null;
                    $trackingUrl = '';
                    if ($courier && $consignmentId) {
                        if ($courier === 'Pathao') {
                            $trackingUrl = 'https://merchant.pathao.com/tracking?consignment_id='.$consignmentId.'&phone='.\Illuminate\Support\Str::after($order->phone, '+88');
                        } elseif ($courier === 'Redx') {
                            $trackingUrl = 'https://redx.com.bd/track-global-parcel/?trackingId='.$consignmentId;
                        } elseif ($courier === 'SteadFast') {
                            $trackingUrl = 'https://www.steadfast.com.bd/user/consignment/'.$consignmentId;
                        }
                    }

                    return [
                        'id' => $order->id,
                        'created_at' => $order->created_at->format('d-M-Y h:i A'),
                        'customer' => $customer,
                        'products' => $products,
                        'consignment_id' => $consignmentId,
                        'tracking_url' => $trackingUrl,
                        'status' => $order->status,
                        'subtotal' => theMoney($subtotal),
                        'total' => theMoney($total),
                        'actions' => $order->id, // Pass order ID for actions column
                    ];
                }),
            ]);
        }

        $user = Auth::guard('user')->user();
        $query = Order::where('user_id', $user->id);

        // Get filtered total based on status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $totalOrders = $query->count();

        return view('reseller.orders', compact('totalOrders'));
    }

    public function showOrder(Order $order)
    {
        $user = Auth::guard('user')->user();

        abort_if($order->user_id !== $user->id, 403);

        return view('reseller.order-show', compact('order'));
    }

    public function editOrder(Order $order)
    {
        $user = Auth::guard('user')->user();

        abort_if($order->user_id !== $user->id, 403, 'You can only edit your own orders.');

        // Check if order status allows editing
        abort_unless(in_array($order->status, ['PENDING', 'CONFIRMED']), 403, 'You can only edit orders with PENDING or CONFIRMED status.');

        return view('reseller.order-edit', compact('order'));
    }

    public function cancelOrder(Order $order)
    {
        $user = Auth::guard('user')->user();

        abort_if($order->user_id !== $user->id, 403, 'You can only cancel your own orders.');

        // Check if order status allows cancellation
        abort_unless(in_array($order->status, ['PENDING', 'CONFIRMED']), 403, 'You can only cancel orders with PENDING or CONFIRMED status.');

        // Update order status to CANCELLED
        $order->update([
            'status' => 'CANCELLED',
            'status_at' => now()->toDateTimeString(),
        ]);

        return to_route('reseller.orders')
            ->with('success', 'Order cancelled successfully.');
    }

    public function transactions(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::guard('user')->user();
            $transactions = $user->wallet->transactions()->latest()->paginate(50);

            return response()->json([
                'draw' => $request->draw,
                'recordsTotal' => $transactions->total(),
                'recordsFiltered' => $transactions->total(),
                'data' => $transactions->map(fn ($transaction, $index): array => [
                    'DT_RowIndex' => $index + 1,
                    'type' => $transaction->type === 'deposit'
                        ? '<span class="badge badge-success">Deposit</span>'
                        : '<span class="badge badge-danger">Withdraw</span>',
                    'amount' => number_format((float) $transaction->amount, 2).' tk',
                    'created_at' => $transaction->created_at->format('d-M-Y H:i'),
                    'status' => $transaction->confirmed ? 'COMPLETED' : 'PENDING',
                    'meta' => (function ($meta) {
                        if (isset($meta['trx_id']) && isset($meta['admin_id'])) {
                            return '<span class="text-muted">Trx ID: '.e($meta['trx_id']).' by staff #'.e($meta['admin_id']).'</span>';
                        }

                        $title = $meta['reason'] ?? 'N/A';
                        if ($id = $meta['order_id'] ?? false) {
                            return '<a target="_blank" href="'.route('reseller.orders.show', $id).'">'.e($title).'</a>';
                        }

                        return e($title);
                    })($transaction->meta),
                ]),
            ]);
        }

        return view('reseller.transactions');
    }

    public function profile()
    {
        return view('reseller.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('user')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shop_name' => ['required', 'string', 'max:255'],
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone_number' => ['required', 'string', 'max:20'],
            'bkash_number' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'domain' => 'nullable|string|max:255|unique:users,domain,'.$user->id,
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'inside_dhaka_shipping' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'outside_dhaka_shipping' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $user->fill($request->only([
            'name', 'shop_name', 'email', 'phone_number',
            'bkash_number', 'address', 'domain',
            'inside_dhaka_shipping', 'outside_dhaka_shipping',
        ]));

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($user->logo && Storage::disk('public')->exists($user->logo)) {
                Storage::disk('public')->delete($user->logo);
            }

            $logoPath = $request->file('logo')->store('reseller-logos', 'public');
            $user->logo = $logoPath;
        }

        $user->save();

        return to_route('reseller.profile')
            ->with('success', 'Profile updated successfully!');
    }
}
