<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    public const ADMIN_ACTION_STATUSES = ['draft', 'under_review', 'revision_requested', 'ai_failed'];

    public const PRIORITIES = ['low', 'normal', 'high', 'urgent'];

    public const TRADES = ['General Repair', 'Electrical', 'Plumbing', 'HVAC', 'Roofing', 'Flooring', 'Drywall / Paint', 'Foundation', 'Other'];

    public const PHOTO_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    public const DOCUMENT_MIMES = [
        'application/pdf', 'text/plain', 'application/msword', 'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'audio/mpeg', 'audio/mp4', 'audio/wav', 'audio/x-wav', 'audio/ogg',
        'video/mp4', 'video/webm', ...self::PHOTO_MIMES,
    ];

    protected $guarded = [];
    protected $casts = ['descriptions' => 'array', 'dimensions' => 'array', 'home_sqft' => 'decimal:2', 'nte' => 'decimal:2', 'ai_run' => 'integer', 'user_id' => 'integer'];

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function files()
    {
        return $this->hasMany(RepairRequestFile::class)->orderBy('id');
    }

    public function draft()
    {
        return $this->hasOne(RepairDraft::class);
    }

    public function releases()
    {
        return $this->hasMany(RepairEstimateRelease::class);
    }

    public function currentRelease()
    {
        return $this->hasOne(RepairEstimateRelease::class)->latestOfMany('version');
    }

    public function events()
    {
        return $this->hasMany(RepairEstimateEvent::class)->latest('id');
    }

    public function scopeForTrade($query, string $trade)
    {
        return match ($query->getConnection()->getDriverName()) {
            'sqlite' => $query->whereRaw("EXISTS (SELECT 1 FROM json_each(repair_requests.descriptions) WHERE json_extract(value, '$.trade') = ?)", [$trade]),
            default => $query->whereRaw("JSON_SEARCH(repair_requests.descriptions, 'one', ?, NULL, '$[*].trade') IS NOT NULL", [$trade]),
        };
    }

    public function statusClass(bool $staff = false): string
    {
        return match ($this->status) {
            'ai_pending', 'ai_retrying' => $staff ? 'status--ai-processing' : 'status--in-review',
            'ai_failed' => $staff ? 'status--declined' : 'status--in-review',
            'approved' => 'status--awaiting-customer',
            'revision_requested', 'needs_info' => 'status--revision',
            'customer_approved', 'completed' => 'status--approved',
            'customer_rejected', 'declined', 'cancelled' => 'status--declined',
            default => 'status--in-review',
        };
    }

    public function customerStatus(): string
    {
        return match ($this->status) {
            'approved' => 'Estimate Ready',
            'needs_info' => 'More Information Needed',
            'declined' => 'Declined',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'customer_approved' => 'Approved',
            'customer_rejected' => 'Rejected',
            'revision_requested' => 'Revision Requested',
            default => 'Under Review',
        };
    }

    public function attempts()
    {
        return $this->hasMany(RepairAiAttempt::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function number(): string
    {
        return 'REQ-'.strtoupper($this->reference);
    }
}
