<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'file_path',
        'row_count',
        'stats_json',
        'preview_json',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function getStatsAttribute()
    {
        return json_decode($this->stats_json ?? "{}", true) ?: [];
    }

    public function getPreviewAttribute()
    {
        return json_decode($this->preview_json ?? "[]", true) ?: [];
    }
}
