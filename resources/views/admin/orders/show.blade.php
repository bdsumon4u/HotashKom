@extends('layouts.light.master')
@section('title', 'Invoice')

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
            }
            .page-body p {
                font-size: 16px !important;
            }
            .only-print {
                display: block;
                padding-top: 2rem;
            }
        }
    </style>
@endpush

@section('breadcrumb-title')
    <h3>Invoice</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.index') }}">Orders</a>
    </li>
    <li class="breadcrumb-item">Invoice</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="invoice">
                        <div>
                        @php
                            $settings = \App\Models\Setting::array();
                            $defaultCompany = (object) ($settings['company'] ?? []);
                            $defaultLogo = (object) ($settings['logo'] ?? []);

                            if (!isOninda() || !(setting('show_option')->resellers_invoice ?? false)) {
                                // Not Oninda app OR resellers_invoice is false - use current website's settings for header
                                $companyName = $defaultCompany->name ?? '';
                                $logoUrl = isset($defaultLogo->mobile) ? asset($defaultLogo->mobile) : null;
                                $phoneNumber = $defaultCompany->phone ?? '';
                                $address = $defaultCompany->address ?? '';

                                // Get reseller's information for sender's section
                                $user = $order->user;
                                $senderName = ($user && $user->shop_name) ? $user->shop_name : ($defaultCompany->name ?? '');
                                $senderPhone = ($user && $user->phone_number) ? $user->phone_number : ($defaultCompany->phone ?? '');
                                $senderAddress = ($user && $user->address) ? $user->address : ($defaultCompany->address ?? '');
                            } else {
                                // Oninda app with resellers_invoice enabled
                                $user = $order->user;
                                $hasLogo = $user && $user->logo;
                                $shopName = $user && $user->shop_name ? $user->shop_name : null;

                                // Get pre-fetched reseller data
                                $resellerInfo = $user ? ($resellerData[$user->id] ?? null) : null;
                                $isResellerConnected = $resellerInfo && $resellerInfo['connected'];

                                if ($isResellerConnected) {
                                    // Use pre-fetched reseller database settings
                                    $resellerCompany = $resellerInfo['company'];
                                    $resellerLogo = $resellerInfo['logo'];

                                    // Company name: reseller shop_name, fallback to reseller's company name, then current website
                                    $companyName = $shopName ?? ($resellerCompany->name ?? ($defaultCompany->name ?? ''));

                                    // Logo: reseller logo, fallback to reseller's logo, then current website logo
                                    $logoUrl = $hasLogo ? asset('storage/' . $user->logo) : (isset($resellerLogo->mobile) ? asset($resellerLogo->mobile) : (isset($defaultLogo->mobile) ? asset($defaultLogo->mobile) : null));

                                    // Phone and address: use reseller's from database, fallback to user fields, then current website
                                    $phoneNumber = ($resellerCompany->phone ?? null) ?: (($user && $user->phone_number) ? $user->phone_number : ($defaultCompany->phone ?? ''));
                                    $address = ($resellerCompany->address ?? null) ?: (($user && $user->address) ? $user->address : ($defaultCompany->address ?? ''));
                                } else {
                                    // Reseller not connected - use current approach
                                    $companyName = $shopName ?? ($defaultCompany->name ?? '');
                                    $logoUrl = $hasLogo ? asset('storage/' . $user->logo) : (isset($defaultLogo->mobile) && !$shopName ? asset($defaultLogo->mobile) : null);
                                    $phoneNumber = ($user && $user->phone_number) ? $user->phone_number : ($defaultCompany->phone ?? '');
                                    $address = ($user && $user->address) ? $user->address : ($defaultCompany->address ?? '');
                                }

                                // For resellers_invoice true, sender info is same as header info
                                $senderName = $companyName;
                                $senderPhone = $phoneNumber;
                                $senderAddress = $address;
                            }
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
                        <div class="col-sm-12 print-edit-buttons text-center mt-3">
                            <button class="btn btn btn-primary mr-2" type="button" onclick="myFunction()">Print</button>
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-success">Edit</a>
                        </div>
                        <!-- End Invoice-->
                        <!-- End Invoice Holder-->
                    </div>
                </div>
                <div class="card-footer d-print-none">
                    <h5 class="text-center">Other Orders</h5>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Product</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a target="_blank" href="{{ route('admin.orders.show', $order) }}">{{ $order->id }}</a>
                                </td>
                                <td>{{ $order->created_at->format('d-M-Y') }}</td>
                                <td>{{ $order->status }}</td>
                                <td>
                                    @foreach ($order->products as $product)
                                        <div>{{ $product->quantity }} x {{ $product->name }}</div>
                                    @endforeach
                                </td>
                                <td>{{ $order->note }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/print.js')}}"></script>
@endpush
