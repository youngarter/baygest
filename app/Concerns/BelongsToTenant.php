<?php

namespace App\Concerns;

use App\Scopes\TenantScope;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait à utiliser sur tous les modèles métier (Assemblee, Budget, etc.)
 * Applique le TenantScope et auto-assigne residence_id à la création.
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (self $model): void {
            if (empty($model->residence_id)) {
                $model->residence_id = self::resolveCurrentResidenceId();
            }
        });
    }

    public function residence(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Residence::class);
    }

    public static function forResidence(int $residenceId): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(TenantScope::class)
            ->where('residence_id', $residenceId);
    }

    private static function resolveCurrentResidenceId(): ?int
    {
        if (app()->bound('filament') && Filament::getTenant()) {
            return Filament::getTenant()->id;
        }

        if (session()->has('active_residence_id')) {
            return session('active_residence_id');
        }

        return auth()->user()?->residence_id;
    }
}
