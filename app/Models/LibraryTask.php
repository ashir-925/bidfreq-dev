<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryTask extends Model
{
    public const AMBIGUOUS = ['BF-T-027', 'BF-T-093', 'BF-T-094', 'BF-T-112', 'BF-T-117', 'BF-T-125', 'BF-T-126', 'BF-T-145'];
    public const WIDE_RANGE = ['BF-T-071', 'BF-T-072', 'BF-T-074'];

    protected $primaryKey = 'task_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'evergreen_low' => 'decimal:6', 'evergreen_typical' => 'decimal:6', 'evergreen_high' => 'decimal:6',
            'bidfreq_standard_rate' => 'decimal:6', 'reference_rate' => 'decimal:6',
            'standard_hours' => 'decimal:6', 'labor_cost_market_avg' => 'decimal:6',
            'times_seen_scopes' => 'integer',
        ];
    }
}
