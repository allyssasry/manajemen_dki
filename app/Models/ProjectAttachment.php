<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAttachment extends Model
{
    protected $fillable = [
        'project_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',   // ✅ tambahkan ini
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

     // ✅ siapa yang upload (DIG / IT)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
