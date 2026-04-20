<?php

namespace App\Enums;

enum Permission: string
{
    // ── Accounting ────────────────────────────────────────────────────────────
    case AccountView            = 'account.view';
    case AccountCreate          = 'account.create';
    case AccountUpdate          = 'account.update';
    case AccountDelete          = 'account.delete';

    case JournalEntryView       = 'journal_entry.view';
    case JournalEntryCreate     = 'journal_entry.create';
    case JournalEntryDelete     = 'journal_entry.delete';
    case JournalEntryReverse    = 'journal_entry.reverse';

    case AccountingConfigView   = 'accounting_config.view';
    case AccountingConfigUpdate = 'accounting_config.update';

    // ── Treasury ──────────────────────────────────────────────────────────────
    case InvoiceView    = 'invoice.view';
    case InvoiceCreate  = 'invoice.create';
    case InvoiceUpdate  = 'invoice.update';
    case InvoiceDelete  = 'invoice.delete';

    case PaymentView    = 'payment.view';
    case PaymentCreate  = 'payment.create';
    case PaymentDelete  = 'payment.delete';

    case VendorView     = 'vendor.view';
    case VendorCreate   = 'vendor.create';
    case VendorUpdate   = 'vendor.update';
    case VendorDelete   = 'vendor.delete';

    // ── Assemblies ────────────────────────────────────────────────────────────
    case AssembleeCreate = 'assemblee.create';
    case AssembleeView   = 'assemblee.view';
    case AssembleeUpdate = 'assemblee.update';
    case AssembleeDelete = 'assemblee.delete';

    case ResolutionView   = 'resolution.view';
    case ResolutionCreate = 'resolution.create';
    case ResolutionUpdate = 'resolution.update';
    case ResolutionDelete = 'resolution.delete';

    case VoteView   = 'vote.view';
    case VoteCreate = 'vote.create';
    case VoteDelete = 'vote.delete';

    // ── Units ─────────────────────────────────────────────────────────────────
    case UnitView   = 'unit.view';
    case UnitCreate = 'unit.create';
    case UnitUpdate = 'unit.update';
    case UnitDelete = 'unit.delete';

    case UnitTypeView   = 'unit_type.view';
    case UnitTypeCreate = 'unit_type.create';
    case UnitTypeUpdate = 'unit_type.update';
    case UnitTypeDelete = 'unit_type.delete';

    // ── Budget ────────────────────────────────────────────────────────────────
    case BudgetCreate = 'budget.create';
    case BudgetView   = 'budget.view';
    case BudgetUpdate = 'budget.update';
    case BudgetDelete = 'budget.delete';

    case BudgetLineView   = 'budget_line.view';
    case BudgetLineCreate = 'budget_line.create';
    case BudgetLineUpdate = 'budget_line.update';
    case BudgetLineDelete = 'budget_line.delete';

    // ── Users ─────────────────────────────────────────────────────────────────
    case UserCreate = 'user.create';
    case UserView   = 'user.view';
    case UserUpdate = 'user.update';
    case UserDelete = 'user.delete';

    // ── Derived methods ───────────────────────────────────────────────────────

    public function label(): string
    {
        $action = substr($this->value, strrpos($this->value, '.') + 1);

        return match ($action) {
            'view'    => 'Voir',
            'create'  => 'Créer',
            'update'  => 'Modifier',
            'delete'  => '🔐 Supprimer',
            'reverse' => 'Extourner',
            default   => $action,
        };
    }

    public function shortLabel(): string
    {
        [$resource, $action] = explode('.', $this->value, 2);

        $prefix = match ($resource) {
            'account'           => 'Cmpt.',
            'journal_entry'     => 'Écrit.',
            'accounting_config' => 'Conf.',
            'invoice'           => 'Fact.',
            'payment'           => 'Paie.',
            'vendor'            => 'Fourn.',
            'unit'              => 'Lot.',
            'unit_type'         => 'T.Lot.',
            'budget_line'       => 'L.Bud.',
            'resolution'        => 'Rés.',
            'vote'              => 'Vote.',
            'assemblee'         => 'Ass.',
            'budget'            => 'Bud.',
            'user'              => 'Usr.',
            default             => $resource,
        };

        $actionLabel = match ($action) {
            'view'    => 'Voir',
            'create'  => 'Créer',
            'update'  => 'Mod.',
            'delete'  => 'Supp.',
            'reverse' => 'Ext.',
            default   => $action,
        };

        return "{$prefix} {$actionLabel}";
    }

    public function group(): PermissionGroup
    {
        $resource = explode('.', $this->value)[0];

        return match ($resource) {
            'account', 'journal_entry', 'accounting_config' => PermissionGroup::Accounting,
            'invoice', 'payment', 'vendor'                  => PermissionGroup::Treasury,
            'assemblee', 'resolution', 'vote'               => PermissionGroup::Assemblies,
            'unit', 'unit_type'                             => PermissionGroup::Units,
            'budget', 'budget_line'                         => PermissionGroup::Budget,
            'user'                                          => PermissionGroup::Users,
        };
    }

    /** @return array<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Returns [value => label] options for a given group, for CheckboxList.
     *
     * @return array<string, string>
     */
    public static function optionsForGroup(PermissionGroup $group): array
    {
        return collect(self::cases())
            ->filter(fn (self $p): bool => $p->group() === $group)
            ->mapWithKeys(fn (self $p): array => [$p->value => $p->label()])
            ->toArray();
    }
}
