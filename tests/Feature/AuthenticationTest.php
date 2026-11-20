<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function registration(): array
    {
        return [
            'first_name' => 'Jamie', 'last_name' => 'Davis', 'phone' => '555-0100', 'terms' => '1',
            'email' => 'jamie@example.test', 'password' => 'test-password',
            'password_confirmation' => 'test-password', 'role' => 'super_admin', 'status' => 'inactive',
        ];
    }

    public function test_public_registration_forces_an_active_customer(): void
    {
        Notification::fake();
        $this->post(route('register'), $this->registration())->assertRedirectToRoute('verification.notice');
        $user = User::sole();
        $this->assertSame('customer', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertTrue(Hash::check('test-password', $user->password));
        $this->get(route('dashboard'))->assertRedirectToRoute('verification.notice');

        $link = URL::temporarySignedRoute('verification.verify', now()->addMinutes(5), [
            'id' => $user->id, 'hash' => sha1($user->email),
        ]);
        $this->get($link)->assertRedirect(route('dashboard', absolute: false).'?verified=1');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public static function roles(): array
    {
        return [['customer', 'dashboard'], ['admin', 'admin.dashboard'], ['super_admin', 'admin.dashboard']];
    }

    #[DataProvider('roles')]
    public function test_login_redirects_by_role_and_saves_last_login(string $role, string $dashboard): void
    {
        $user = User::factory()->create(['role' => $role]);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirectToRoute($dashboard);
        $this->get(route($dashboard))->assertOk();
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->post(route('logout'))->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_roles_cannot_cross_panels_and_super_admin_is_hidden(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $super = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
        $this->postJson(route('admin.users.store'))->assertForbidden()->assertJsonStructure(['message']);
        $this->actingAs($admin)->get(route('dashboard'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertOk()->assertSee($customer->email)->assertDontSee($super->email);
        $this->patch(route('admin.users.update', $super), ['status' => 'inactive'])->assertForbidden();
        $this->actingAs($super)->get(route('dashboard'))->assertOk();
        $this->get(route('admin.users.index'))->assertOk()->assertDontSee($super->email);
    }

    public function test_inactive_users_cannot_log_in_or_keep_access(): void
    {
        $user = User::factory()->create(['status' => 'inactive']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'Your account is inactive. Please contact BidFreq for help.']);
        $this->assertGuest();
        $this->actingAs($user)->get(route('dashboard'))->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_staff_seeding_is_idempotent(): void
    {
        config(['staff' => [
            'admin' => ['first_name' => 'Test', 'last_name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'test-admin-password-1'],
            'super_admin' => ['first_name' => 'Test', 'last_name' => 'Owner', 'email' => 'owner@example.test', 'password' => 'test-owner-password-2'],
        ]]);
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['role' => 'admin', 'email' => 'admin@example.test']);
        $this->assertDatabaseHas('users', ['role' => 'super_admin', 'email' => 'owner@example.test']);
    }

    public function test_login_throttling_shows_the_wait(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong']);
        }
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertMatchesRegularExpression('/\d+ seconds/', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_registration_survives_mail_failure_and_logs_it(): void
    {
        $this->mock(MailChannel::class)->shouldReceive('send')->once()->andThrow(new RuntimeException('mail failed'));
        Log::shouldReceive('channel')->with('daily')->once()->andReturnSelf();
        Log::shouldReceive('error')->once()->with('BidFreq operation failed', \Mockery::on(
            fn (array $context) => $context['action'] === 'verification.send' && $context['user_id'] !== null
        ));

        $this->post(route('register'), $this->registration())->assertRedirectToRoute('verification.notice')
            ->assertSessionHas('error', 'We could not send the email, please try again');
        $this->assertDatabaseHas('users', ['email' => 'jamie@example.test']);
        $this->get(route('verification.notice'))->assertOk()->assertSee('Resend verification email');
    }

    public function test_validation_errors_render_next_to_fields(): void
    {
        $this->followingRedirects()->from(route('register'))->post(route('register'), [])
            ->assertOk()->assertSee('The first name field is required.')->assertSee('id="first_name-error"', false);
    }

    public function test_admin_can_create_admins_and_toggle_account_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $this->actingAs($admin)->from(route('admin.users.index'))
            ->post(route('admin.users.store'), $this->registration())->assertSessionHas('status');
        $this->assertDatabaseHas('users', ['role' => 'admin', 'email' => 'jamie@example.test', 'status' => 'active']);
        foreach (['inactive', 'active'] as $status) {
            $this->patch(route('admin.users.update', $customer), ['status' => $status])->assertSessionHas('status');
            $this->assertSame($status, $customer->fresh()->status);
        }
    }

    public function test_password_reset_uses_breeze_password_broker(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');
        $token = Notification::sent($user, ResetPassword::class)->sole()->token;
        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))->assertOk();
        $this->post(route('password.store'), [
            'email' => $user->email, 'token' => $token, 'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])->assertRedirectToRoute('login');
        $this->assertTrue(Hash::check('replacement-password', $user->fresh()->password));
    }

    public function test_failed_reset_mail_can_be_resent(): void
    {
        $user = User::factory()->create();
        $this->mock(MailChannel::class)->shouldReceive('send')->once()->andThrow(new RuntimeException('mail failed'));
        Log::shouldReceive('channel')->with('daily')->once()->andReturnSelf();
        Log::shouldReceive('error')->once();

        $this->from(route('password.request'))->post(route('password.email'), ['email' => $user->email])
            ->assertRedirectToRoute('password.request')
            ->assertSessionHas('error', 'We could not send the email, please try again');
        $this->get(route('password.request'))->assertSee('Resend reset email');

        Notification::fake();
        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_http_errors_have_friendly_html_and_json_responses(): void
    {
        foreach ([403, 404, 419, 429, 500] as $status) {
            \Illuminate\Support\Facades\Route::get("/test-http-error/{$status}", fn () => abort($status, 'Internal details'));
            $this->get("/test-http-error/{$status}")->assertStatus($status)->assertDontSee('Internal details');
            $this->getJson("/test-http-error/{$status}")->assertStatus($status)->assertJsonStructure(['message'])
                ->assertDontSee('Internal details');
        }
    }


    public function test_all_original_panel_pages_render_for_their_role(): void
    {
        $panels = [
            'customer' => ['dashboard', 'estimate-request', 'new-estimate-request', 'estimate_request_detail', 'projects', 'project-detail', 'notifications', 'profile'],
            'admin' => ['admin.dashboard', 'admin.estimates', 'admin.estimate-review', 'admin.projects', 'admin.customers', 'admin.estimators', 'admin.reports', 'admin.notifications', 'admin.profile', 'admin.users.index'],
        ];

        foreach ($panels as $role => $pages) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            foreach ($pages as $page) {
                $this->get(route($page))->assertOk()->assertSee('id="navToggle"', false);
            }
        }

        $this->actingAs(User::factory()->create());
        $this->get(route('estimate_request_detail'))->assertOk()
            ->assertSee('id="approveModal"', false)->assertSee('id="toastRegion"', false);
        $this->get(route('admin.estimates'))->assertForbidden();
        $this->get(route('admin.customers'))->assertForbidden();
    }


    public function test_a_password_change_revokes_an_existing_session(): void
    {
        $user = User::factory()->create();
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirectToRoute('dashboard')->assertSessionHas('password_hash_web');

        // Simulate a password reset from another browser before the first dashboard request.
        $user->forceFill(['password' => 'different-password'])->save();
        $this->app['auth']->forgetGuards();

        $this->get(route('dashboard'))->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_login_throttles_one_ip_across_different_accounts(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->postJson(route('login.store'), ['email' => "user{$i}@example.test", 'password' => 'wrong'])
                ->assertUnprocessable();
        }

        $this->postJson(route('login.store'), ['email' => 'another@example.test', 'password' => 'wrong'])
            ->assertTooManyRequests()
            ->assertHeader('Retry-After')
            ->assertJsonPath('message', fn (string $message) => preg_match('/\d+ seconds/', $message) === 1);
    }

    public function test_registration_rejects_non_string_email_and_overlong_bcrypt_password(): void
    {
        $overlong = str_repeat('é', 37);
        $this->postJson(route('register'), array_replace($this->registration(), [
            'email' => ['jamie@example.test'],
            'password' => $overlong,
            'password_confirmation' => $overlong,
        ]))->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }


    public function test_html_auth_forms_can_redisplay_invalid_nested_input(): void
    {
        foreach (['login' => 'login.store', 'register' => 'register', 'password.request' => 'password.email'] as $view => $form) {
            $this->from(route($view))->post(route($form), ['email' => ['invalid']])
                ->assertRedirectToRoute($view)->assertSessionHasErrors('email');
            $this->followingRedirects()->from(route($view))->post(route($form), ['email' => ['invalid']])
                ->assertOk()->assertSee('id="email-error"', false);
        }

        $this->get(route('password.reset', ['token' => 'placeholder', 'email' => ['invalid']]))->assertOk();
    }


    public function test_admin_seeder_does_not_require_a_super_admin_or_promote_an_existing_customer(): void
    {
        config(['staff' => [
            'admin' => ['first_name' => 'Seeded', 'last_name' => 'Admin', 'email' => 'staff@example.test', 'password' => 'test-staff-password-3'],
            'super_admin' => ['first_name' => null, 'last_name' => null, 'email' => null, 'password' => null],
        ]]);
        $this->seed(\Database\Seeders\AdminSeeder::class);
        $staff = User::sole();
        $this->assertSame('admin', $staff->role);
        $this->assertNotSame(config('staff.admin.password'), $staff->password);
        $this->assertTrue(Hash::check(config('staff.admin.password'), $staff->password));
        $this->assertNotNull($staff->email_verified_at);

        $customer = User::factory()->create();
        config(['staff.admin.email' => $customer->email]);
        try {
            $this->seed(\Database\Seeders\AdminSeeder::class);
            $this->fail('The seeder accepted an email belonging to a customer.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('different role', $e->getMessage());
        }
        $this->assertSame('customer', $customer->fresh()->role);
    }

    public function test_server_feedback_renders_a_single_toast_region_and_inline_errors(): void
    {
        $this->withSession(['status' => 'Signed out.'])->get(route('login'))->assertOk()
            ->assertSee('data-feedback-kind="success"', false)->assertSee('Signing in…');

        $this->followingRedirects()->from(route('register'))->post(route('register'), [])->assertOk()
            ->assertSee('data-feedback-kind="danger"', false)
            ->assertSee('data-error-field="first_name"', false);

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.reports'))->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'id="toastRegion"'));
    }

}
