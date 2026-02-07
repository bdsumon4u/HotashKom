<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class MaintenancePaymentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $invoice = Cache::get('unpaid_or_overdue_invoice');
        $statusData = $this->resolveInvoiceStatus($invoice);
        $amount = data_get($invoice, 'total');
        $currency = data_get($invoice, 'currency') ?? 'BDT';
        $dueDateRaw = data_get($invoice, 'duedate');

        $dueDate = null;

        if ($dueDateRaw) {
            try {
                $dueDate = Carbon::parse($dueDateRaw)->format('d M Y');
            } catch (\Throwable) {
                $dueDate = $dueDateRaw;
            }
        }

        if (! $request->session()->has('maintenance_return_url')) {
            $previous = url()->previous();
            if ($previous && ! str_contains($previous, route('maintenance.payment'))) {
                $request->session()->put('maintenance_return_url', $previous);
            }
        }

        return view('maintenance.payment', [
            'invoice' => $invoice,
            'statusLabel' => $statusData['status'],
            'isOverdue' => $statusData['isOverdue'],
            'isUnpaid' => ! $statusData['isOverdue'],
            'amount' => $amount,
            'currency' => $currency,
            'dueDate' => $dueDate,
        ]);
    }

    public function pay(Request $request): RedirectResponse
    {
        $invoice = Cache::get('unpaid_or_overdue_invoice');
        $invoiceId = data_get($invoice, 'invoice_id') ?? data_get($invoice, 'id');

        if (! $invoiceId) {
            return redirect()->route('maintenance.payment')->with('error', 'Unable to find the invoice for payment.');
        }

        Cache::forget('ignore_maintenance_due_check');
        Cache::forget('unpaid_or_overdue_invoice');

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::acceptJson()->get('https://hotash.tech/includes/api/generatehotashpayurl.php', [
            'invoiceid' => $invoiceId,
        ]);

        if (! $paymentUrl = $response->json('payment_url')) {
            return redirect()->route('maintenance.payment')->with('error', 'Payment link is unavailable. Please try again.');
        }

        return redirect()->away($paymentUrl);
    }

    public function defer(Request $request): RedirectResponse
    {
        $invoice = Cache::get('unpaid_or_overdue_invoice');
        $statusData = $this->resolveInvoiceStatus($invoice);

        if ($statusData['isOverdue']) {
            return redirect()->route('maintenance.payment');
        }

        Cache::put('ignore_maintenance_due_check', true, now()->addDay());

        $returnUrl = $request->session()->pull('maintenance_return_url');

        return redirect()->to($returnUrl ?? route('/'));
    }

    /**
     * @return array{status: string, isOverdue: bool}
     */
    private function resolveInvoiceStatus(mixed $invoice): array
    {
        $status = strtolower((string) data_get($invoice, 'status', data_get($invoice, 'invoice_status', data_get($invoice, 'state', 'unpaid'))));
        $isOverdue = (bool) data_get($invoice, 'is_overdue', false);

        $dueDateRaw = data_get($invoice, 'duedate');

        if (! $isOverdue && $dueDateRaw) {
            try {
                $isOverdue = Carbon::parse($dueDateRaw)->isPast();
            } catch (\Throwable) {
                $isOverdue = false;
            }
        }

        if (str_contains($status, 'overdue')) {
            $isOverdue = true;
        }

        return [
            'status' => $status ?: 'unpaid',
            'isOverdue' => $isOverdue,
        ];
    }
}
