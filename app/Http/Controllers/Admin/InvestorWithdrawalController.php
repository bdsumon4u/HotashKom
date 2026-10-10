<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvestorWithdrawalController extends Controller
{
    /**
     * Display a listing of investor withdrawal requests.
     */
    public function index(): View
    {
        return view('admin.investor-withdrawals.index');
    }

    /**
     * Get investor withdrawal requests data for DataTables.
     */
    public function data(): JsonResponse
    {
        $investorMorph = (new Investor)->getMorphClass();

        $transactions = Transaction::with('payable')
            ->where('type', 'withdraw')
            ->where('payable_type', $investorMorph)
            ->latest();

        return DataTables::of($transactions)
            ->filter(function ($query): void {
                $search = strtolower((string) request('search.value', ''));
                if ($search === '') {
                    return;
                }

                $query->where(function ($q) use ($search): void {
                    $q->whereRaw('LOWER(CAST(`transactions`.`id` AS CHAR)) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(CAST(`transactions`.`amount` AS CHAR)) LIKE ?', ["%{$search}%"])
                        ->orWhereHas('payable', function ($uq) use ($search): void {
                            $uq->whereRaw('LOWER(`name`) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(`email`) LIKE ?', ["%{$search}%"])
                                ->orWhereRaw('LOWER(`phone_number`) LIKE ?', ["%{$search}%"]);
                        });
                });
            }, true)
            ->addIndexColumn()
            ->editColumn('id', fn ($row): string => '#'.$row->id)
            ->editColumn('investor', function ($row): string {
                /** @var Investor|null $investor */
                $investor = $row->payable;
                if (! $investor) {
                    return 'N/A';
                }

                return '<div class="text-nowrap" style="white-space: nowrap;">
                    <a href="'.route('admin.investment.investors.show', $investor->id).'" class="font-weight-bold text-nowrap d-inline-block">'.e($investor->name).'</a>
                    '.($investor->phone_number ? '<br><small class="text-muted text-nowrap"><i class="fa fa-phone"></i> '.e($investor->phone_number).'</small>' : '').'
                </div>';
            })
            ->addColumn('account_details', function ($row): string {
                $meta = $row->meta ?? [];
                $method = strtoupper($meta['payment_method'] ?? 'bKash');
                $acc = $meta['account_number'] ?? ($row->payable?->bkash_number ?? 'N/A');

                return '<span class="badge badge-secondary">'.e($method).'</span> <span class="font-weight-bold">'.e((string) $acc).'</span>';
            })
            ->editColumn('amount', fn ($row): string => '<span class="font-weight-bold text-danger">-'.number_format(abs((float) $row->amount), 2).' TK</span>')
            ->addColumn('balance', function ($row): string {
                $investor = $row->payable;

                return $investor ? '<span class="font-weight-bold text-info">'.number_format((float) $investor->balance, 2).' TK</span>' : 'N/A';
            })
            ->editColumn('created_at', fn ($row): string => $row->created_at->format('M d, Y H:i'))
            ->editColumn('status', function ($row): string {
                if ($row->confirmed) {
                    $trxId = $row->meta['trx_id'] ?? null;

                    return '<span class="badge badge-success">Confirmed</span>'.($trxId ? '<br><small class="text-muted">Trx: '.e((string) $trxId).'</small>' : '');
                }

                return '<span class="badge badge-warning">Pending</span>';
            })
            ->addColumn('actions', function ($row): string {
                $investor = $row->payable;
                if (! $investor) {
                    return 'N/A';
                }

                if ($row->confirmed) {
                    return '<span class="text-muted"><i class="fa fa-check-circle text-success"></i> Completed</span>';
                }

                return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-primary confirm-withdraw"
                            data-id="'.$row->id.'"
                            data-investor-id="'.$investor->id.'"
                            data-amount="'.abs((float) $row->amount).'">
                         <i class="fa fa-check"></i> Confirm
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-withdraw"
                            data-id="'.$row->id.'"
                            data-investor-id="'.$investor->id.'"
                            data-amount="'.abs((float) $row->amount).'">
                        <i class="fa fa-times"></i> Delete
                    </button>
                </div>';
            })
            ->rawColumns(['id', 'investor', 'account_details', 'amount', 'balance', 'status', 'actions'])
            ->make(true);
    }

    /**
     * Confirm an investor withdrawal request.
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => ['required', 'integer'],
            'investor_id' => ['required', 'integer'],
            'trx_id' => ['required', 'string', 'max:255'],
        ]);

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Withdrawal request not found or already confirmed.'], 404);
        }

        $investor = Investor::find($request->investor_id);
        if (! $investor) {
            return response()->json(['message' => 'Investor not found.'], 404);
        }

        $meta = $transaction->meta ?? [];
        $meta['trx_id'] = $request->trx_id;
        $meta['admin_id'] = auth('admin')->id();
        $meta['status'] = 'confirmed';
        $transaction->meta = $meta;
        $transaction->save();

        $investor->confirm($transaction);

        cacheMemo()->forget('investor_pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal confirmed successfully.']);
    }

    /**
     * Delete / Reject an unconfirmed withdrawal request.
     */
    public function deleteRequest(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => ['required', 'integer'],
            'investor_id' => ['required', 'integer'],
        ]);

        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('type', 'withdraw')
            ->where('confirmed', false)
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Pending withdrawal request not found.'], 404);
        }

        $investor = Investor::find($request->investor_id);
        if (! $investor) {
            return response()->json(['message' => 'Investor not found.'], 404);
        }

        // Deleting unconfirmed transaction automatically restores the balance
        $transaction->delete();

        cacheMemo()->forget('investor_pending_withdrawal_amount');

        return response()->json(['message' => 'Withdrawal request deleted and funds restored.']);
    }
}
