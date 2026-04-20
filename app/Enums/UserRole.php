<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Coproprietary = 'coproprietary';
    case Locataire = 'locataire';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Coproprietary => 'Copropriétaire',
            self::Locataire => 'Locataire',
        };
    }
}
