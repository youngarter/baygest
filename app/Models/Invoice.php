<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Enums\InvoiceStatus;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;

class Invoice extends Model implements Auditable
{
    /** @use HasFactory<InvoiceFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'vendor_id',
        'budget_line_id',
        'title',
        'amount_total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount_total' => 'decimal:2',
            'status'       => InvoiceStatus::class,
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function budgetLine(): BelongsTo
    {
        return $this->belongsTo(BudgetLine::class);
    }

    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'source');
    }

    protected $auditInclude = ['title', 'amount_total', 'status', 'vendor_id', 'budget_line_id'];
}
