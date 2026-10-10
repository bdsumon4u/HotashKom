<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\InvestmentService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessInvestmentInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:process-installments {--date= : Process installments due on or before this date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due monthly investment return installments and distribute payouts, bonuses, and company earnings';

    /**
     * Execute the console command.
     */
    public function handle(InvestmentService $investmentService): int
    {
        if (! config('investment.enabled', false)) {
            $this->warn('Investment module is currently disabled in configuration.');

            return Command::SUCCESS;
        }

        $dateOption = $this->option('date');
        $asOfDate = $dateOption ? Carbon::parse($dateOption) : now();

        $this->info('Processing due investment installments as of: '.$asOfDate->toDateString());

        $processedCount = $investmentService->processDueInstallments($asOfDate);

        $this->info("Successfully processed {$processedCount} installment(s).");

        return Command::SUCCESS;
    }
}
