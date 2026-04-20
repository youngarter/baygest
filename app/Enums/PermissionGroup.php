<?php

namespace App\Enums;

enum PermissionGroup: string
{
    case Accounting = 'accounting';
    case Treasury   = 'treasury';
    case Assemblies = 'assemblies';
    case Units      = 'units';
    case Budget     = 'budget';
    case Users      = 'users';

    public function label(): string
    {
        return match ($this) {
            self::Accounting => 'Comptabilité',
            self::Treasury   => 'Trésorerie',
            self::Assemblies => 'Assemblées',
            self::Units      => 'Lots',
            self::Budget     => 'Budget',
            self::Users      => 'Utilisateurs',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Accounting => 'heroicon-o-calculator',
            self::Treasury   => 'heroicon-o-banknotes',
            self::Assemblies => 'heroicon-o-user-group',
            self::Units      => 'heroicon-o-home',
            self::Budget     => 'heroicon-o-chart-pie',
            self::Users      => 'heroicon-o-users',
        };
    }
}
