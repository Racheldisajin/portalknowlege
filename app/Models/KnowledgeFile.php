<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeFile extends Model
{
    protected $fillable = [
        'knowledge_id',
        'file_path',
        'text',
    ];

    public function knowledge(): BelongsTo
    {
        return $this->belongsTo(Knowledge::class);
    }
}
