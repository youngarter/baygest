<?php

namespace App\Providers;

use App\Events\AssembleeCreated;
use App\Events\BudgetCreated;
use App\Events\BudgetUpdated;
use App\Events\PasswordResetLinkRequested;
use App\Listeners\LogPasswordResetLinkRequested;
use App\Listeners\MarkEmailAsVerifiedOnPasswordReset;
use App\Listeners\NotifyAdminsOfAssembleeCreated;
use App\Listeners\NotifyAdminsOfBudgetCreated;
use App\Listeners\NotifyAdminsOfBudgetUpdated;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PasswordReset::class => [
            MarkEmailAsVerifiedOnPasswordReset::class,
        ],
        PasswordResetLinkRequested::class => [
            LogPasswordResetLinkRequested::class,
        ],
        AssembleeCreated::class => [
            NotifyAdminsOfAssembleeCreated::class,
        ],
        BudgetCreated::class => [
            NotifyAdminsOfBudgetCreated::class,
        ],
        BudgetUpdated::class => [
            NotifyAdminsOfBudgetUpdated::class,
        ],
    ];
}
