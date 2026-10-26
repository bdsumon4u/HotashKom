<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PropagateReferralVerification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId) {}

    /**
     * Execute the job.
     */
    public function handle(AffiliateService $affiliateService): void
    {
        $user = User::find($this->userId);
        if (! $user || ! $user->is_verified) {
            return;
        }

        $affiliateService->processVerification($user);
    }
}
