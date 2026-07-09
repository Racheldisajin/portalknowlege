<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug'])]
class Domain extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Domain $domain) {
            if (empty($domain->slug)) {
                $domain->slug = Str::slug($domain->name);
            }
        });
    }

    public function knowledge(): BelongsToMany
    {
        return $this->belongsToMany(Knowledge::class);
    }
}
