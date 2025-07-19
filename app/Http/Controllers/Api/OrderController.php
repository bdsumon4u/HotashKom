<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $_start = Carbon::parse(\request('start_d'));
        $_end = Carbon::parse(\request('end_d'));

        $orders = Order::with('admin');
        if (strtolower($request->type) === 'online') {
            $orders->where('type', Order::ONLINE);
        } elseif (strtolower($request->type) === 'manual') {
            $orders->where('type', Order::MANUAL);
        }

        if ($request->user_id) {
            $orders->where('user_id', $request->user_id);
        }

        if ($request->phone) {
            $orders->where('phone', $request->phone);
        }

        if ($request->status) {
            $orders->where('status', $request->status);
        }

        if ($request->staff_id) {
            $orders->where('admin_id', $request->staff_id);
        }

        if ($request->has('start_d') && $request->has('end_d')) {
            $orders->whereBetween(request('date_type', 'status_at'), [
                $_start->startOfDay()->toDateTimeString(),
                $_end->endOfDay()->toDateTimeString(),
            ]);
        }

        if ($request->shipped_at) {
            $shippedDate = Carbon::parse($request->shipped_at);
            $orders->whereNotNull('shipped_at')
                ->whereBetween('shipped_at', [
                    $shippedDate->startOfDay()->toDateTimeString(),
                    $shippedDate->endOfDay()->toDateTimeString(),
                ]);
        }

        $orders = $orders->when($request->role_id == Admin::SALESMAN, function ($orders): void {
            $orders->where('admin_id', request('admin_id'));
        });
        $orders = $orders->when(! $request->has('order'), function ($orders): void {
            $orders->latest('id');
        });

        if (isOninda()) {
            $orders->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->select('orders.*', 'users.domain', 'users.shop_name', 'users.order_prefix');
        }

        $salesmans = Admin::where('role_id', Admin::SALESMAN)->get(['id', 'name'])->pluck('name', 'id');

        $dt = DataTables::of($orders)
            ->addIndexColumn()
            ->setRowAttr([
                'style' => function ($row) {
                    if ($row->data['is_fraud'] ?? false) {
                        return 'background: #ff9e9e';
                    }
                    if (! ($row->data['is_fraud'] ?? false) && ($row->data['is_repeat'] ?? false)) {
                        return 'background: #ffeeaa';
                    }
                },
            ])
            ->editColumn('id', fn ($row): string => '<a class="px-2 btn btn-light btn-sm text-nowrap" href="'.route('admin.orders.edit', $row->id).'">'.$row->id.'<i class="ml-1 fa fa-eye"></i></a>')
            ->editColumn('source_id', function ($row): string {
                if (! $row->source_id) {
                    return '';
                }

                if (isOninda()) {
                    $id = $row->order_prefix.$row->source_id;
                    $url = request()->getScheme().'://'.$row->domain.'/track-order?order='.$row->source_id;

                    return '<a target="_blank" title="'.$row->shop_name.'" class="px-2 btn btn-light btn-sm text-nowrap" href="'.$url.'">'.$id.'<i class="ml-1 fa fa-eye"></i></a>';
                }

                if (isReseller()) {
                    $url = config('app.oninda_url').'/track-order?order='.$row->source_id;

                    return '<a target="_blank" class="px-2 btn btn-light btn-sm text-nowrap" href="'.$url.'">'.$row->source_id.'<i class="ml-1 fa fa-eye"></i></a>';
                }

                return '';
            })
            ->editColumn('created_at', fn ($row): string => "<div class='text-nowrap'>".$row->created_at->format('d-M-Y').'<br>'.$row->created_at->format('h:i A').'</div>')
            ->addColumn('amount', fn ($row): int => intval($row->data['subtotal']) + intval($row->data['shipping_cost']) - intval($row->data['discount'] ?? 0) - intval($row->data['advanced'] ?? 0))
            ->editColumn('status', function ($row) {
                $return = '<select data-id="'.$row->id.'" onchange="changeStatus" class="status-column form-control-sm">';
                foreach (config('app.orders', []) as $status) {
                    $selected = $status === $row->status ? 'selected' : '';
                    $return .= '<option value="'.$status.'" '.$this->isDisabled($row, $status).' '.$selected.'>'.$status.'</option>';
                }

                return $return.'</select>';
            })
            ->addColumn('checkbox', fn ($row): string => '<input type="checkbox" class="form-control" name="order_id[]" value="'.$row->id.' '.$this->isDisabled($row).' " style="min-height: 20px;min-width: 20px;max-height: 20px;max-width: 20px;">')
            ->editColumn('customer', fn ($row): string => "
                    <div>
                        <div><i class='mr-1 fa fa-user'></i>{$row->name}</div>
                        <div><i class='mr-1 fa fa-phone'></i><a href='tel:{$row->phone}'>{$row->phone}</a></div>
                        <div><i class='mr-1 fa fa-map-marker'></i>{$row->address}</div>".
                ($row->note ? "<div class='text-danger'><i class='mr-1 fa fa-sticky-note-o'></i>{$row->note}</div>" : '').
                '</div>')
            ->editColumn('products', function ($row) {
                $products = '<ul style="list-style: none; padding-left: 1rem;">';
                foreach ((array) ($row->products) ?? [] as $product) {
                    $products .= "<li>{$product->quantity} x <a class='text-underline' href='".route('products.show', $product->slug)."' target='_blank'>{$product->name}</a></li>";
                }

                return $products.'</ul>';
            })
            ->addColumn('courier', function ($row) {
                $link = '';
                $selected = ($row->data['courier'] ?? false) ? $row->data['courier'] : 'Other';

                $return = '<select data-id="'.$row->id.'" onchange="changeCourier" class="courier-column form-control-sm">';
                foreach (couriers() as $provider) {
                    $return .= '<option value="'.$provider.'" '.($provider == $selected ? 'selected' : '').' '.$this->isDisabled($row).'>'.$provider.'</option>';
                }
                $return .= '</select>';

                if (! ($row->data['courier'] ?? false)) {
                    return $return;
                }

                if ($row->data['courier'] == 'Pathao') {
                    // append city, area and weight
                    $return .= '<div style="white-space: nowrap;">City: '.($row->data['city_name'] ?? '<strong class="text-danger">N/A</strong>').'</div>';
                    $return .= '<div style="white-space: nowrap;">Area: '.($row->data['area_name'] ?? '<strong class="text-danger">N/A</strong>').'</div>';
                    $return .= '<div style="white-space: nowrap;">Weight: '.($row->data['weight'] ?? '0.5').' kg</div>';

                    $link = 'https://merchant.pathao.com/tracking?consignment_id='.($row->data['consignment_id'] ?? '').'&phone='.Str::after($row->phone, '+88');
                } elseif ($row->data['courier'] == 'Redx') {
                    // append area and weight
                    $return .= '<div style="white-space: nowrap;">Area: '.($row->data['area_name'] ?? '<strong class="text-danger">N/A</strong>').'</div>';
                    $return .= '<div style="white-space: nowrap;">Weight: '.($row->data['weight'] ?? '500').' gm</div>';
                    $link = 'https://redx.com.bd/track-global-parcel/?trackingId='.($row->data['consignment_id'] ?? '');
                } elseif ($row->data['courier'] == 'SteadFast') {
                    $link = 'https://www.steadfast.com.bd/user/consignment/'.($row->data['consignment_id'] ?? '');
                }

                if ($cid = $row->data['consignment_id'] ?? false) {
                    $return .= '<div style="white-space: nowrap;">C.ID: <a href="'.$link.'" target="_blank">'.$cid.'</a></div>';
                } elseif ($row->data['courier'] != 'Other') {
                    $return .= '<a href="'.route('admin.orders.booking', ['order_id' => $row->id]).'" class="btn btn-sm btn-primary">Submit</a>';
                }

                return $return.'<div style="white-space: nowrap; display: none;">Tracking Code: <a href="https://www.steadfast.com.bd/?tracking_code=" target="_blank"></a></div>';
            })
            ->filterColumn('customer', function ($query, $keyword): void {
                $query->where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('phone', 'like', '%'.$keyword.'%')
                    ->orWhere('address', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('courier', function ($query, $keyword): void {
                $query->where('data->courier', 'like', '%'.$keyword.'%')
                    ->orWhere('data->consignment_id', 'like', '%'.$keyword.'%');
            });

        if (isOninda()) {
            $dt = $dt->filterColumn('source_id', function ($query, $keyword): void {
                // Support search in the format PREFIX + ORDER_ID (digits at the end)
                if (preg_match('/^([^\d]+)(\d+)$/', $keyword, $matches)) {
                    $prefix = $matches[1];
                    $orderId = $matches[2];
                    // If users table is joined (isOninda), filter by both prefix and source_id
                    $query->where('users.order_prefix', $prefix)
                        ->where('orders.source_id', $orderId);
                } else {
                    // Fallback: search source_id as before
                    $query->where('orders.source_id', 'like', "%$keyword%");
                }
            });
        }

        $dt = $dt->editColumn('staff', function ($row) use ($salesmans) {
            $return = '<select data-id="'.$row->id.'" onchange="changeStaff" class="staff-column form-control-sm">';
            if (! isset($salesmans[$row->admin_id])) {
                $return .= '<option value="'.$row->admin_id.'" selected '.$this->isDisabled($row).'>'.$row->admin->name.'</option>';
            }
            foreach ($salesmans as $id => $name) {
                $return .= '<option value="'.$id.'" '.($id == $row->admin_id ? 'selected' : '').' '.$this->isDisabled($row).'>'.$name.'</option>';
            }

            return $return.'</select>';
        })
            ->filterColumn('created_at', function ($query, $keyword): void {
                if (str_contains($keyword, ' - ')) {
                    [$start, $end] = explode(' - ', $keyword);
                    $query->whereBetween('created_at', [
                        Carbon::parse($start)->startOfDay(),
                        Carbon::parse($end)->endOfDay(),
                    ]);
                }
            })
            ->addColumn('actions', function (Order $order) {
                $actions = '<div class="btn-group">';
                if (! $order->source_id) {
                    $actions .= '<a href="'.route('admin.orders.destroy', $order).'" data-action="delete" class="btn btn-sm btn-danger">Delete</a>';
                }
                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['checkbox', 'id', 'source_id', 'customer', 'products', 'status', 'courier', 'staff', 'created_at', 'actions']);

        return $dt->make(true);
    }

    private function isDisabled(Order $order, string $status = ''): string
    {
        if (config('app.oninda_url') && $order->source_id) {
            return 'disabled title="This order is managed by Oninda"';
        }

        if (! $status) {
            return '';
        }

        if ($order->status === 'DELIVERED') {
            return $status !== 'RETURNED' ? 'disabled' : '';
        }

        return $status === 'RETURNED' ? 'disabled' : '';
    }
}
