<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairRequestFile extends Model
{
    protected $guarded = [];

    protected $casts = ['repair_request_id' => 'integer', 'bytes' => 'integer'];
}
