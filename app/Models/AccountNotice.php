<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountNotice extends Model
{
    protected $guarded = [];
    protected $casts = ['read_at' => 'datetime'];
}
