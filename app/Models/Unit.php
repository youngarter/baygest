<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Unit extends Model implements Auditable
{
    /** @use HasFactory<UnitFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'unit_type_id',
        'user_id',
        'name',
        'tantiemes',
    ];

    protected function casts(): array
    {
        return [
            'tantiemes' => 'decimal:4',
        ];
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(UnitType::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    protected $auditInclude = ['name', 'tantiemes', 'unit_type_id', 'user_id'];
}
