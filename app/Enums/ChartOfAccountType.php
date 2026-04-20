<?php

namespace App\Enums;

enum ChartOfAccountType: string
{
    case Asset     = 'asset';
    case Liability = 'liability';
    case Equity    = 'equity';
    case Revenue   = 'revenue';
    case Expense   = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Asset     => 'Actif',
            self::Liability => 'Passif',
            self::Equity    => 'Capitaux propres',
            self::Revenue   => 'Produit',
            self::Expense   => 'Charge',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Asset     => 'info',
            self::Liability => 'warning',
            self::Equity    => 'success',
            self::Revenue   => 'success',
            self::Expense   => 'danger',
        };
    }
}
