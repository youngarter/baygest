<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Enums\ChartOfAccountType;
use Database\Factories\ChartOfAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class ChartOfAccount extends Model implements Auditable
{
    /** @use HasFactory<ChartOfAccountFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'code',
        'name',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => ChartOfAccountType::class,
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    protected $auditInclude = ['code', 'name', 'type'];
}
