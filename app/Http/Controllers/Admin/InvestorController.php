<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Services\InvestmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvestorController extends Controller
{
    /**
     * Display a listing of investors.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $query = Investor::with(['referrer', 'investments'])->latest();

            return DataTables::of($query)
                ->filter(function ($query): void {
                    $search = strtolower((string) request('search.value', ''));
                    if ($search === '') {
                        return;
                    }

                    $query->where(function ($q) use ($search): void {
                        $q->whereRaw('LOWER(`name`) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(`email`) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(`phone_number`) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(`referral_code`) LIKE ?', ["%{$search}%"]);
                    });
                }, true)
                ->addIndexColumn()
                ->editColumn('name', function ($row): string {
                    return '<div class="text-nowrap" style="white-space: nowrap;">
                        <a href="'.route('admin.investment.investors.show', $row->id).'" class="font-weight-bold text-primary text-nowrap d-inline-block">'.e($row->name).'</a>
                        '.($row->phone_number ? '<br><small class="text-muted text-nowrap"><i class="fa fa-phone"></i> '.e($row->phone_number).'</small>' : '').'
                    </div>';
                })
                ->addColumn('referral_code', function ($row): string {
                    return '<span class="badge badge-info">'.e($row->referral_code).'</span>';
                })
                ->addColumn('referred_by', function ($row): string {
                    if (! $row->referrer) {
                        return '<span class="text-muted">None</span>';
                    }

                    return '<a href="'.route('admin.investment.investors.show', $row->referrer->id).'">'.e($row->referrer->name).'</a>';
                })
                ->addColumn('total_invested', function ($row): string {
                    return '<span class="font-weight-bold">'.number_format($row->getTotalInvestedAmount(), 2).' TK</span>';
                })
                ->addColumn('total_returned', function ($row): string {
                    return '<span class="text-success font-weight-bold">'.number_format($row->getTotalReceivedReturnAmount(), 2).' TK</span>';
                })
                ->addColumn('balance', function ($row): string {
                    $available = $row->getAvailableBalance();
                    $pending = $row->getPendingWithdrawalAmount();

                    $html = '<span class="font-weight-bold text-info">'.number_format($available, 2).' TK</span>';
                    if ($pending > 0) {
                        $html .= '<br><small class="text-warning">Pending: '.number_format($pending, 2).' TK</small>';
                    }

                    return $html;
                })
                ->editColumn('is_active', function ($row): string {
                    return $row->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($row): string {
                    return '<div class="btn-group">
                        <a href="'.route('admin.investment.investors.show', $row->id).'" class="btn btn-sm btn-info" title="View Details">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="'.route('admin.investment.investors.edit', $row->id).'" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['name', 'referral_code', 'referred_by', 'total_invested', 'total_returned', 'balance', 'is_active', 'actions'])
                ->make(true);
        }

        return view('admin.investors.index');
    }

    /**
     * Show the form for creating a new investor.
     */
    public function create(): View
    {
        $investors = Investor::where('is_active', true)->orderBy('name')->get();

        return view('admin.investors.create', compact('investors'));
    }

    /**
     * Store a newly created investor in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:investors,email'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'bkash_number' => ['nullable', 'string', 'max:50'],
            'bank_details' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:6'],
            'referred_by_id' => ['nullable', 'exists:investors,id'],
            'is_active' => ['boolean'],
        ]);

        $investor = Investor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'bkash_number' => $request->bkash_number,
            'bank_details' => $request->bank_details,
            'address' => $request->address,
            'password' => $request->password,
            'referred_by_id' => $request->referred_by_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.investment.investors.show', $investor->id)
            ->with('success', 'Investor registered successfully. You can now record their investment.');
    }

    /**
     * Display the specified investor details.
     */
    public function show(Investor $investor): View
    {
        $investor->load(['referrer', 'referrals', 'investments.installments']);

        $totalInvested = $investor->getTotalInvestedAmount();
        $totalExpected = $investor->getTotalExpectedReturnAmount();
        $totalReceived = $investor->getTotalReceivedReturnAmount();
        $availableBalance = $investor->getAvailableBalance();
        $pendingWithdrawal = $investor->getPendingWithdrawalAmount();
        $totalReferralBonus = $investor->getTotalReferralBonusEarned();

        $transactions = $investor->wallet->transactions()->latest()->take(20)->get();

        return view('admin.investors.show', compact(
            'investor',
            'totalInvested',
            'totalExpected',
            'totalReceived',
            'availableBalance',
            'pendingWithdrawal',
            'totalReferralBonus',
            'transactions'
        ));
    }

    /**
     * Show the form for editing the specified investor.
     */
    public function edit(Investor $investor): View
    {
        $investors = Investor::where('id', '!=', $investor->id)->where('is_active', true)->orderBy('name')->get();

        return view('admin.investors.edit', compact('investor', 'investors'));
    }

    /**
     * Update the specified investor in storage.
     */
    public function update(Request $request, Investor $investor): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:investors,email,'.$investor->id],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'bkash_number' => ['nullable', 'string', 'max:50'],
            'bank_details' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:6'],
            'referred_by_id' => ['nullable', 'exists:investors,id'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'bkash_number' => $request->bkash_number,
            'bank_details' => $request->bank_details,
            'address' => $request->address,
            'referred_by_id' => $request->referred_by_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $investor->update($data);

        return redirect()->route('admin.investment.investors.show', $investor->id)
            ->with('success', 'Investor profile updated successfully.');
    }

    /**
     * Store a new investment for this investor.
     */
    public function storeInvestment(Request $request, Investor $investor, InvestmentService $investmentService): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now();

        $investment = $investmentService->createInvestment(
            investor: $investor,
            amount: (float) $request->amount,
            startDate: $startDate,
            notes: $request->notes
        );

        return redirect()->route('admin.investment.investments.show', $investment->id)
            ->with('success', 'Investment of '.number_format((float) $request->amount, 2).' TK successfully activated with 36 monthly installment schedules!');
    }
}
