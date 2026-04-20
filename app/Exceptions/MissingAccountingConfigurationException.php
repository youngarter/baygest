<?php

namespace App\Exceptions;

use RuntimeException;

class MissingAccountingConfigurationException extends RuntimeException
{
    public function __construct(int $residenceId)
    {
        parent::__construct(
            "No accounting configuration found for residence #{$residenceId}. Run InitializeResidenceAccounting first."
        );
    }
}
