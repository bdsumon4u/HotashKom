<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = User::findOrFail($request->user_id);
            $transactions = $user->wallet->transactions();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return $row->type === 'deposit' ?
                        '<span class="badge badge-success">Deposit</span>' :
                        '<span class="badge badge-danger">Withdraw</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y, h:i A');
                })
                ->addColumn('meta', function ($row) {
                    $meta = $row->meta;

                    if (isset($meta['trx_id']) && isset($meta['admin_id'])) {
                        return '<span class="text-muted">Trx ID: '.$meta['trx_id'].' by staff #'.$meta['admin_id'].'</span>';
                    }

                    $title = $row->meta['reason'] ?? 'N/A';
                    if ($id = $meta['order_id'] ?? false) {
                        return '<a target="_blank" href="'.route('admin.orders.edit', $id).'">'.$title.'</a>';
                    }

                    return $title;
                })

                ->rawColumns(['type', 'meta'])
                ->make(true);
        }

        $user = User::findOrFail($request->user_id);

        return view('admin.transactions.index', compact('user'));
    }

    /**
     * Handle the withdrawal request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function withdraw(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'trx_id' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);

        if ($request->amount > $user->balance) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }

        $user->wallet->withdraw($request->amount, [
            'trx_id' => $request->trx_id,
            'admin_id' => auth('admin')->id(),
        ]);

        return response()->json(['message' => 'Withdrawal successful']);
    }
}
