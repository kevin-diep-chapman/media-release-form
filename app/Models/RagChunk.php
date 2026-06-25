<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RagChunk extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'chunk_index',
        'content',
        'embedding',
        'metadata',
        'indexed_at',
    ];

    protected $casts = [
        'embedding' => 'array',
        'metadata' => 'array',
        'indexed_at' => 'datetime',
    ];
}
