<?php

namespace App\Enums;

enum ResolutionType: string
{
    case BudgetApproval   = 'budget_approval';
    case WorkApproval     = 'work_approval';
    case ContractApproval = 'contract_approval';
    case Other            = 'other';

    public function color(): string
    {
        return match ($this) {
            self::BudgetApproval   => 'info',
            self::WorkApproval     => 'warning',
            self::ContractApproval => 'purple',
            self::Other            => 'gray',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::BudgetApproval   => 'Approbation budget',
            self::WorkApproval     => 'Approbation travaux',
            self::ContractApproval => 'Approbation contrat',
            self::Other            => 'Autre',
        };
    }
}
