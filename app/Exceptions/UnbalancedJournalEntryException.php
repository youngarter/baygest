<?php

namespace App\Exceptions;

use RuntimeException;

class UnbalancedJournalEntryException extends RuntimeException
{
    public function __construct(float $totalDebit, float $totalCredit)
    {
        parent::__construct(
            sprintf(
                'Journal entry is unbalanced: debit %.2f ≠ credit %.2f.',
                $totalDebit,
                $totalCredit
            )
        );
    }
}
