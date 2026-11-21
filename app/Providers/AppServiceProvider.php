<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Services\ProjectPaymentProvider::class, fn ($app) => match (config('project_payments.provider')) {
            'stripe' => $app->make(\App\Services\StripeProjectPayments::class),
            default => throw new \RuntimeException('Payment provider is not configured.'),
        });
    }

    public function boot(): void
    {
        $notices = $this->app->make(\App\Services\AccountNotices::class);
        \App\Models\ProjectEvent::created($notices->project(...));
        \App\Models\RepairEstimateEvent::created($notices->estimate(...));
        \App\Models\RepairDraft::created($notices->draft(...));
        \App\Models\RepairRequest::created(fn ($repair) => $notices->request($repair, 'New estimate request'));
        \App\Models\RepairRequest::updated(function ($repair) use ($notices) {
            if ($repair->wasChanged('status') && $repair->status === 'ai_failed') {
                $notices->request($repair, 'Estimate analysis needs staff review');
            }
        });

        Password::defaults(fn () => Password::min(8)->rules([
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_string($value) && strlen($value) > 72) {
                    $fail('The password must be no longer than 72 bytes.');
                }
            },
        ]));

        Gate::before(fn (User $user) => $user->role === 'super_admin' ? true : null);
        Gate::define('access-panel', fn (User $user, string $panel) => $user->role === $panel);
    }
}
