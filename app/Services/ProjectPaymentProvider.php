<?php

namespace App\Services;

use App\Models\ProjectPayment;

interface ProjectPaymentProvider
{
    public function session(ProjectPayment $payment): array;
    public function cancel(ProjectPayment $payment): void;
    public function verify(ProjectPayment $payment): array;
}

