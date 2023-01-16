<?php

namespace App\Exceptions;

use Exception;

class ExchangeRateApiException extends Exception
{
    protected $message = 'Error retrieving exchange rates';
}
