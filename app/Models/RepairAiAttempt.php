<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairAiAttempt extends Model
{
    protected $guarded = [];
    protected $casts = ['input' => 'array', 'run' => 'integer', 'attempt' => 'integer'];
}
