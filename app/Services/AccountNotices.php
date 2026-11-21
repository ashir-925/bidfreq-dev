<?php

namespace App\Services;

use App\Models\{AccountNotice, ProjectEvent, RepairDraft, RepairEstimateEvent, RepairRequest, User};
use App\Support\ReportFailure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AccountNotices
{
    public function project(ProjectEvent $event): void
    {
        $project = $event->project;
        $title = match ($event->action) {
            'project_created' => 'Project created',
            'project_updated' => 'Project details updated',
            'daily_report_added' => 'Daily progress posted',
            'document_added' => 'Project document uploaded',
            'walkthrough_updated' => 'Walkthrough updated',
            'change_order_updated' => ($event->details['after'] ?? null) === 'approved' ? 'Change Order approved' : null,
            'invoice_saved' => ($event->details['after']['status'] ?? null) === 'sent' && ($event->details['before']['status'] ?? null) !== 'sent' ? 'Invoice available' : null,
            'card_payment_verified' => match ($event->details['after'] ?? null) { 'completed' => 'Card payment confirmed', 'failed' => 'Card payment declined', default => null },
            'payment_recorded' => ($event->details['after']['status'] ?? null) === 'refunded' ? 'Refund recorded' : 'Payment record added',
            'project_file_removed' => 'Project attachment removed',
            default => null,
        };
        if (!$title) return;
        $category = in_array($event->action, ['invoice_saved', 'card_payment_verified', 'payment_recorded'], true) ? 'billing' : 'projects';
        if ($event->action === 'project_updated' && ($event->details['before']['status'] ?? null) !== ($event->details['after']['status'] ?? null)) {
            $title = 'Project status: '.Str::headline($event->details['after']['status']);
        }
        $this->publish($project->user_id, $category, $title, Str::limit($project->address, 220),
            'project-detail', 'admin.projects.show', $project, $category === 'billing' ? '#project-billing' : '');
    }

    public function estimate(RepairEstimateEvent $event): void
    {
        $title = match ($event->action) {
            'released' => 'Estimate ready to review',
            'declined' => 'Estimate request declined',
            'needs_info' => 'More information requested',
            'information_received' => 'Customer sent more information',
            'customer_approved' => 'Customer accepted an estimate',
            'customer_rejected' => 'Customer declined an estimate',
            'revision_requested' => 'Customer requested a revision',
            default => null,
        };
        if (!$title) return;
        $repair = RepairRequest::findOrFail($event->repair_request_id);
        $owner = in_array($event->action, ['released', 'declined', 'needs_info'], true) ? $repair->user_id : null;
        $this->publish($owner, 'estimates', $title, $repair->number(), 'repair-requests.show', 'admin.estimate-review', $repair);
    }

    public function request(RepairRequest $repair, string $title): void
    {
        $this->publish(null, 'estimates', $title, $repair->number(), 'repair-requests.show', 'admin.estimate-review', $repair);
    }

    public function draft(RepairDraft $draft): void
    {
        $repair = RepairRequest::findOrFail($draft->repair_request_id);
        $this->request($repair, 'Estimate draft ready for staff review');
    }

    private function publish(?int $owner, string $category, string $title, string $message, string $ownerRoute, string $staffRoute, mixed $resource, string $fragment = ''): void
    {
        $ownerLink = route($ownerRoute, $resource, false).$fragment;
        $staffLink = route($staffRoute, $resource, false).$fragment;
        DB::afterCommit(function () use ($owner, $category, $title, $message, $ownerLink, $staffLink) {
            try {
                $now = now();
                $data = ['category' => $category, 'title' => $title, 'message' => $message, 'created_at' => $now, 'updated_at' => $now];
                $staff = User::whereIn('role', ['admin', 'super_admin'])->where('status', 'active')->pluck('id');
                $rows = $staff->map(fn ($id) => $data + ['user_id' => $id, 'link' => $staffLink])->all();
                if ($owner) {
                    $rows[] = $data + ['user_id' => $owner, 'link' => $ownerLink];
                }
                if ($rows) AccountNotice::insert($rows);
            } catch (Throwable $error) {
                ReportFailure::log($error, 'notification.create');
            }
        });
    }
}

