<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Investor;
use App\Models\ResellerAffiliate;
use App\Services\AffiliateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateNumericReferralCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral-codes:regenerate-numeric
                            {--type=all : The target type to regenerate: "resellers", "investors", or "all"}
                            {--resellers : Target resellers only}
                            {--investors : Target investors only}
                            {--dry-run : Preview changes without updating the database}';

    /**
     * The console command aliases.
     *
     * @var array<int, string>
     */
    protected $aliases = [
        'resellers:regenerate-numeric-referral-codes',
        'investors:regenerate-numeric-referral-codes',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate unique numeric referral codes for resellers and/or investors whose referral codes contain alphabetic characters';

    /**
     * Execute the console command.
     */
    public function handle(AffiliateService $affiliateService): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $targetResellers = (bool) $this->option('resellers');
        $targetInvestors = (bool) $this->option('investors');

        // If invoked via an alias and no explicit target flag was provided, scope appropriately
        $invokedCommand = $_SERVER['argv'][1] ?? '';
        if (! $targetResellers && ! $targetInvestors) {
            if (str_contains($invokedCommand, 'resellers:')) {
                $targetResellers = true;
            } elseif (str_contains($invokedCommand, 'investors:')) {
                $targetInvestors = true;
            }
        }

        // If still neither flag was passed, resolve from --type option
        if (! $targetResellers && ! $targetInvestors) {
            $type = strtolower((string) ($this->option('type') ?: 'all'));

            if ($type === 'resellers') {
                $targetResellers = true;
            } elseif ($type === 'investors') {
                $targetInvestors = true;
            } elseif ($type === 'all') {
                $targetResellers = true;
                $targetInvestors = true;
            } else {
                $this->error("Invalid --type '{$type}'. Allowed values: all, resellers, investors.");

                return Command::FAILURE;
            }
        }

        $resellersUpdated = 0;
        $investorsUpdated = 0;

        if ($targetResellers) {
            $resellersUpdated = $this->regenerateResellerCodes($affiliateService, $isDryRun);
        }

        if ($targetInvestors) {
            $investorsUpdated = $this->regenerateInvestorCodes($isDryRun);
        }

        $this->newLine();

        if ($isDryRun) {
            $this->warn('DRY RUN: No database changes were saved.');
        } else {
            $total = $resellersUpdated + $investorsUpdated;
            $this->info("Done! Regenerated {$total} numeric referral code(s) ({$resellersUpdated} reseller(s), {$investorsUpdated} investor(s)).");
        }

        return Command::SUCCESS;
    }

    /**
     * Regenerate codes for resellers.
     */
    protected function regenerateResellerCodes(AffiliateService $affiliateService, bool $isDryRun): int
    {
        $this->info('--- Reseller Referral Codes ---');

        $affiliates = ResellerAffiliate::with('user')
            ->whereRaw("referral_code REGEXP '[A-Za-z]'")
            ->get();

        if ($affiliates->isEmpty()) {
            $this->line('No reseller referral codes with alphabetic characters found.');

            return 0;
        }

        $this->info("Found {$affiliates->count()} reseller referral code(s) with alphabetic characters.");

        $rows = [];

        DB::transaction(function () use ($affiliates, $affiliateService, $isDryRun, &$rows): void {
            foreach ($affiliates as $affiliate) {
                $oldCode = $affiliate->referral_code;
                $newCode = $affiliateService->generateUniqueCode();

                if (! $isDryRun) {
                    $affiliate->update(['referral_code' => $newCode]);
                }

                $rows[] = [
                    'user_id' => $affiliate->user_id,
                    'user_name' => $affiliate->user?->name ?? 'N/A',
                    'old_code' => $oldCode,
                    'new_code' => $newCode,
                ];
            }
        });

        $this->table(['User ID', 'Name', 'Old Code', 'New Code'], $rows);

        return $affiliates->count();
    }

    /**
     * Regenerate codes for investors.
     */
    protected function regenerateInvestorCodes(bool $isDryRun): int
    {
        $this->info('--- Investor Referral Codes ---');

        $investors = Investor::whereRaw("referral_code REGEXP '[A-Za-z]'")->get();

        if ($investors->isEmpty()) {
            $this->line('No investor referral codes with alphabetic characters found.');

            return 0;
        }

        $this->info("Found {$investors->count()} investor referral code(s) with alphabetic characters.");

        $rows = [];

        DB::transaction(function () use ($investors, $isDryRun, &$rows): void {
            foreach ($investors as $investor) {
                $oldCode = $investor->referral_code;
                $newCode = Investor::generateReferralCode();

                if (! $isDryRun) {
                    $investor->update(['referral_code' => $newCode]);
                }

                $rows[] = [
                    'id' => $investor->id,
                    'name' => $investor->name,
                    'email' => $investor->email,
                    'old_code' => $oldCode,
                    'new_code' => $newCode,
                ];
            }
        });

        $this->table(['ID', 'Name', 'Email', 'Old Code', 'New Code'], $rows);

        return $investors->count();
    }
}
