<?php

declare(strict_types=1);

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\InvestmentInstallment;
use App\Models\Investor;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the investor dashboard.
     */
    public function index(): View
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        $totalInvested = $investor->getTotalInvestedAmount();
        $totalExpectedReturn = $investor->getTotalExpectedReturnAmount();
        $totalReceivedReturn = $investor->getTotalReceivedReturnAmount();
        $availableBalance = $investor->getAvailableBalance();
        $pendingWithdrawals = $investor->getPendingWithdrawalAmount();
        $totalReferralBonus = $investor->getTotalReferralBonusEarned();
        $referralsCount = $investor->referrals()->count();

        $investments = $investor->investments()
            ->withCount(['installments as paid_installments_count' => function ($q): void {
                $q->where('status', 'processed');
            }])
            ->latest()
            ->take(5)
            ->get();

        $upcomingInstallments = InvestmentInstallment::where('investor_id', $investor->id)
            ->where('status', 'pending')
            ->with('investment')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentTransactions = $investor->wallet->transactions()
            ->latest()
            ->take(5)
            ->get();

        return view('investor.dashboard', compact(
            'investor',
            'totalInvested',
            'totalExpectedReturn',
            'totalReceivedReturn',
            'availableBalance',
            'pendingWithdrawals',
            'totalReferralBonus',
            'referralsCount',
            'investments',
            'upcomingInstallments',
            'recentTransactions'
        ));
    }
}
