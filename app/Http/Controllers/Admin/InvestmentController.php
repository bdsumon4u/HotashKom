<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Services\InvestmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvestmentController extends Controller
{
    /**
     * Display a listing of investments.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $query = Investment::with(['investor.referrer'])->latest();

            return DataTables::of($query)
                ->filter(function ($query): void {
                    $search = strtolower((string) request('search.value', ''));
                    if ($search === '') {
                        return;
                    }

                    $query->where(function ($q) use ($search): void {
                        $q->whereRaw('LOWER(CAST(`investments`.`id` AS CHAR)) LIKE ?', ["%{$search}%"])
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('investor', function ($uq) use ($search): void {
                                $uq->whereRaw('LOWER(`name`) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(`email`) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(`phone_number`) LIKE ?', ["%{$search}%"]);
                            });
                    });
                }, true)
                ->addIndexColumn()
                ->editColumn('id', fn ($row): string => '#'.$row->id)
                ->editColumn('investor', function ($row): string {
                    if (! $row->investor) {
                        return 'N/A';
                    }

                    return '<a href="'.route('admin.investment.investors.show', $row->investor->id).'" class="font-weight-bold">'.e($row->investor->name).'</a>
                        <br><small class="text-muted">'.e($row->investor->phone_number ?? $row->investor->email).'</small>';
                })
                ->addColumn('invested_amount', fn ($row): string => '<span class="font-weight-bold">'.number_format($row->invested_amount, 2).' TK</span>')
                ->addColumn('total_return', fn ($row): string => '<span class="font-weight-bold text-success">'.number_format($row->total_return_amount, 2).' TK</span>')
                ->addColumn('progress', function ($row): string {
                    $percent = $row->getProgressPercentage();

                    return '<div class="progress" style="height: 16px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: '.$percent.'%;" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100">
                            '.$row->installments_paid_count.'/'.$row->duration_months.'
                        </div>
                    </div>';
                })
                ->editColumn('start_date', fn ($row) => $row->start_date->format('d M Y'))
                ->editColumn('status', function ($row): string {
                    return match ($row->status) {
                        'active' => '<span class="badge badge-success">Active</span>',
                        'completed' => '<span class="badge badge-primary">Completed</span>',
                        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                        default => '<span class="badge badge-secondary">'.e($row->status).'</span>',
                    };
                })
                ->addColumn('actions', function ($row): string {
                    return '<a href="'.route('admin.investment.investments.show', $row->id).'" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i> Details
                    </a>';
                })
                ->rawColumns(['id', 'investor', 'invested_amount', 'total_return', 'progress', 'status', 'actions'])
                ->make(true);
        }

        $totalActive = Investment::where('status', 'active')->count();
        $totalInvested = Investment::where('status', '!=', 'cancelled')->sum('invested_amount');
        $totalReturned = Investment::where('status', '!=', 'cancelled')->sum('total_paid_amount');

        return view('admin.investments.index', compact('totalActive', 'totalInvested', 'totalReturned'));
    }

    /**
     * Display the specified investment.
     */
    public function show(Investment $investment): View
    {
        $investment->load(['investor.referrer', 'installments' => function ($q): void {
            $q->orderBy('installment_number');
        }, 'earnings']);

        return view('admin.investments.show', compact('investment'));
    }

    /**
     * Manually trigger processing of all due installments.
     */
    public function processDueInstallments(InvestmentService $investmentService): RedirectResponse
    {
        $processed = $investmentService->processDueInstallments();

        return back()->with('success', 'Processed '.$processed.' due monthly installment(s) successfully.');
    }
}
