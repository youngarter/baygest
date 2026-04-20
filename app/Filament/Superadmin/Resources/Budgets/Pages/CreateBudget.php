<?php

namespace App\Filament\Superadmin\Resources\Budgets\Pages;

use App\Filament\Superadmin\Resources\Budgets\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;
}
