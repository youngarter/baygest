<?php

namespace App\Actions;

use App\Enums\ChartOfAccountType;
use App\Models\AccountingConfig;
use App\Models\ChartOfAccount;
use App\Models\Residence;
use Illuminate\Support\Facades\DB;

class InitializeResidenceAccounting
{
    /**
     * @return array{accounts_created: int, already_initialized: bool}
     */
    public function execute(Residence $residence): array
    {
        $alreadyInitialized = AccountingConfig::where('residence_id', $residence->id)->exists();

        $created = DB::transaction(function () use ($residence): int {
            $count = 0;

            foreach ($this->planComptable() as $account) {
                $exists = ChartOfAccount::where('residence_id', $residence->id)
                    ->where('code', $account['code'])
                    ->exists();

                if (! $exists) {
                    ChartOfAccount::create([
                        'residence_id' => $residence->id,
                        'code'         => $account['code'],
                        'name'         => $account['name'],
                        'type'         => $account['type']->value,
                    ]);
                    $count++;
                }
            }

            $config = AccountingConfig::firstOrCreate(['residence_id' => $residence->id]);

            $config->update([
                'default_bank_account_id'   => $this->findAccountId($residence->id, '5141'),
                'default_vendor_account_id' => $this->findAccountId($residence->id, '4411'),
                'default_owner_account_id'  => $this->findAccountId($residence->id, '3421'),
            ]);

            return $count;
        });

        return ['accounts_created' => $created, 'already_initialized' => $alreadyInitialized];
    }

    private function findAccountId(int $residenceId, string $code): ?int
    {
        return ChartOfAccount::where('residence_id', $residenceId)
            ->where('code', $code)
            ->value('id');
    }

    /**
     * Plan comptable marocain — 15 accounts for syndic de copropriété.
     *
     * @return array<array{code: string, name: string, type: ChartOfAccountType}>
     */
    private function planComptable(): array
    {
        return [
            // Capitaux
            ['code' => '1011', 'name' => 'Fonds de réserve',                        'type' => ChartOfAccountType::Equity],

            // Immobilisations
            ['code' => '2100', 'name' => 'Immobilisations corporelles',              'type' => ChartOfAccountType::Asset],

            // Tiers — créances
            ['code' => '3421', 'name' => 'Copropriétaires — charges courantes',      'type' => ChartOfAccountType::Asset],
            ['code' => '4417', 'name' => 'TVA récupérable',                          'type' => ChartOfAccountType::Asset],

            // Tiers — dettes
            ['code' => '4411', 'name' => 'Fournisseurs',                             'type' => ChartOfAccountType::Liability],
            ['code' => '4490', 'name' => 'Charges à payer',                          'type' => ChartOfAccountType::Liability],

            // Trésorerie
            ['code' => '5141', 'name' => 'Banques — compte courant',                 'type' => ChartOfAccountType::Asset],
            ['code' => '5161', 'name' => 'Caisse',                                   'type' => ChartOfAccountType::Asset],

            // Charges
            ['code' => '6111', 'name' => 'Entretien et réparations',                 'type' => ChartOfAccountType::Expense],
            ['code' => '6122', 'name' => 'Énergie et fluides',                       'type' => ChartOfAccountType::Expense],
            ['code' => '6132', 'name' => 'Honoraires syndic',                        'type' => ChartOfAccountType::Expense],
            ['code' => '6161', 'name' => 'Assurances',                               'type' => ChartOfAccountType::Expense],
            ['code' => '6171', 'name' => 'Cotisations syndicales',                   'type' => ChartOfAccountType::Expense],

            // Produits
            ['code' => '7111', 'name' => 'Charges communes récupérées',              'type' => ChartOfAccountType::Revenue],
            ['code' => '7143', 'name' => 'Produits financiers',                      'type' => ChartOfAccountType::Revenue],
        ];
    }
}
