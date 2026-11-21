<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEvent extends Model
{
    public function project() { return $this->belongsTo(Project::class); }

    protected $guarded = [];
    protected $casts = ['details' => 'array'];
}

