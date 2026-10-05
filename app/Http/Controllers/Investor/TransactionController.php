<?php

declare(strict_types=1);

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions or return datatable JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        if ($request->ajax()) {
            $query = $investor->wallet->transactions()->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('type', function ($row): string {
                    return $row->type === 'deposit'
                        ? '<span class="badge badge-success">Deposit</span>'
                        : '<span class="badge badge-danger">Withdraw</span>';
                })
                ->editColumn('amount', fn ($row): string => '<span class="font-weight-bold '.($row->type === 'deposit' ? 'text-success' : 'text-danger').'">'.($row->type === 'deposit' ? '+' : '-').number_format(abs((float) $row->amount), 2).' TK</span>')
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y, h:i A'))
                ->addColumn('reason', function ($row): string {
                    $reason = $row->meta['reason'] ?? 'N/A';
                    $trxId = $row->meta['trx_id'] ?? null;
                    if ($trxId) {
                        $reason .= ' <br><small class="text-muted">Trx ID: '.e((string) $trxId).'</small>';
                    }

                    return $reason;
                })
                ->addColumn('status', function ($row): string {
                    if ($row->confirmed) {
                        return '<span class="badge badge-success">Confirmed</span>';
                    }

                    return '<span class="badge badge-warning">Pending</span>';
                })
                ->rawColumns(['type', 'amount', 'reason', 'status'])
                ->make(true);
        }

        $availableBalance = $investor->getAvailableBalance();
        $pendingWithdrawal = $investor->getPendingWithdrawalAmount();
        $totalBalance = (float) $investor->balance;

        return view('investor.transactions', compact('investor', 'availableBalance', 'pendingWithdrawal', 'totalBalance'));
    }

    /**
     * Request a withdrawal for the investor.
     */
    public function withdrawRequest(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'payment_method' => ['required', 'string', 'in:bkash,nagad,rocket,bank'],
            'account_number' => ['required', 'string', 'max:255'],
        ]);

        /** @var Investor $investor */
        $investor = auth('investor')->user();
        $availableBalance = $investor->getAvailableBalance();
        $amount = (float) $request->amount;

        if ($amount > $availableBalance) {
            $pendingAmount = $investor->getPendingWithdrawalAmount();
            $message = 'Insufficient available balance. ';
            if ($pendingAmount > 0) {
                $message .= 'You have '.number_format($pendingAmount, 2).' TK in pending withdrawals.';
            }

            return response()->json(['message' => $message], 422);
        }

        // Create withdraw request with pending status
        $investor->wallet->withdraw((string) $request->amount, [
            'reason' => 'Investor Withdraw Request ('.strtoupper($request->payment_method).': '.$request->account_number.')',
            'payment_method' => $request->payment_method,
            'account_number' => $request->account_number,
            'status' => 'pending',
        ], false); // unconfirmed

        cacheMemo()->forget('investor_pending_withdrawal_amount');

        return response()->json([
            'message' => 'Withdrawal request of '.number_format($amount, 2).' TK submitted successfully!',
        ]);
    }
}
