<?php

namespace App\Observers;

use App\Events\AssembleeCreated;
use App\Models\Assemblee;

class AssembleeObserver
{
    public function created(Assemblee $assemblee): void
    {
        AssembleeCreated::dispatch($assemblee);
    }
}
