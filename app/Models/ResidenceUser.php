<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ResidenceUser extends Pivot
{
    protected $table = 'residence_user';

    public $incrementing = true;

    protected $fillable = ['user_id', 'residence_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function residence(): BelongsTo
    {
        return $this->belongsTo(Residence::class);
    }
}
