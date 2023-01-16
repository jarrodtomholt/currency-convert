<?php

namespace App\Console\Commands;

use App\Enums\ReportStatus;
use App\Jobs\ProcessExchangeRateReport;
use App\Models\ExchangeRateReport;
use Illuminate\Console\Command;

class ExchangeRateReportScheduleCommand extends Command
{
    protected $signature = 'schedule:pending-reports';

    protected $description = 'Process Pending Exchange Rate Report Requests';

    public function handle()
    {
        ExchangeRateReport::query()
            ->where('status', ReportStatus::PENDING)
            ->each(fn (ExchangeRateReport $report) => ProcessExchangeRateReport::dispatch($report));

        return Command::SUCCESS;
    }
}
