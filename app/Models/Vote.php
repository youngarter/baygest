<?php

namespace App\Models;

use App\Enums\VoteDecision;
use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Vote extends Model implements Auditable
{
    /** @use HasFactory<VoteFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'resolution_id',
        'unit_id',
        'decision',
        'weight_used',
    ];

    protected function casts(): array
    {
        return [
            'decision'    => VoteDecision::class,
            'weight_used' => 'decimal:4',
        ];
    }

    public function resolution(): BelongsTo
    {
        return $this->belongsTo(Resolution::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    protected $auditInclude = ['decision', 'weight_used'];
}
