<?php

namespace App\Models;

use Database\Factories\JournalEntryLineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class JournalEntryLine extends Model implements Auditable
{
    /** @use HasFactory<JournalEntryLineFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'journal_entry_id',
        'chart_of_account_id',
        'debit',
        'credit',
        'unit_id',
    ];

    protected function casts(): array
    {
        return [
            'debit'  => 'decimal:2',
            'credit' => 'decimal:2',
        ];
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    protected $auditInclude = ['debit', 'credit', 'chart_of_account_id'];
}
