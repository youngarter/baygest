<?php

use App\Actions\InitializeResidenceAccounting;
use App\Actions\ValidateInvoice;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Residence;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setupResidence(): Residence
{
    $residence = Residence::factory()->create();
    app(InitializeResidenceAccounting::class)->execute($residence);

    return $residence;
}

it('changes status from draft to validated', function () {
    $residence = setupResidence();
    $vendor    = Vendor::factory()->create(['residence_id' => $residence->id]);
    $invoice   = Invoice::factory()->create([
        'residence_id' => $residence->id,
        'vendor_id'    => $vendor->id,
        'status'       => InvoiceStatus::Draft,
    ]);

    app(ValidateInvoice::class)->execute($invoice);

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::Validated);
});

it('creates a balanced journal entry linked to the invoice', function () {
    $residence = setupResidence();
    $vendor    = Vendor::factory()->create(['residence_id' => $residence->id]);
    $invoice   = Invoice::factory()->create([
        'residence_id' => $residence->id,
        'vendor_id'    => $vendor->id,
        'amount_total' => 3000,
        'status'       => InvoiceStatus::Draft,
    ]);

    app(ValidateInvoice::class)->execute($invoice);

    $entry = JournalEntry::where('source_type', Invoice::class)
        ->where('source_id', $invoice->id)
        ->first();

    expect($entry)->not->toBeNull()
        ->and($entry->posted_at)->not->toBeNull()
        ->and((float) $entry->lines->sum('debit'))->toBe(3000.0)
        ->and((float) $entry->lines->sum('credit'))->toBe(3000.0);
});

it('throws LogicException when invoice is not in draft', function () {
    $residence = setupResidence();
    $vendor    = Vendor::factory()->create(['residence_id' => $residence->id]);
    $invoice   = Invoice::factory()->create([
        'residence_id' => $residence->id,
        'vendor_id'    => $vendor->id,
        'status'       => InvoiceStatus::Validated,
    ]);

    app(ValidateInvoice::class)->execute($invoice);
})->throws(\LogicException::class);

it('rolls back invoice status if journal entry creation fails', function () {
    $residence = Residence::factory()->create();
    $vendor    = Vendor::factory()->create(['residence_id' => $residence->id]);
    $invoice   = Invoice::factory()->create([
        'residence_id' => $residence->id,
        'vendor_id'    => $vendor->id,
        'status'       => InvoiceStatus::Draft,
    ]);

    try {
        app(ValidateInvoice::class)->execute($invoice);
    } catch (\Throwable) {
    }

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::Draft)
        ->and(JournalEntry::count())->toBe(0);
});
