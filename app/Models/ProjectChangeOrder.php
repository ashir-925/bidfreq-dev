<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChangeOrder extends Model
{
    protected $guarded = [];
    protected $casts = ['lines' => 'array', 'total' => 'decimal:2'];
}
