<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Model;

class ProjectInvoice extends Model
{
    protected $guarded = [];
    protected $casts = ['lines' => 'array', 'due_date' => 'date', 'total' => 'decimal:2'];

    public function project() { return $this->belongsTo(Project::class); }
    public function payments() { return $this->hasMany(ProjectPayment::class)->latest('id'); }

    public function netPaid(): BigDecimal
    {
        return $this->payments->reduce(fn (BigDecimal $net, ProjectPayment $payment) => match ($payment->status) {
            'completed' => $net->plus($payment->amount),
            'refunded' => $net->minus($payment->amount),
            default => $net,
        }, BigDecimal::zero()->toScale(2));
    }

    public function balance(): BigDecimal { return BigDecimal::of($this->total)->minus($this->netPaid()); }

    public function paymentStatus(): string
    {
        return match (true) {
            $this->netPaid()->isZero() => 'Unpaid',
            $this->balance()->isPositive() => 'Partially paid',
            default => 'Paid',
        };
    }

    public function overdue(): bool
    {
        return $this->status === 'sent' && $this->due_date->lt(today()) && $this->balance()->isPositive();
    }
}
