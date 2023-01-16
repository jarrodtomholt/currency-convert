<?php

namespace App\Enums;

enum ReportRange: string
{
    case LAST12MONTHS = 'Last 12 Months';
    case LAST6MONTHS = 'Last 6 Months';
    case LASTMONTH = 'Last Month';
}
