<?php

namespace App\Models;

use Database\Factories\BudgetLineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class BudgetLine extends Model implements Auditable
{
    /** @use HasFactory<BudgetLineFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'budget_id',
        'chart_of_account_id',
        'title',
        'amount_previsionnel',
    ];

    protected function casts(): array
    {
        return [
            'amount_previsionnel' => 'decimal:2',
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    protected $auditInclude = ['title', 'amount_previsionnel', 'chart_of_account_id'];
}
