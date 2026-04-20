<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Database\Factories\JournalEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class JournalEntry extends Model implements Auditable
{
    /** @use HasFactory<JournalEntryFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'date',
        'reference',
        'description',
        'source_type',
        'source_id',
        'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'date'      => 'date',
            'posted_at' => 'datetime',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function isPosted(): bool
    {
        return $this->posted_at !== null;
    }

    protected $auditInclude = ['date', 'reference', 'description', 'posted_at'];
    protected $auditExclude = ['source_type', 'source_id'];
}
