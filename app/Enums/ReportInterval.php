<?php

namespace App\Enums;

enum ReportInterval: string
{
    case MONTHLY = 'monthly';
    case WEEKLY = 'weekly';
    case DAILY = 'daily';
    case HOURLY = 'hourly';
}
