<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public const STATUSES = ['scheduled', 'in_progress', 'completed', 'cancelled'];

    protected $guarded = [];

    protected $casts = [
        'materials' => 'array', 'crew_contacts' => 'array', 'start_date' => 'date',
        'estimated_completion_date' => 'date', 'walkthrough_date' => 'date', 'certificate_date' => 'date',
    ];

    public function getRouteKeyName(): string { return 'reference'; }
    public function customer() { return $this->belongsTo(User::class, 'user_id'); }
    public function repairRequest() { return $this->belongsTo(RepairRequest::class); }
    public function release() { return $this->belongsTo(RepairEstimateRelease::class, 'repair_estimate_release_id'); }
    public function estimator() { return $this->belongsTo(Estimator::class); }
    public function reports() { return $this->hasMany(ProjectDailyReport::class)->latest('report_date'); }
    public function files() { return $this->hasMany(ProjectFile::class); }
    public function events() { return $this->hasMany(ProjectEvent::class)->latest('id'); }
    public function invoices() { return $this->hasMany(ProjectInvoice::class)->latest('id'); }
    public function changeOrders() { return $this->hasMany(ProjectChangeOrder::class)->latest('id'); }
}

