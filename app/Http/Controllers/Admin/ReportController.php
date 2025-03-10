<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.reports.index', [
            'reports' => Report::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (request()->has('code')) {
            $code = ltrim(str_replace('-', '', request('code')), '0');
            if ($order = Order::find($code)) {
                return $order;
            }

            return null;
        }

        return view('admin.reports.scanning');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'codes' => 'required',
            'orders' => 'required',
            'products' => 'required',
            'courier' => 'required',
            'status' => 'required',
            'total' => 'required',
        ]);

        Report::create($data);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        $codes = explode(',', $report->codes);
        $codes = array_map('trim', $codes);
        $codes = array_filter($codes);

        return view('admin.reports.scanning', [
            'orders' => Order::whereIn('id', $codes)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        $data = $request->validate([
            'codes' => 'required',
            'orders' => 'required',
            'products' => 'required',
            'courier' => 'required',
            'status' => 'required',
            'total' => 'required',
        ]);

        $report->update($data);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function stock(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return view('admin.reports.stock', [
            'products' => Product::with('parent')->whereShouldTrack(true)->orderBy('stock_count')->get(),
        ]);
    }

    public function customer(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $_start = Carbon::parse(\request('start_d', date('Y-m-d')));
        $start = $_start->format('Y-m-d');
        $_end = Carbon::parse(\request('end_d'));
        $end = $_end->format('Y-m-d');
        $type = $request->get('date_type', 'status_at');
        $top = $request->get('top_by', 'order_amount');

        $query = User::withWhereHas('orders', function ($query) use ($type, $_start, $_end): void {
            $query->where('status', 'COMPLETED')
                ->whereBetween($type, [
                    $_start->startOfDay()->toDateTimeString(),
                    $_end->endOfDay()->toDateTimeString(),
                ]);
        });

        $users = $query->get()->map(function ($user) {
            $user->order_amount = $user->orders->sum('data.subtotal')
                + $user->orders->sum('data.shipping_cost')
                - $user->orders->sum('data.discount');
            $user->order_count = $user->orders->count();

            return $user;
        })->sortByDesc($top);

        return view('admin.reports.customer', [
            'users' => $users,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
