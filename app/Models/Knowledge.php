<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Knowledge extends Model
{
    protected $fillable = [
        'title',
        'text',
        'file_path',
    ];

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class);
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KnowledgeFile::class);
    }
}
