<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Applique automatiquement le filtre residence_id sur toutes les requêtes.
 * Désactivé dans les Jobs (pas d'Auth) — injecter residence_id explicitement.
 */
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $residenceId = $this->resolveResidenceId();

        if ($residenceId !== null) {
            $builder->where($model->getTable().'.residence_id', $residenceId);
        }
    }

    private function resolveResidenceId(): ?int
    {
        // Priorité 1 : contexte Filament (tenant actif)
        if (app()->bound('filament') && filament()->getTenant()) {
            return filament()->getTenant()->id;
        }

        // Priorité 2 : tenant switching SuperAdmin (session)
        if (session()->has('active_residence_id')) {
            return session('active_residence_id');
        }

        // Priorité 3 : résidence de l'utilisateur connecté
        if (auth()->check()) {
            return auth()->user()->residence_id;
        }

        return null;
    }
}
