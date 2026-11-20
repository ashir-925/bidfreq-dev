<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairEstimateRelease extends Model
{
    protected $guarded = [];
    protected $casts = ['snapshot' => 'array', 'version' => 'integer', 'decided_at' => 'datetime'];
}
