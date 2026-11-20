<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairDraftLine extends Model
{
    protected $guarded = [];
    protected $casts = ['evidence' => 'array', 'flags' => 'array', 'quantity' => 'decimal:6', 'rate' => 'decimal:6', 'line_total' => 'decimal:2', 'original_rate' => 'decimal:6'];

    public function task()
    {
        return $this->belongsTo(LibraryTask::class, 'task_id');
    }
}
