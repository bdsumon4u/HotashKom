<?php

namespace App\Console\Commands;

use App\Models\AffiliateCommission;
use App\Models\ResellerAffiliate;
use App\Models\User;
use App\Services\AffiliateService;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class SeedAffiliateNetwork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resellers:seed-network {--clean : Remove previously seeded demo resellers before creating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create 10-15 demo resellers in a multi-level tree hierarchy and verify them to demonstrate dual-leg round-robin assignment and milestone commissions';

    /**
     * Execute the console command.
     */
    public function handle(AffiliateService $affiliateService): int
    {
        // Clean up previously seeded demo resellers and reset demo state
        $demoUsers = User::where('email', 'like', 'demo_affiliate_%@example.com')->get();
        foreach ($demoUsers as $demoUser) {
            $demoUser->delete();
        }
        AffiliateCommission::truncate();

        // Pick or create root reseller (Use existing User 1 or create dedicated demo root)
        $rootUser = User::first();
        if (! $rootUser) {
            $rootUser = User::create([
                'name' => 'Root Reseller',
                'shop_name' => 'Root Shop',
                'email' => 'demo_affiliate_root@example.com',
                'phone_number' => '+8801999990000',
                'bkash_number' => '+8801999990000',
                'password' => Hash::make('password'),
                'is_verified' => true,
            ]);
        }

        $rootAffiliate = $affiliateService->ensureAffiliateProfile($rootUser);
        $rootAffiliate->update([
            'left_total' => 0,
            'right_total' => 0,
            'direct_left_count' => 0,
            'direct_right_count' => 0,
            'direct_leg' => null,
            'referrer_id' => null,
            'verified_at' => now(),
        ]);

        $this->info("Using Root Reseller: {$rootUser->name} (ID: {$rootUser->id}, Code: {$rootAffiliate->referral_code})");

        /*
         * We will create 14 resellers:
         * - Level 1: Under Root:
         *     #01 (Verified -> Left)
         *     #02 (Verified -> Right)
         *     #03 (Verified -> Left)
         *     #04 (Verified -> Right)
         *     #05 (Verified -> Left)
         *     #06 (PENDING -> no leg, no counts)
         * - Level 2: Under #01 (in Root's Left):
         *     #07 (Verified -> #01 Left, Root Left)
         *     #08 (Verified -> #01 Right, Root Left)
         *     #09 (Verified -> #01 Left, Root Left)
         * - Level 2: Under #02 (in Root's Right):
         *     #10 (Verified -> #02 Left, Root Right)
         *     #11 (Verified -> #02 Right, Root Right)
         * - Level 3: Under #07:
         *     #12 (Verified -> #07 Left, #01 Left, Root Left)
         * - Level 3: Under #10:
         *     #13 (Verified -> #10 Left, #02 Left, Root Right)
         * - Level 1: Under Root:
         *     #14 (PENDING -> stays unverified)
         */

        $definitions = [
            // Level 1 under Root
            ['num' => '01', 'name' => 'Reseller 01', 'referrer' => 'root', 'verify' => true],
            ['num' => '02', 'name' => 'Reseller 02', 'referrer' => 'root', 'verify' => true],
            ['num' => '03', 'name' => 'Reseller 03', 'referrer' => 'root', 'verify' => true],
            ['num' => '04', 'name' => 'Reseller 04', 'referrer' => 'root', 'verify' => true],
            ['num' => '05', 'name' => 'Reseller 05', 'referrer' => 'root', 'verify' => true],
            ['num' => '06', 'name' => 'Reseller 06 (Pending)', 'referrer' => 'root', 'verify' => false],

            // Level 2 under Reseller 01
            ['num' => '07', 'name' => 'Reseller 07', 'referrer' => '01', 'verify' => true],
            ['num' => '08', 'name' => 'Reseller 08', 'referrer' => '01', 'verify' => true],
            ['num' => '09', 'name' => 'Reseller 09', 'referrer' => '01', 'verify' => true],

            // Level 2 under Reseller 02
            ['num' => '10', 'name' => 'Reseller 10', 'referrer' => '02', 'verify' => true],
            ['num' => '11', 'name' => 'Reseller 11', 'referrer' => '02', 'verify' => true],

            // Level 3 under Reseller 07
            ['num' => '12', 'name' => 'Reseller 12', 'referrer' => '07', 'verify' => true],

            // Level 3 under Reseller 10
            ['num' => '13', 'name' => 'Reseller 13', 'referrer' => '10', 'verify' => true],

            // Level 1 pending under Root
            ['num' => '14', 'name' => 'Reseller 14 (Pending)', 'referrer' => 'root', 'verify' => false],

            // Level 3 under Reseller 08 (Indirect to Reseller 01, on Reseller 01's Right leg)
            ['num' => '15', 'name' => 'Reseller 15 (under 08)', 'referrer' => '08', 'verify' => true],

            // Level 3 under Reseller 11 (Indirect to Reseller 02, on Reseller 02's Right leg)
            ['num' => '16', 'name' => 'Reseller 16 (under 11)', 'referrer' => '11', 'verify' => true],
        ];

        /** @var array<string, User> $createdUsers */
        $createdUsers = ['root' => $rootUser];

        $this->output->progressStart(count($definitions));

        foreach ($definitions as $def) {
            $referrerUser = $createdUsers[$def['referrer']];
            $referrerUser->refresh();
            $referrerCode = $referrerUser->affiliate->referral_code;

            $email = "demo_affiliate_{$def['num']}@example.com";
            $phone = '+880199999'.str_pad($def['num'], 4, '0', STR_PAD_LEFT);

            $user = new User([
                'name' => $def['name'],
                'shop_name' => $def['name'].' Shop',
                'email' => $email,
                'phone_number' => $phone,
                'bkash_number' => $phone,
                'password' => Hash::make('password'),
                'is_verified' => false,
            ]);
            $user->referral_code_input = $referrerCode;
            $user->save();

            // If marked for verification, verify and process queue
            if ($def['verify']) {
                $user->update(['is_verified' => true]);

                // Run queue worker to process the PropagateReferralVerification job
                Artisan::call('queue:work', ['--stop-when-empty' => true]);
            }

            $user->load('affiliate');
            $createdUsers[$def['num']] = $user;

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->newLine();

        // 1. Resellers Summary Table
        $this->info('=== Resellers Network Overview ===');
        $rows = [];
        foreach ($createdUsers as $key => $user) {
            $user->refresh();
            $affiliate = $user->affiliate;

            $referrerName = '-';
            if ($affiliate && $affiliate->referrer_id) {
                $referrerName = User::find($affiliate->referrer_id)?->name ?? "User #{$affiliate->referrer_id}";
            }

            $rows[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Ref Code' => $affiliate?->referral_code ?? '-',
                'Referrer' => $referrerName,
                'Direct Leg' => $affiliate?->direct_leg ?? '(pending/none)',
                'Left Total' => $affiliate?->left_total ?? 0,
                'Right Total' => $affiliate?->right_total ?? 0,
                'Direct (L/R)' => ($affiliate?->direct_left_count ?? 0).'/'.($affiliate?->direct_right_count ?? 0),
                'Verified' => $user->is_verified ? 'Yes' : 'No',
                'Wallet Balance' => number_format((float) $user->balance, 2).' BDT',
            ];
        }

        $this->table(
            ['ID', 'Name', 'Ref Code', 'Referrer', 'Direct Leg', 'Left Total', 'Right Total', 'Direct (L/R)', 'Verified', 'Wallet Balance'],
            $rows
        );

        // 2. Commission Ledger Table
        $this->newLine();
        $this->info('=== Milestone Commissions Awarded ===');
        $commissions = AffiliateCommission::with(['user', 'triggeredBy'])->latest()->get();

        if ($commissions->isEmpty()) {
            $this->comment('No commissions awarded.');
        } else {
            $commRows = [];
            foreach ($commissions as $comm) {
                $isDirect = ResellerAffiliate::where('user_id', $comm->triggered_by_user_id)
                    ->where('referrer_id', $comm->user_id)
                    ->exists();

                $commRows[] = [
                    'ID' => $comm->id,
                    'Beneficiary' => $comm->user?->name,
                    'Milestone (x)' => "{$comm->milestone_x} Pairs",
                    'Rate (%)' => "{$comm->percentage}%",
                    'Base Amount' => number_format($comm->base_amount, 2).' BDT',
                    'Commission Earned' => number_format($comm->amount, 2).' BDT',
                    'Trigger Leg' => strtoupper($comm->trigger_leg),
                    'Triggered By' => $comm->triggeredBy?->name,
                    'Referral Type' => $isDirect ? 'Direct Referral' : 'INDIRECT Downline',
                ];
            }

            $this->table(
                ['ID', 'Beneficiary', 'Milestone (x)', 'Rate (%)', 'Base Amount', 'Commission Earned', 'Trigger Leg', 'Triggered By', 'Referral Type'],
                $commRows
            );
        }

        // 3. Wallet Transactions Table
        $this->newLine();
        $this->info('=== Wallet Deposit Transactions (Audit Log) ===');
        $transactions = Transaction::where('type', 'deposit')
            ->where('meta->type', 'affiliate_commission')
            ->latest('id')
            ->get();

        $txRows = [];
        foreach ($transactions as $tx) {
            $user = User::find($tx->payable_id);
            $txRows[] = [
                'User' => $user?->name ?? "User #{$tx->payable_id}",
                'Type' => $tx->type,
                'Amount' => number_format((float) $tx->amount, 2).' BDT',
                'Confirmed' => $tx->confirmed ? 'Yes' : 'No',
                'Reason' => $tx->meta['reason'] ?? '-',
            ];
        }

        if (! empty($txRows)) {
            $this->table(
                ['User', 'Type', 'Amount', 'Confirmed', 'Reason'],
                $txRows
            );
        }

        return Command::SUCCESS;
    }
}
