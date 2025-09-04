@extends('layouts.reseller.master')

@section('title', 'Order Details')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/print.css')}}">
@endpush

@push('styles')
    <style>
        .only-print {
            display: none;
        }
        @media print {
            html, body {
                height:100vh;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
                background: white !important;
            }
            .main-nav {
                display: none !important;
                width: 0 !important;
            }
            .print-edit-buttons,
            .footer {
                display: none !important;
            }
            .page-body {
                font-size: 20px;
                margin-top: 0 !important;
                margin-left: 0 !important;
                page-break-after: always;
                background: white !important;
            }
            .page-body p {
                font-size: 16px !important;
            }
            .only-print {
                display: block;
                padding-top: 2rem;
            }
            /* Remove all backgrounds and make invoice fill the page */
            .container-fluid,
            .card,
            .card-body,
            .invoice {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
            /* Remove all padding and margins in print mode */
            * {
                margin: 0 !important;
                padding: 0 !important;
            }
            .container-fluid,
            .row,
            .col-md-4,
            .col-md-6,
            .col-12,
            .col-6,
            .col-3 {
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Ensure invoice takes full page */
            .invoice {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                width: 100% !important;
                max-width: none !important;
            }
            /* Remove all spacing from invoice elements */
            .invoice .row,
            .invoice .col-6,
            .invoice .col-3,
            .invoice .media,
            .invoice .media-left,
            .invoice .media-body {
                margin: 0 !important;
                padding: 0 !important;
            }
            /* Fix barcode overflow by reducing padding and ensuring proper containment */
            .invoice .col-3,
            .invoice .text-md-right,
            .invoice #project {
                padding: 0 !important;
                margin: 0 !important;
                overflow: hidden !important;
            }
            .invoice img[src*="barcode"] {
                max-width: 100% !important;
                height: auto !important;
                max-height: 80px !important;
            }
            /* Ensure invoice content doesn't overflow */
            .invoice .row {
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice .col-6,
            .invoice .col-3 {
                padding: 0 !important;
            }
            /* Hide only order details sections in print mode */
            .card-header,
            .alert,
            .order-details-section,
            .print-edit-buttons {
                display: none !important;
            }
            /* Hide sidebar elements that might overlap */
            .sidebar-toggler,
            .toggle-nav,
            .toggler-sidebar,
            .navbar-toggler,
            .navbar-toggler-icon,
            .hamburger,
            .menu-toggle,
            .sidebar-toggle,
            .nav-toggle,
            .toggle-sidebar {
                display: none !important;
            }
            /* Hide any overlapping elements in invoice */
            .invoice .media-left button,
            .invoice .media-left .btn,
            .invoice .media-left .dropdown,
            .invoice .media-left .toggle,
            .invoice .media-left .menu,
            .invoice .media-left .hamburger,
            .invoice .media-left .navbar-toggler,
            .invoice .media-left .navbar-toggler-icon {
                display: none !important;
            }
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Order Details</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('reseller.orders') }}">Orders</a></li>
    <li class="breadcrumb-item">Order #{{ $order->id }}</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="mb-5 row">
            <div class="col-sm-12">
                <div class="shadow-sm card rounded-0">
                    <div class="p-3 card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Order #{{ $order->id }}</h5>
                            <small>{{ $order->created_at->format('d-M-Y h:i A') }}</small>
                            <div>
                                <span class="badge badge-{{
                                    $order->status == 'PENDING' ? 'warning' :
                                    ($order->status == 'CONFIRMED' ? 'success' :
                                    ($order->status == 'CANCELLED' ? 'danger' : 'info'))
                                }}">{{ $order->status }}</span>
                                @if(in_array($order->status, ['PENDING', 'CONFIRMED']))
                                    <a href="{{ route('reseller.orders.edit', $order) }}" class="ml-2 btn btn-warning btn-sm">Edit Order</a>
                                    <form method="POST" action="{{ route('reseller.orders.cancel', $order) }}" style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                                        @csrf
                                        <button type="submit" class="ml-2 btn btn-danger btn-sm">Cancel Order</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-3 card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row order-details-section">
                            <div class="col-md-4">
                                <h6>Buy Information (Purchase)</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td>{!! theMoney($order->data['subtotal'] ?? 0) !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Shipping Cost:</strong></td>
                                        <td>{!! theMoney($order->data['shipping_cost'] ?? 0) !!}</td>
                                    </tr>
                                    @if(isOninda() && config('app.resell'))
                                    <tr>
                                        <td><strong>Packaging Charge:</strong></td>
                                        <td>{!! theMoney($order->data['packaging_charge'] ?? 25) !!}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Discount:</strong></td>
                                        <td>{!! theMoney($order->data['discount'] ?? 0) !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total:</strong></td>
                                        <td><strong>{!! theMoney((float) ($order->data['subtotal'] ?? 0) + (float) ($order->data['shipping_cost'] ?? 0) + (isOninda() && config('app.resell') ? (float) ($order->data['packaging_charge'] ?? 25) : 0) - (float) ($order->data['discount'] ?? 0)) !!}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h6>Sell Information (Retail)</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td>{!! theMoney($retail = collect($order->products ?? [])->sum(function($product) { return ($product->retail_price ?? $product->price ?? 0) * ($product->quantity ?? 0); })) !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Delivery Fee:</strong></td>
                                        <td>{!! theMoney($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost'] ?? 0) !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Discount:</strong></td>
                                        <td>{!! theMoney($order->data['retail_discount'] ?? 0) !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Advanced:</strong></td>
                                        <td>{!! theMoney($order->data['advanced'] ?? 0) !!}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Grand Total:</strong></td>
                                        <td><strong>{!! theMoney((float) $retail + (float) ($order->data['retail_delivery_fee'] ?? $order->data['shipping_cost'] ?? 0) - (float) ($order->data['retail_discount'] ?? 0) - (float) ($order->data['advanced'] ?? 0)) !!}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h6>Customer Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $order->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $order->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $order->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong></td>
                                        <td>{{ $order->address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Note:</strong></td>
                                        <td>{{ $order->note ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 row order-details-section">
                            <div class="col-12">
                                <h6>Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Buy Price</th>
                                                <th>Sell Price</th>
                                                <th>Quantity</th>
                                                <th>Buy Total</th>
                                                <th>Sell Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($order->products && !empty($order->products))
                                                @foreach($order->products as $product)
                                                    @php
                                                        $buyPrice = $product->price ?? 0;
                                                        $sellPrice = $product->retail_price ?? 0;
                                                        $quantity = $product->quantity ?? 0;
                                                        $buyTotal = $buyPrice * $quantity;
                                                        $sellTotal = $sellPrice * $quantity;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $product->name ?? 'N/A' }}</td>
                                                        <td>{!! theMoney($buyPrice) !!}</td>
                                                        <td>{!! theMoney($sellPrice) !!}</td>
                                                        <td>{{ $quantity }}</td>
                                                        <td>{!! theMoney($buyTotal) !!}</td>
                                                        <td>{!! theMoney($sellTotal) !!}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-active">
                                                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                                    <td><strong>{!! theMoney((float) ($order->data['subtotal'] ?? 0)) !!}</strong></td>
                                                    <td><strong>{!! theMoney($retail) !!}</strong></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">No products found</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if(isset($order->data['notes']) && $order->data['notes'])
                            <div class="mt-4 row order-details-section">
                                <div class="col-12">
                                    <h6>Order Notes</h6>
                                    <p>{{ $order->data['notes'] }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Invoice Section for Printing -->
                        <div class="mt-4">
                            @php
                                $settings = \App\Models\Setting::array();
                                $defaultCompany = (object) ($settings['company'] ?? []);
                                $defaultLogo = (object) ($settings['logo'] ?? []);
                                $user = $order->user;
                                $hasLogo = $user && $user->logo;
                                $shopName = $user && $user->shop_name ? $user->shop_name : null;

                                // Use reseller's information
                                $companyName = $shopName ?? ($defaultCompany->name ?? '');
                                $logoUrl = $hasLogo ? asset('storage/' . $user->logo) : (isset($defaultLogo->mobile) ? asset($defaultLogo->mobile) : null);
                                $phoneNumber = ($user && $user->phone_number) ? $user->phone_number : ($defaultCompany->phone ?? '');
                                $address = ($user && $user->address) ? $user->address : ($defaultCompany->address ?? '');

                                // For reseller invoice, sender info is same as header info
                                $senderName = $companyName;
                                $senderPhone = $phoneNumber;
                                $senderAddress = $address;
                            @endphp
                            @include('admin.orders.invoice', [
                                'companyName' => $companyName,
                                'logoUrl' => $logoUrl,
                                'phoneNumber' => $phoneNumber,
                                'address' => $address,
                                'senderName' => $senderName,
                                'senderPhone' => $senderPhone,
                                'senderAddress' => $senderAddress
                            ])
                        </div>

                        <div class="gap-2 mt-3 text-center col-sm-12 print-edit-buttons d-flex justify-content-center">
                            @if(in_array($order->status, ['PENDING', 'CONFIRMED']))
                                <a href="{{ route('reseller.orders.edit', $order->id) }}" class="mr-2 btn btn-warning">
                                    Edit Order
                                </a>
                            @endif
                            <button class="btn btn-primary" type="button" onclick="myFunction()">
                                Print Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/print.js')}}"></script>
@endpush
