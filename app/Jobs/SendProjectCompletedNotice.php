<?php

namespace App\Jobs;

use App\Models\Project;
use App\Support\ReportFailure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class SendProjectCompletedNotice implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public function __construct(public int $projectId) {}

    public function handle(): void
    {
        $project = Project::with('customer')->findOrFail($this->projectId);
        $customer = $project->customer;
        if ($project->status !== 'completed' || $customer->status !== 'active' || ! $customer->hasVerifiedEmail()) {
            return;
        }

        try {
            Mail::raw("Your BidFreq project at {$project->address} is complete.\n".route('project-detail', $project),
                fn ($mail) => $mail->to($customer->email)->subject('BidFreq project completed'));
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'project.completion_email', $customer->id);
            throw new RuntimeException('Project completion email delivery failed.');
        }
    }
}
