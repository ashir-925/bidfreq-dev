<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = config('staff');

        if (collect($accounts['super_admin'])->every(fn ($value) => blank($value))) {
            unset($accounts['super_admin']);
        }

        foreach ($accounts as $role => &$account) {
            $account['email'] = strtolower(trim((string) $account['email']));
            $length = strlen((string) $account['password']);

            if (! filter_var($account['email'], FILTER_VALIDATE_EMAIL)
                || blank($account['first_name']) || blank($account['last_name'])
                || $length < 16 || $length > 72) {
                throw new RuntimeException("Configure the {$role} account with a name, valid email and a unique password of 16 to 72 bytes.");
            }
        }

        unset($account);

        if (isset($accounts['super_admin']) && $accounts['admin']['email'] === $accounts['super_admin']['email']) {
            throw new RuntimeException('Seed accounts must have different email addresses.');
        }

        if (isset($accounts['super_admin']) && $accounts['admin']['password'] === $accounts['super_admin']['password']) {
            throw new RuntimeException('Seed accounts must have different passwords.');
        }

        DB::transaction(function () use ($accounts) {
            foreach ($accounts as $role => $account) {
                $existing = User::where('email', $account['email'])->lockForUpdate()->first();

                if ($existing && $existing->role !== $role) {
                    throw new RuntimeException("The {$role} seed email belongs to a different role.");
                }

                $account['password'] = $existing && Hash::check($account['password'], $existing->password)
                    ? $existing->password
                    : Hash::make($account['password']);

                User::unguarded(fn () => User::updateOrCreate(['email' => $account['email']], $account + [
                    'role' => $role,
                    'status' => $existing?->status ?? 'active',
                    'email_verified_at' => $existing?->email_verified_at ?? now(),
                ]));
            }
        });
    }
}
