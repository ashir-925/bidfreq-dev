<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimator extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'active'];
    protected $casts = ['active' => 'boolean'];

    public function projects() { return $this->hasMany(Project::class); }
}
