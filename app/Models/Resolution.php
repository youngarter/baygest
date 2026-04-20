<?php

namespace App\Models;

use App\Enums\ResolutionType;
use Database\Factories\ResolutionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Resolution extends Model implements Auditable
{
    /** @use HasFactory<ResolutionFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'assemblee_id',
        'title',
        'description',
        'resolution_type',
    ];

    protected function casts(): array
    {
        return [
            'resolution_type' => ResolutionType::class,
        ];
    }

    public function assemblee(): BelongsTo
    {
        return $this->belongsTo(Assemblee::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    protected $auditInclude = ['title', 'description', 'resolution_type'];
}
