<x-filament-panels::page>
    {{-- Filters --}}
    <div class="flex flex-wrap gap-4 p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Compte</label>
            <select
                wire:model.live="selectedAccountId"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500"
            >
                <option value="">— Sélectionner un compte —</option>
                @foreach ($this->getAccounts() as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} — {{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-40">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Du</label>
            <input
                type="date"
                wire:model.live="startDate"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm"
            />
        </div>

        <div class="min-w-40">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Au</label>
            <input
                type="date"
                wire:model.live="endDate"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm"
            />
        </div>
    </div>

    {{-- Account heading --}}
    @if ($account = $this->getSelectedAccount())
        <div class="text-lg font-semibold text-gray-800 dark:text-gray-200">
            {{ $account->code }} — {{ $account->name }}
        </div>
    @endif

    {{-- Movements table --}}
    @php $movements = $this->getMovements(); @endphp

    @if ($this->selectedAccountId && count($movements) === 0)
        <div class="p-6 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            Aucun mouvement pour ce compte sur la période sélectionnée.
        </div>
    @elseif (count($movements) > 0)
        <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Référence</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-right">Débit</th>
                        <th class="px-4 py-3 text-right">Crédit</th>
                        <th class="px-4 py-3 text-right">Solde</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @foreach ($movements as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                                {{ $row['reference'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $row['description'] }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-800 dark:text-gray-200">
                                {{ $row['debit'] > 0 ? number_format($row['debit'], 2, ',', ' ') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-800 dark:text-gray-200">
                                {{ $row['credit'] > 0 ? number_format($row['credit'], 2, ',', ' ') : '—' }}
                            </td>
                            <td @class([
                                'px-4 py-3 text-right tabular-nums font-medium',
                                'text-green-600 dark:text-green-400' => $row['balance'] >= 0,
                                'text-red-600 dark:text-red-400' => $row['balance'] < 0,
                            ])>
                                {{ number_format(abs($row['balance']), 2, ',', ' ') }}
                                {{ $row['balance'] >= 0 ? 'D' : 'C' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold text-sm">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-gray-700 dark:text-gray-300">Totaux</td>
                        <td class="px-4 py-3 text-right tabular-nums text-gray-800 dark:text-gray-200">
                            {{ number_format(array_sum(array_column($movements, 'debit')), 2, ',', ' ') }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums text-gray-800 dark:text-gray-200">
                            {{ number_format(array_sum(array_column($movements, 'credit')), 2, ',', ' ') }}
                        </td>
                        @php $finalBalance = end($movements)['balance']; @endphp
                        <td @class([
                            'px-4 py-3 text-right tabular-nums',
                            'text-green-600 dark:text-green-400' => $finalBalance >= 0,
                            'text-red-600 dark:text-red-400' => $finalBalance < 0,
                        ])>
                            {{ number_format(abs($finalBalance), 2, ',', ' ') }}
                            {{ $finalBalance >= 0 ? 'D' : 'C' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="p-6 text-center text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            Sélectionnez un compte pour afficher ses mouvements.
        </div>
    @endif
</x-filament-panels::page>
