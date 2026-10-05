<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentEarning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvestmentEarningController extends Controller
{
    /**
     * Display a listing of company investment earnings.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $query = InvestmentEarning::with(['investor', 'investment'])->latest();

            return DataTables::of($query)
                ->filter(function ($query): void {
                    $search = strtolower((string) request('search.value', ''));
                    if ($search === '') {
                        return;
                    }

                    $query->where(function ($q) use ($search): void {
                        $q->where('type', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhereHas('investor', function ($uq) use ($search): void {
                                $uq->whereRaw('LOWER(`name`) LIKE ?', ["%{$search}%"]);
                            });
                    });
                }, true)
                ->addIndexColumn()
                ->editColumn('id', fn ($row): string => '#'.$row->id)
                ->editColumn('type', function ($row): string {
                    return $row->type === 'initial_deduction'
                        ? '<span class="badge badge-primary">Initial Fee (Company)</span>'
                        : '<span class="badge badge-info">Monthly Fee (Company)</span>';
                })
                ->editColumn('investor', function ($row): string {
                    if (! $row->investor) {
                        return 'N/A';
                    }

                    return '<a href="'.route('admin.investment.investors.show', $row->investor->id).'" class="font-weight-bold">'.e($row->investor->name).'</a>';
                })
                ->editColumn('investment', function ($row): string {
                    if (! $row->investment) {
                        return 'N/A';
                    }

                    return '<a href="'.route('admin.investment.investments.show', $row->investment->id).'">Investment #'.$row->investment->id.'</a>';
                })
                ->editColumn('amount', fn ($row): string => '<span class="font-weight-bold text-success">+'.number_format($row->amount, 2).' TK</span>')
                ->editColumn('description', fn ($row): string => e($row->description ?? 'N/A'))
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d M Y, h:i A'))
                ->rawColumns(['id', 'type', 'investor', 'investment', 'amount'])
                ->make(true);
        }

        $totalEarnings = InvestmentEarning::sum('amount');
        $initialEarnings = InvestmentEarning::where('type', 'initial_deduction')->sum('amount');
        $monthlyEarnings = InvestmentEarning::where('type', 'monthly_deduction')->sum('amount');

        return view('admin.investment-earnings.index', compact('totalEarnings', 'initialEarnings', 'monthlyEarnings'));
    }
}
