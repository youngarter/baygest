<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Database\Factories\UnitTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class UnitType extends Model implements Auditable
{
    /** @use HasFactory<UnitTypeFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'name',
        'default_annual_fee',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'default_annual_fee' => 'decimal:2',
        ];
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    protected $auditInclude = ['name', 'default_annual_fee', 'description'];
}
