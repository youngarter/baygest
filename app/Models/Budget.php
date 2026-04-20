<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BudgetLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Budget extends Model implements Auditable
{
    use BelongsToTenant, HasFactory, HasUuids, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'assemblee_id',
        'titre',
        'description',
        'type',
        'budget_reel',
        'seuil_alerte_estimatif',
        'seuil_alerte_reel',
    ];

    protected function casts(): array
    {
        return [
            'budget_reel' => 'decimal:2',
            'seuil_alerte_estimatif' => 'decimal:2',
            'seuil_alerte_reel' => 'decimal:2',
        ];
    }

    public function assemblee(): BelongsTo
    {
        return $this->belongsTo(Assemblee::class);
    }

    public function budgetLines(): HasMany
    {
        return $this->hasMany(BudgetLine::class);
    }
}
