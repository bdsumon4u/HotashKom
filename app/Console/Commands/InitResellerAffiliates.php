<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Console\Command;

class InitResellerAffiliates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resellers:init-affiliates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize referral codes and affiliate profiles for all existing resellers';

    /**
     * Execute the console command.
     */
    public function handle(AffiliateService $affiliateService): int
    {
        $this->info('Initializing affiliate profiles for existing resellers...');

        $count = 0;
        User::doesntHave('affiliate')->chunk(100, function ($users) use ($affiliateService, &$count): void {
            foreach ($users as $user) {
                $affiliateService->ensureAffiliateProfile($user);
                $count++;
            }
        });

        $this->info("Initialized affiliate profiles for {$count} resellers.");

        return Command::SUCCESS;
    }
}
