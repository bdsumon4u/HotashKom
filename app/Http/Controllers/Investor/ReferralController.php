<?php

declare(strict_types=1);

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\View\View;

class ReferralController extends Controller
{
    /**
     * Display referral statistics and list of referred investors.
     */
    public function index(): View
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        $referralCode = $investor->referral_code;
        $referralLink = $investor->getReferralLink();

        $referrals = $investor->referrals()
            ->with(['investments'])
            ->latest()
            ->paginate(15);

        $totalReferrals = $investor->referrals()->count();
        $totalBonusEarned = $investor->getTotalReferralBonusEarned();

        $initialRate = (float) config('investment.initial_referrer_bonus_rate', 5.0);
        $monthlyRate = (float) config('investment.monthly_referrer_bonus_rate', 5.0);

        return view('investor.referrals', compact(
            'investor',
            'referralCode',
            'referralLink',
            'referrals',
            'totalReferrals',
            'totalBonusEarned',
            'initialRate',
            'monthlyRate'
        ));
    }
}
