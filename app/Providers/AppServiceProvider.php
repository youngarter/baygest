<?php

namespace App\Providers;

use App\Models\Assemblee;
use App\Models\Budget;
use App\Models\Residence;
use App\Models\User;
use App\Observers\AssembleeObserver;
use App\Observers\BudgetObserver;
use App\Observers\UserObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerObservers();
        $this->registerPolicies();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function registerObservers(): void
    {
        User::observe(UserObserver::class);
        Assemblee::observe(AssembleeObserver::class);
        Budget::observe(BudgetObserver::class);
    }

    protected function registerPolicies(): void
    {
        // Superadmin bypasses all policy checks
        Gate::before(function ($user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
