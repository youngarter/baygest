<?php

namespace App\Observers;

use App\Events\BudgetCreated;
use App\Events\BudgetUpdated;
use App\Models\Budget;

class BudgetObserver
{
    public function created(Budget $budget): void
    {
        BudgetCreated::dispatch($budget);
    }

    public function updated(Budget $budget): void
    {
        BudgetUpdated::dispatch($budget);
    }
}
