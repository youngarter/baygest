<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
{
    /** @use HasFactory<PaymentFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'unit_id',
        'amount',
        'payment_method',
        'date_received',
    ];

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'date_received' => 'date',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'source');
    }

    protected $auditInclude = ['amount', 'payment_method', 'date_received', 'unit_id'];
}
