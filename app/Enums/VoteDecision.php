<?php

namespace App\Enums;

enum VoteDecision: string
{
    case For     = 'for';
    case Against = 'against';
    case Abstain = 'abstain';

    public function label(): string
    {
        return match ($this) {
            self::For     => 'Pour',
            self::Against => 'Contre',
            self::Abstain => 'Abstention',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::For     => 'success',
            self::Against => 'danger',
            self::Abstain => 'gray',
        };
    }
}
