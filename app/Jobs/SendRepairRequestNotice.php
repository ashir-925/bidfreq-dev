<?php

namespace App\Jobs;

use App\Models\RepairRequest;
use App\Services\Estimates\EstimatePdf;
use App\Models\User;
use App\Support\ReportFailure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendRepairRequestNotice implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;

    public $backoff = [60, 300];

    public function __construct(public int $requestId, public string $kind, public int $recipientId, public ?int $releaseId = null) {}

    public static function notifyStaff(RepairRequest $repair, string $kind): void
    {
        User::whereIn('role', ['admin', 'super_admin'])->where('status', 'active')->whereNotNull('email_verified_at')
            ->eachById(fn (User $user) => self::dispatch($repair->id, $kind, $user->id)->onConnection(config('estimate_requests.queue_connection')));
    }

    public function handle(): void
    {
        $repair = RepairRequest::findOrFail($this->requestId);
        $recipient = User::findOrFail($this->recipientId);
        if ($recipient->status !== 'active' || ! $recipient->hasVerifiedEmail()) {
            return;
        }
        if (in_array($this->kind, ['received', 'ready', 'revised', 'needs_info', 'declined'], true) && $recipient->id !== $repair->user_id) {
            return;
        }
        if (! in_array($this->kind, ['received', 'ready', 'revised', 'needs_info', 'declined'], true) && ! in_array($recipient->role, ['admin', 'super_admin'], true)) {
            return;
        }
        [$subject, $body] = match ($this->kind) {
            'received' => ['Request received', 'Your request '.$repair->number().' has been received and is now under review. We will email you as soon as your estimate is ready.'],
            'revised' => ['Revised estimate ready', 'A revised estimate for '.$repair->number().' is ready. Sign in to review the new version.'],
            'ready' => ['Estimate ready', 'Your BidFreq estimate for '.$repair->number().' is ready. Sign in to review it and record your decision.'],
            'needs_info' => ['More information needed', 'Please sign in to respond to the information request for '.$repair->number().'.'],
            'declined' => ['Request declined', 'Please sign in for the decision and reason for '.$repair->number().'.'],
            'failed' => ['Estimate request needs manual review', 'AI analysis reached its retry limit for '.$repair->number().'. Open the Admin request inbox to review it.'],
            default => ['New estimate request', 'A '.ucfirst($repair->priority).' priority request ('.$repair->number().') is available in the Admin request inbox.'],
        };

        try {
            if ($this->releaseId !== null) {
                $release = $repair->releases()->findOrFail($this->releaseId);
                $pdf = app(EstimatePdf::class)->render($release);
                $body .= "\n".route('repair-requests.show', $repair);
                Mail::raw($body, fn ($message) => $message->to($recipient->email)
                    ->subject($subject.' · BidFreq')
                    ->attachData($pdf, 'BidFreq-estimate-v'.$release->version.'.pdf', ['mime' => 'application/pdf']));
            } else {
                Mail::raw($body, fn ($message) => $message->to($recipient->email)->subject($subject.' · BidFreq'));
            }
        } catch (Throwable $e) {
            ReportFailure::log($e, 'repair.email.'.$this->kind, $recipient->id);
            throw new \RuntimeException('Request email delivery failed.');
        }
    }
}
