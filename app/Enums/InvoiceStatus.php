<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft     = 'draft';
    case Validated = 'validated';
    case Paid      = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Brouillon',
            self::Validated => 'Validée',
            self::Paid      => 'Payée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft     => 'gray',
            self::Validated => 'warning',
            self::Paid      => 'success',
        };
    }
}
