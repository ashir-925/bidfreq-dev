<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
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
