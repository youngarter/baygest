<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Resolution;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Assemblee extends Model implements Auditable
{
    use BelongsToTenant, HasFactory, HasUuids, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'annee_syndic',
        'type',
        'titre',
        'description',
        'date_assemblee',
    ];

    protected function casts(): array
    {
        return [
            'date_assemblee' => 'date',
            'annee_syndic' => 'integer',
        ];
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(Resolution::class);
    }
}
