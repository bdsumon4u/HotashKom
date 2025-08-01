<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ResellerController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $resellers = User::withCount('orders');

        return DataTables::of($resellers)
            ->addIndexColumn()
            ->editColumn('id', fn ($row): string => $row->id)
            ->editColumn('name', fn ($row): string => '<a href="'.route('admin.orders.index', ['user_id' => $row->id, 'status' => '']).'">'.$row->name.'</a>')
            ->editColumn('shop_name', fn ($row): string => $row->shop_name ?? '-')
            ->editColumn('phone_number', fn ($row): string => $row->phone_number ?? '-')
            ->editColumn('bkash_number', fn ($row): string => $row->bkash_number ?? '-')
            ->editColumn('balance', function ($row): string {
                $availableBalance = $row->getAvailableBalance();
                $pendingAmount = $row->getPendingWithdrawalAmount();

                $balanceText = number_format($availableBalance, 2);
                if ($pendingAmount > 0) {
                    $balanceText .= ' <small class="text-warning">(+'.theMoney($pendingAmount).')</small>';
                }

                return '<a href="'.route('admin.transactions.index', $row->id).'" class="text-primary">'.$balanceText.'</a>';
            })
            ->editColumn('orders_count', fn ($row): string => $row->orders_count)
            ->editColumn('is_verified', fn ($row): string => $row->is_verified ? 'Yes' : 'No')
            ->addColumn('actions', function ($row) {
                return '<div class="btn-group">
                    <a href="'.route('admin.resellers.edit', $row->id).'" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm'.($row->is_verified ? ' btn-danger' : ' btn-success').' toggle-verify" data-id="'.$row->id.'" data-verified="'.$row->is_verified.'">
                        <i class="fa'.($row->is_verified ? ' fa-times' : ' fa-check').'"></i>
                    </button>
                </div>';
            })
            ->filterColumn('name', function ($query, $keyword): void {
                $query->where('name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('shop_name', function ($query, $keyword): void {
                $query->where('shop_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('phone_number', function ($query, $keyword): void {
                $query->where('phone_number', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('bkash_number', function ($query, $keyword): void {
                $query->where('bkash_number', 'like', '%'.$keyword.'%');
            })
            ->orderColumn('balance', function ($query, $order) {
                // Prioritize resellers with pending withdrawals first, then sort by balance
                $query->leftJoin('wallets', function ($join) {
                    $join->on('users.id', '=', 'wallets.holder_id')
                        ->where('wallets.slug', 'default');
                })
                    ->orderByRaw('EXISTS(
                    SELECT 1
                    FROM transactions
                    WHERE transactions.payable_id = users.id
                    AND transactions.type = "withdraw"
                    AND transactions.confirmed = 0
                ) DESC')
                    ->orderBy('wallets.balance', $order);
            })
            ->rawColumns(['name', 'balance', 'actions'])
            ->make(true);
    }

    /**
     * Update the specified reseller.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reseller = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'bkash_number' => 'required|string|max:255',
        ]);

        $reseller->update($validated);

        return response()->json(['message' => 'Reseller updated successfully']);
    }

    /**
     * Toggle verification status of the reseller.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleVerify($id)
    {
        $reseller = User::findOrFail($id);
        $reseller->update(['is_verified' => ! $reseller->is_verified]);

        return response()->json([
            'message' => 'Verification status updated successfully',
            'is_verified' => $reseller->is_verified,
        ]);
    }
}
