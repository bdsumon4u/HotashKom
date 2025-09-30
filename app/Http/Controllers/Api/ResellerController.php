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
        $isPendingView = $request->has('status') && $request->status === 'pending';

        // Only load orders count if not pending view (to avoid unnecessary query
        if (! $isPendingView) {
            $resellers = User::withCount('orders');
        } else {
            $resellers = User::query();
        }

        // Filter by status if provided
        if ($isPendingView) {
            $resellers = $resellers->where('is_verified', false);
        } else {
            $resellers = $resellers->where('is_verified', true);
        }

        $dataTable = DataTables::of($resellers)
            ->addIndexColumn()
            ->editColumn('id', fn ($row): string => $row->id)
            ->editColumn('name', fn ($row): string => '<a href="'.route('admin.orders.index', ['user_id' => $row->id, 'status' => '']).'">'.$row->name.'</a>')
            ->editColumn('shop_name', fn ($row): string => $row->shop_name ?? '-')
            ->editColumn('phone_number', fn ($row): string => $row->phone_number ?? '-')
            ->editColumn('bkash_number', fn ($row): string => $row->bkash_number ?? '-');

        // Add conditional columns for non-pending view
        if (! $isPendingView) {
            $dataTable = $dataTable
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
                ->editColumn('is_verified', fn ($row): string => $row->is_verified ? 'Yes' : 'No');
        }

        $dataTable = $dataTable->addColumn('actions', function ($row) use ($isPendingView) {
            $actions = '<div class="btn-group">';
            $actions .= '<a href="'.route('admin.resellers.edit', $row->id).'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>';

            if (! $isPendingView) {
                $actions .= '<button type="button" class="btn btn-sm'.($row->is_verified ? ' btn-danger' : ' btn-success').' toggle-verify" data-id="'.$row->id.'" data-verified="'.$row->is_verified.'"><i class="fa'.($row->is_verified ? ' fa-times' : ' fa-check').'"></i></button>';
            } else {
                // For pending resellers, show verify and delete
                $actions .= '<button type="button" class="btn btn-sm btn-success toggle-verify" data-id="'.$row->id.'" data-verified="0"><i class="fa fa-check"></i></button>';
                $actions .= '<button type="button" class="btn btn-sm btn-danger delete-reseller" data-id="'.$row->id.'"><i class="fa fa-trash"></i></button>';
            }

            $actions .= '</div>';

            return $actions;
        });

        $dataTable = $dataTable
            ->filterColumn('id', function ($query, $keyword): void {
                $query->whereRaw('CAST(users.id AS CHAR) REGEXP ?', [$keyword]);
            })
            ->filterColumn('name', function ($query, $keyword): void {
                $query->where('users.name', 'like', '%'.$keyword.'%')
                    ->orWhere('users.email', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('shop_name', function ($query, $keyword): void {
                $query->where('users.shop_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('phone_number', function ($query, $keyword): void {
                $query->where('users.phone_number', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('bkash_number', function ($query, $keyword): void {
                $query->where('users.bkash_number', 'like', '%'.$keyword.'%');
            });

        // Add conditional sorting for non-pending view
        if (! $isPendingView) {
            $dataTable = $dataTable->orderColumn('balance', function ($query, $order) {
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
            });
        }

        // Set raw columns based on view type
        if ($isPendingView) {
            $dataTable = $dataTable->rawColumns(['name', 'actions']);
        } else {
            $dataTable = $dataTable->rawColumns(['name', 'balance', 'actions']);
        }

        return $dataTable->make(true);
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

    /**
     * Remove the specified reseller from storage.
     */
    public function destroy($id)
    {
        $reseller = User::findOrFail($id);

        if ($reseller->is_verified) {
            return response()->json([
                'message' => 'Only unverified resellers can be deleted.',
            ], 422);
        }

        // Optionally, ensure no dependent data should block deletion here
        // e.g., if ($reseller->orders()->exists()) { ... }

        $reseller->delete();

        return response()->json([
            'message' => 'Reseller deleted successfully',
        ]);
    }
}
