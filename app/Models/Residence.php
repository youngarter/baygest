<?php

namespace App\Models;

use Database\Factories\ResidenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Residence extends Model implements Auditable
{
    /** @use HasFactory<ResidenceFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'description',
        'avatar',
        'images',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Residence $residence) {
            if (empty($residence->uuid)) {
                $residence->uuid = (string) Str::uuid();
            }

            if (empty($residence->slug)) {
                $residence->slug = static::generateUniqueSlug($residence->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ResidenceUser::class)
            ->withTimestamps();
    }

    protected $auditInclude = [
        'name',
        'slug',
        'address',
        'description',
    ];

    protected $auditExclude = [
        'uuid',
        'avatar',
        'images',
    ];
}
