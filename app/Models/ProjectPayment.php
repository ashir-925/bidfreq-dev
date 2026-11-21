<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $guarded = [];
    protected $casts = ['amount' => 'decimal:2', 'received_on' => 'date'];
}
