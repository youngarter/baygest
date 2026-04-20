<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Database\Factories\AccountingConfigFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class AccountingConfig extends Model implements Auditable
{
    /** @use HasFactory<AccountingConfigFactory> */
    use BelongsToTenant, HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'residence_id',
        'default_bank_account_id',
        'default_vendor_account_id',
        'default_owner_account_id',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'default_bank_account_id');
    }

    public function vendorAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'default_vendor_account_id');
    }

    public function ownerAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'default_owner_account_id');
    }

    protected $auditInclude = [
        'default_bank_account_id',
        'default_vendor_account_id',
        'default_owner_account_id',
    ];
}
