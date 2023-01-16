<?php

namespace App\Policies;

use App\Models\ExchangeRateReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExchangeRateReportPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ExchangeRateReport $report): bool
    {
        return $user->id === $report->user_id;
    }
}
