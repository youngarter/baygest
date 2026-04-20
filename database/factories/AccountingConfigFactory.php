<?php

namespace Database\Factories;

use App\Models\AccountingConfig;
use App\Models\Residence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountingConfig>
 */
class AccountingConfigFactory extends Factory
{
    protected $model = AccountingConfig::class;

    public function definition(): array
    {
        return [
            'residence_id'              => Residence::factory(),
            'default_bank_account_id'   => null,
            'default_vendor_account_id' => null,
            'default_owner_account_id'  => null,
        ];
    }
}
