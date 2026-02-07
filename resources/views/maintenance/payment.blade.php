@extends('layouts.yellow.master')

@title('Maintenance Payment Due')

@section('content')
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="border-0 shadow-sm card">
                    <div class="p-3 card-body">
                        @if (session('error'))
                            <div class="mb-2 alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (empty($invoice))
                            <div class="mb-2 alert alert-warning">
                                No maintenance invoice was found at this time.
                            </div>
                            <a href="{{ route('/') }}" class="btn btn-primary">Go Home</a>
                        @else
                            <div
                                class="alert {{ $isOverdue ? 'alert-danger' : 'alert-warning' }} d-flex align-items-start mb-2">
                                <div class="mr-2">
                                    <i class="fa {{ $isOverdue ? 'fa-exclamation-triangle' : 'fa-info-circle' }}"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold">
                                        {{ $isOverdue ? 'Maintenance payment is overdue' : 'Maintenance payment is unpaid' }}
                                    </div>
                                    <div class="small">
                                        Access may be limited until payment is completed.
                                    </div>
                                </div>
                                <span
                                    class="badge {{ $isOverdue ? 'badge-danger' : 'badge-warning' }} ml-auto text-uppercase">
                                    {{ $isOverdue ? 'Overdue' : 'Unpaid' }}
                                </span>
                            </div>

                            <div class="p-2 border rounded bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small">Amount Due</div>
                                        <div class="mb-0 h4 text-danger">
                                            @if ($amount !== null)
                                                {{ $currency }} {{ number_format((float) $amount, 2) }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-muted small">Due Date</div>
                                        <div class="font-weight-bold">{{ $dueDate ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 table-responsive">
                                <table class="table mb-0 table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted">Invoice ID</td>
                                        <td class="text-right">
                                            {{ data_get($invoice, 'invoice_id') ?? (data_get($invoice, 'id') ?? (data_get($invoice, 'invoice_number') ?? '—')) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Service</td>
                                        <td class="text-right">
                                            {{ data_get($invoice, 'service_name') ?? (data_get($invoice, 'service') ?? (data_get($invoice, 'product_name') ?? 'Maintenance')) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td class="text-right text-uppercase">
                                            {{ $statusLabel ?: ($isOverdue ? 'overdue' : 'unpaid') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Invoice Date</td>
                                        <td class="text-right">
                                            {{ data_get($invoice, 'invoice_date') ?? (data_get($invoice, 'date') ?? (data_get($invoice, 'created_at') ?? '—')) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mt-3">
                                <form action="{{ route('maintenance.payment.pay') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-lg btn-block">
                                        <i class="fa fa-credit-card"></i> Make Payment Now
                                    </button>
                                </form>

                                @if ($isUnpaid)
                                    <form action="{{ route('maintenance.payment.defer') }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-block">
                                            I'll Pay Later
                                        </button>
                                    </form>
                                    <p class="mt-2 mb-0 small text-muted">
                                        You can continue using the service for now, but please pay before the due date.
                                    </p>
                                @else
                                    <p class="mt-2 mb-0 text-center small text-muted">
                                        Overdue invoices require payment to restore access.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
