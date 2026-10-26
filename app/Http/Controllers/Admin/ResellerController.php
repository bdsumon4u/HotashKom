<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\ResellerAffiliate;
use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resellers.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        return view('admin.resellers.index', compact('status'));
    }

    /**
     * Display the affiliate network for the specified reseller.
     */
    public function affiliate(User $reseller, Request $request, AffiliateService $affiliateService)
    {
        $user = $reseller;
        $affiliate = $user->affiliate ?? $affiliateService->ensureAffiliateProfile($user);
        $referralLink = route('user.register', ['ref' => $affiliate->referral_code]);

        $query = ResellerAffiliate::with('user')
            ->where('referrer_id', $user->id)
            ->latest('id');

        if ($request->filled('leg') && in_array($request->leg, ['left', 'right'])) {
            $query->where('direct_leg', $request->leg);
        }

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('verified_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('verified_at');
            }
        }

        $directReferrals = $query->paginate(15)->withQueryString();

        $directTotal = ResellerAffiliate::where('referrer_id', $user->id)->count();
        $directVerified = ResellerAffiliate::where('referrer_id', $user->id)->whereNotNull('verified_at')->count();
        $directPending = ResellerAffiliate::where('referrer_id', $user->id)->whereNull('verified_at')->count();

        $commissions = AffiliateCommission::with('triggeredBy')
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();
        $totalCommissionEarned = $commissions->sum('amount');

        $tiersConfig = config('affiliate.tiers', []);
        $pairsCount = min($affiliate->left_total, $affiliate->right_total);
        $verificationFee = (float) config('app.verification_fee', 1000);

        $tiersProgress = [];
        foreach ($tiersConfig as $x => $tier) {
            $awarded = $commissions->firstWhere('milestone_x', $x);
            $baseAmount = 2 * $x * $verificationFee;
            $potentialCommission = ($tier['percentage'] / 100) * $baseAmount;

            $tiersProgress[] = [
                'milestone_x' => $x,
                'percentage' => $tier['percentage'],
                'base_amount' => $baseAmount,
                'commission' => $potentialCommission,
                'is_awarded' => ! is_null($awarded),
                'awarded_at' => $awarded?->created_at,
                'awarded_amount' => $awarded?->amount,
                'progress_percent' => min(100, round(($pairsCount / $x) * 100)),
            ];
        }

        return view('admin.resellers.affiliate', compact(
            'user',
            'reseller',
            'affiliate',
            'referralLink',
            'directReferrals',
            'directTotal',
            'directVerified',
            'directPending',
            'commissions',
            'totalCommissionEarned',
            'tiersProgress',
            'pairsCount'
        ));
    }

    /**
     * Show the form for editing the specified reseller.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $reseller = User::findOrFail($id);

        return view('admin.resellers.edit', compact('reseller'));
    }

    /**
     * Update the specified reseller in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, User $reseller)
    {
        $validated = $request->mergeIfMissing([
            'is_verified' => 0,
            'is_active' => 0,
        ])->validate(array_merge([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$reseller->id,
            'phone_number' => 'required|string|max:255',
            'bkash_number' => 'required|string|max:255',
            'is_verified' => 'boolean',
            'new_password' => 'nullable|string|min:8',
        ], config('app.demo') ? [] : [
            'is_active' => 'boolean',
            'order_prefix' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'db_host' => 'nullable|string|max:255',
            'db_name' => 'nullable|string|max:255',
            'db_username' => 'nullable|string|max:255',
            'db_password' => 'nullable|string|min:6',
        ]));

        // Handle password reset
        if (! empty($validated['new_password'])) {
            $validated['password'] = Hash::make($validated['new_password']);
            unset($validated['new_password']);
        }

        // Only update database password if provided
        if (empty($validated['db_password'])) {
            unset($validated['db_password']);
        }

        $reseller->update($validated);

        return back()->with('success', 'Reseller updated successfully');
    }
}
