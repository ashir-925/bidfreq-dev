<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairDraft extends Model
{
    protected $guarded = [];
    protected $casts = ['analysis' => 'array', 'total' => 'decimal:2', 'over_nte' => 'boolean', 'review_version' => 'integer'];

    public function lines()
    {
        return $this->hasMany(RepairDraftLine::class)->orderBy('id');
    }
}
