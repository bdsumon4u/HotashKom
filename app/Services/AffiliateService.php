<?php

namespace App\Services;

use App\Models\AffiliateCommission;
use App\Models\ResellerAffiliate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateService
{
    /**
     * Generate a unique uppercase referral code.
     */
    public function generateUniqueCode(): string
    {
        do {
            $code = 'HK'.Str::upper(Str::random(6));
        } while (ResellerAffiliate::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Ensure the user has an affiliate profile and referral code.
     */
    public function ensureAffiliateProfile(User $user, ?string $referralCode = null): ResellerAffiliate
    {
        $referralCode = $referralCode ?: (request()?->input('ref') ?: session('ref'));

        $existing = ResellerAffiliate::where('user_id', $user->id)->first();
        if ($existing) {
            if (! empty($referralCode) && ! $existing->referrer_id) {
                $referrerAffiliate = ResellerAffiliate::where('referral_code', trim($referralCode))->first();
                if ($referrerAffiliate && $referrerAffiliate->user_id !== $user->id) {
                    $existing->update(['referrer_id' => $referrerAffiliate->user_id]);
                    $existing->refresh();
                }
            }

            return $existing;
        }

        $referrerId = null;
        if (! empty($referralCode)) {
            $referrerAffiliate = ResellerAffiliate::where('referral_code', trim($referralCode))->first();
            if ($referrerAffiliate && $referrerAffiliate->user_id !== $user->id) {
                $referrerId = $referrerAffiliate->user_id;
            }
        }

        return ResellerAffiliate::create([
            'user_id' => $user->id,
            'referrer_id' => $referrerId,
            'referral_code' => $this->generateUniqueCode(),
            'direct_leg' => null,
            'left_total' => 0,
            'right_total' => 0,
            'direct_left_count' => 0,
            'direct_right_count' => 0,
            'verified_at' => null,
        ]);
    }

    /**
     * Register or link a user's referral at registration time.
     */
    public function registerReferral(User $user, ?string $referralCode): ResellerAffiliate
    {
        return $this->ensureAffiliateProfile($user, $referralCode);
    }

    /**
     * Process verification for a newly verified reseller.
     * Assigns round-robin leg (left/right) and propagates counts up the ancestor tree,
     * awarding milestone matching commissions as tiers are completed.
     */
    public function processVerification(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $affiliate = ResellerAffiliate::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $affiliate) {
                $affiliate = $this->ensureAffiliateProfile($user);
                $affiliate = ResellerAffiliate::where('id', $affiliate->id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            // Guard against duplicate processing
            if ($affiliate->verified_at !== null) {
                return;
            }

            $affiliate->verified_at = now();

            // If the user has no referrer, they are a root node
            if (! $affiliate->referrer_id) {
                $affiliate->save();

                return;
            }

            // Lock direct referrer
            $referrerAffiliate = ResellerAffiliate::where('user_id', $affiliate->referrer_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Count already verified direct referrals under this referrer (excluding self)
            $verifiedDirectsCount = ResellerAffiliate::where('referrer_id', $referrerAffiliate->user_id)
                ->whereNotNull('verified_at')
                ->where('id', '!=', $affiliate->id)
                ->count();

            // Round-Robin Assignment: 0th (1st user) -> 'left', 1st (2nd user) -> 'right', etc.
            $assignedLeg = ($verifiedDirectsCount % 2 === 0) ? 'left' : 'right';

            $affiliate->direct_leg = $assignedLeg;
            $affiliate->save();

            // Increment direct counts on direct referrer
            if ($assignedLeg === 'left') {
                $referrerAffiliate->increment('direct_left_count');
            } else {
                $referrerAffiliate->increment('direct_right_count');
            }

            // Ancestor propagation
            $currentLeg = $assignedLeg;
            $ancestor = $referrerAffiliate;
            $verificationFee = (float) config('app.verification_fee', 1000);
            $tiers = config('affiliate.tiers', []);

            while ($ancestor) {
                if ($currentLeg === 'left') {
                    $ancestor->increment('left_total');
                    $ancestor->refresh();
                    $activeCount = $ancestor->left_total;
                    $counterCount = $ancestor->right_total;
                } else {
                    $ancestor->increment('right_total');
                    $ancestor->refresh();
                    $activeCount = $ancestor->right_total;
                    $counterCount = $ancestor->left_total;
                }

                // Check milestone matching commission condition:
                // Active leg just completed exactly x, and counter leg has >= x
                if (isset($tiers[$activeCount]) && $counterCount >= $activeCount) {
                    $x = $activeCount;
                    $z = (float) $tiers[$x]['percentage'];

                    $alreadyAwarded = AffiliateCommission::where('user_id', $ancestor->user_id)
                        ->where('milestone_x', $x)
                        ->lockForUpdate()
                        ->exists();

                    if (! $alreadyAwarded) {
                        $baseAmount = 2 * $x * $verificationFee;
                        $commissionAmount = ($z / 100) * $baseAmount;

                        AffiliateCommission::create([
                            'user_id' => $ancestor->user_id,
                            'milestone_x' => $x,
                            'percentage' => $z,
                            'base_amount' => $baseAmount,
                            'amount' => $commissionAmount,
                            'trigger_leg' => $currentLeg,
                            'triggered_by_user_id' => $user->id,
                        ]);

                        $ancestorUser = User::find($ancestor->user_id);
                        if ($ancestorUser) {
                            $ancestorUser->deposit($commissionAmount, [
                                'reason' => "Affiliate commission for {$x} pairs on left and right ({$z}%)",
                                'type' => 'affiliate_commission',
                                'milestone_x' => $x,
                                'triggered_by_user_id' => $user->id,
                            ]);
                        }
                    }
                }

                if (! $ancestor->referrer_id) {
                    break;
                }

                $currentLeg = $ancestor->direct_leg;
                if (! $currentLeg) {
                    break;
                }

                $ancestor = ResellerAffiliate::where('user_id', $ancestor->referrer_id)
                    ->lockForUpdate()
                    ->first();
            }
        });
    }
}
