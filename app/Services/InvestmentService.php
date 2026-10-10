<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentEarning;
use App\Models\InvestmentInstallment;
use App\Models\Investor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvestmentService
{
    /**
     * Create and activate a new investment for an investor.
     */
    public function createInvestment(
        Investor $investor,
        float $amount,
        ?Carbon $startDate = null,
        ?string $notes = null
    ): Investment {
        $startDate = $startDate ? $startDate->copy()->startOfDay() : now()->startOfDay();

        $durationMonths = (int) config('investment.duration_months', 36);
        $multiplier = (float) config('investment.multiplier', 2.0);
        $initialDeductionRate = (float) config('investment.initial_deduction_rate', 0.0);
        $initialReferrerRate = (float) config('investment.initial_referrer_bonus_rate', 0.0);
        $monthlyDeductionRate = (float) config('investment.monthly_deduction_rate', 10.0);
        $monthlyReferrerRate = (float) config('investment.monthly_referrer_bonus_rate', 5.0);

        $totalReturnAmount = round($amount * $multiplier, 2);
        $monthlyInstallmentAmount = round($totalReturnAmount / $durationMonths, 2);

        // Initial deduction calculation
        $initialDeductionAmount = round(($amount * $initialDeductionRate) / 100, 2);
        $hasReferrer = ! empty($investor->referred_by_id) && $investor->referrer !== null;

        if ($hasReferrer) {
            $initialReferrerBonusAmount = round(($amount * $initialReferrerRate) / 100, 2);
            $initialCompanyAmount = max(0.0, round($initialDeductionAmount - $initialReferrerBonusAmount, 2));
        } else {
            $initialReferrerBonusAmount = 0.0;
            $initialCompanyAmount = $initialDeductionAmount;
        }

        return DB::transaction(function () use (
            $investor,
            $amount,
            $multiplier,
            $totalReturnAmount,
            $durationMonths,
            $monthlyInstallmentAmount,
            $initialDeductionRate,
            $initialReferrerRate,
            $monthlyDeductionRate,
            $monthlyReferrerRate,
            $initialDeductionAmount,
            $initialReferrerBonusAmount,
            $initialCompanyAmount,
            $startDate,
            $notes,
            $hasReferrer
        ): Investment {
            $investment = Investment::create([
                'tenant_id' => $investor->tenant_id,
                'investor_id' => $investor->id,
                'invested_amount' => $amount,
                'multiplier' => $multiplier,
                'total_return_amount' => $totalReturnAmount,
                'duration_months' => $durationMonths,
                'monthly_installment_amount' => $monthlyInstallmentAmount,
                'initial_deduction_rate' => $initialDeductionRate,
                'initial_referrer_bonus_rate' => $initialReferrerRate,
                'monthly_deduction_rate' => $monthlyDeductionRate,
                'monthly_referrer_bonus_rate' => $monthlyReferrerRate,
                'initial_deduction_amount' => $initialDeductionAmount,
                'initial_referrer_bonus_amount' => $initialReferrerBonusAmount,
                'initial_company_amount' => $initialCompanyAmount,
                'start_date' => $startDate->toDateString(),
                'installments_paid_count' => 0,
                'total_paid_amount' => 0.00,
                'status' => 'active',
                'notes' => $notes,
            ]);

            // 1. Process initial referrer bonus if applicable
            if ($hasReferrer && $initialReferrerBonusAmount > 0) {
                $investor->referrer->deposit((string) $initialReferrerBonusAmount, [
                    'reason' => 'Direct Referral Initial Bonus from '.$investor->name.' (Investment #'.$investment->id.')',
                    'type' => 'referral_bonus',
                    'investment_id' => $investment->id,
                    'investor_id' => $investor->id,
                ]);
            }

            // 2. Process initial company earning
            if ($initialCompanyAmount > 0) {
                InvestmentEarning::create([
                    'tenant_id' => $investor->tenant_id,
                    'investment_id' => $investment->id,
                    'investor_id' => $investor->id,
                    'type' => 'initial_deduction',
                    'amount' => $initialCompanyAmount,
                    'description' => 'Initial investment deduction from '.$investor->name.' (Investment #'.$investment->id.')',
                ]);
            }

            // 3. Pre-generate all scheduled installments
            $runningTotalGross = 0.0;
            for ($month = 1; $month <= $durationMonths; $month++) {
                $dueDate = $startDate->copy()->addMonthsNoOverflow($month);

                $grossAmount = ($month === $durationMonths)
                    ? round($totalReturnAmount - $runningTotalGross, 2)
                    : $monthlyInstallmentAmount;

                $runningTotalGross += $grossAmount;

                $deductionAmount = round(($grossAmount * $monthlyDeductionRate) / 100, 2);

                if ($hasReferrer) {
                    $referrerBonusAmount = round(($grossAmount * $monthlyReferrerRate) / 100, 2);
                    $companyAmount = max(0.0, round($deductionAmount - $referrerBonusAmount, 2));
                } else {
                    $referrerBonusAmount = 0.0;
                    $companyAmount = $deductionAmount;
                }

                $netInvestorAmount = round($grossAmount - $deductionAmount, 2);

                InvestmentInstallment::create([
                    'tenant_id' => $investor->tenant_id,
                    'investment_id' => $investment->id,
                    'investor_id' => $investor->id,
                    'installment_number' => $month,
                    'due_date' => $dueDate->toDateString(),
                    'gross_amount' => $grossAmount,
                    'deduction_amount' => $deductionAmount,
                    'referrer_bonus_amount' => $referrerBonusAmount,
                    'company_amount' => $companyAmount,
                    'net_investor_amount' => $netInvestorAmount,
                    'status' => 'pending',
                ]);
            }

            return $investment;
        });
    }

    /**
     * Process all installments due on or before given date.
     */
    public function processDueInstallments(?Carbon $asOfDate = null): int
    {
        $date = $asOfDate ? $asOfDate->copy()->endOfDay() : now()->endOfDay();

        $dueInstallments = InvestmentInstallment::query()
            ->where('status', 'pending')
            ->where('due_date', '<=', $date->toDateString())
            ->whereHas('investment', fn ($q) => $q->where('status', 'active'))
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();

        $processedCount = 0;

        foreach ($dueInstallments as $installment) {
            $this->processSingleInstallment($installment);
            $processedCount++;
        }

        return $processedCount;
    }

    /**
     * Process a single installment with strict locking & atomicity.
     */
    public function processSingleInstallment(InvestmentInstallment $installment): void
    {
        DB::transaction(function () use ($installment): void {
            // Lock installment and investment
            /** @var InvestmentInstallment $lockedInstallment */
            $lockedInstallment = InvestmentInstallment::where('id', $installment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedInstallment->status !== 'pending') {
                return;
            }

            /** @var Investment $investment */
            $investment = Investment::where('id', $lockedInstallment->investment_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($investment->status !== 'active') {
                return;
            }

            $investor = Investor::with('referrer')->findOrFail($lockedInstallment->investor_id);

            // 1. Credit Net Amount to Investor
            $investor->deposit((string) $lockedInstallment->net_investor_amount, [
                'reason' => 'Monthly Return Installment #'.$lockedInstallment->installment_number.' for Investment #'.$investment->id,
                'type' => 'monthly_return',
                'investment_id' => $investment->id,
                'installment_id' => $lockedInstallment->id,
            ]);

            // 2. Credit Referrer Bonus if applicable
            if ($lockedInstallment->referrer_bonus_amount > 0 && $investor->referrer) {
                $investor->referrer->deposit((string) $lockedInstallment->referrer_bonus_amount, [
                    'reason' => 'Monthly Referral Bonus #'.$lockedInstallment->installment_number.' from '.$investor->name.' (Investment #'.$investment->id.')',
                    'type' => 'referral_bonus',
                    'investment_id' => $investment->id,
                    'installment_id' => $lockedInstallment->id,
                ]);
            }

            // 3. Record Company Earning
            if ($lockedInstallment->company_amount > 0) {
                InvestmentEarning::create([
                    'tenant_id' => $investor->tenant_id,
                    'investment_id' => $investment->id,
                    'installment_id' => $lockedInstallment->id,
                    'investor_id' => $investor->id,
                    'type' => 'monthly_deduction',
                    'amount' => $lockedInstallment->company_amount,
                    'description' => 'Monthly deduction on installment #'.$lockedInstallment->installment_number.' from '.$investor->name.' (Investment #'.$investment->id.')',
                ]);
            }

            // 4. Update Installment Status
            $lockedInstallment->update([
                'status' => 'processed',
                'paid_at' => now(),
            ]);

            // 5. Update Investment Progress
            $paidCount = $investment->installments()->where('status', 'processed')->count();
            $paidTotal = (float) $investment->installments()->where('status', 'processed')->sum('gross_amount');

            $status = ($paidCount >= $investment->duration_months) ? 'completed' : 'active';

            $investment->update([
                'installments_paid_count' => $paidCount,
                'total_paid_amount' => $paidTotal,
                'status' => $status,
            ]);
        });
    }
}
