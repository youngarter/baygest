<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Database\Factories\VendorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Vendor extends Model implements Auditable
{
    /** @use HasFactory<VendorFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'name',
        'tax_id',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    protected $auditInclude = ['name', 'tax_id'];
}
