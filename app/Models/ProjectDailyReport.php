<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDailyReport extends Model
{
    protected $guarded = [];
    protected $casts = ['report_date' => 'date'];

    public function project() { return $this->belongsTo(Project::class); }
    public function files() { return $this->hasMany(ProjectFile::class); }
}
