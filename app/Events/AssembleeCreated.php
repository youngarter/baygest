<?php

namespace App\Events;

use App\Models\Assemblee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssembleeCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Assemblee $assemblee) {}
}
