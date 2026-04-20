<?php

namespace App\Events;

use App\Models\Budget;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Budget $budget) {}
}
