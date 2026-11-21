<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Hash, Notification};
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_admin_can_update_only_their_own_profile_and_password(): void
    {
        foreach (['customer' => 'profile', 'admin' => 'admin.profile'] as $role => $route) {
            $this->flushSession();
            Notification::fake();
            $user = User::factory()->create(['role' => $role, 'password' => 'old-password']);
            $other = User::factory()->create();
            $this->actingAs($user)->get(route($route))->assertOk()->assertSee($user->email)->assertDontSee('E-Signature');
            $this->get(route($route, ['tab' => 'security']))->assertOk()->assertSee('Change password');
            $this->get(route($route, ['tab' => 'sessions']))->assertOk()->assertSee('Active sessions');
            $data = ['first_name' => 'Updated', 'last_name' => 'Account', 'phone' => '555-0101', 'email' => $user->email, 'company_name' => 'Example Property'];
            $this->patch(route($route.'.update'), $data + ['role' => 'super_admin', 'user_id' => $other->id])->assertRedirect()->assertSessionHasNoErrors();
            $this->assertSame('Updated', $user->fresh()->first_name);
            $this->assertSame($role, $user->fresh()->role);
            $this->assertSame($other->first_name, $other->fresh()->first_name);
            $this->assertSame($role === 'customer' ? 'Example Property' : $user->company_name, $user->fresh()->company_name);
            $this->putJson(route($route.'.password'), ['current_password' => 'wrong', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertUnprocessable();
            $this->put(route($route.'.password'), ['current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'new-password'])
                ->assertRedirect(route($route, ['tab' => 'security']))->assertSessionHasNoErrors();
            $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
            $data['email'] = $role.'-updated@example.test';
            $this->patchJson(route($route.'.update'), $data)->assertUnprocessable()->assertJsonValidationErrors('current_password');
            $this->patch(route($route.'.update'), $data + ['current_password' => 'new-password'])->assertRedirect(route('verification.notice'));
            $this->assertNull($user->fresh()->email_verified_at);
            Notification::assertSentTo($user, VerifyEmail::class);
        }
    }

    public function test_admin_can_upload_view_and_replace_their_private_profile_photo(): void
    {
        $disk = \Illuminate\Support\Facades\Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create();
        $upload = route('admin.profile.update');
        $details = ['first_name' => 'Photo', 'last_name' => $admin->last_name, 'email' => $admin->email, 'phone' => $admin->phone];
        $photo = route('admin.profile.photo');

        $this->actingAs($admin)->patchJson($upload, $details + ['photo' => \Illuminate\Http\UploadedFile::fake()->create('notes.txt')])
            ->assertUnprocessable()->assertJsonValidationErrors('photo');
        $this->post($upload, $details + ['_method' => 'PATCH', 'photo' => \Illuminate\Http\UploadedFile::fake()->image('photo.jpg')])
            ->assertRedirect(route('admin.profile'))->assertSessionHasNoErrors();
        $first = $admin->fresh()->profile_photo_path;
        $disk->assertExists($first);
        $this->assertSame('Photo', $admin->fresh()->first_name);

        $this->get(route('admin.profile'))->assertOk()
            ->assertSee('class="admin-profile__photo"', false)
            ->assertSee('class="topbar__account-photo"', false)
            ->assertSee('title="Photo '.$admin->last_name.'"', false);
        $this->get($photo)->assertOk()->assertHeader('Content-Type', 'image/jpeg');

        $this->post($upload, $details + ['_method' => 'PATCH', 'photo' => \Illuminate\Http\UploadedFile::fake()->image('replacement.png')])
            ->assertRedirect(route('admin.profile'))->assertSessionHasNoErrors();
        $disk->assertMissing($first);
        $disk->assertExists($admin->fresh()->profile_photo_path);

        $this->flushSession();
        $this->actingAs($otherAdmin)->get($photo)->assertNotFound();
        $this->flushSession();
        $this->actingAs($customer)->get($photo)->assertForbidden();
        $this->patch($upload)->assertForbidden();
    }

    public function test_customer_can_revoke_their_other_database_session_only(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create();
        $other = User::factory()->create();
        foreach (['own-browser' => $user, 'another-browser' => $other] as $id => $owner) {
            DB::table('sessions')->insert(['id' => $id, 'user_id' => $owner->id, 'payload' => '', 'last_activity' => time(), 'user_agent' => $id]);
        }
        $this->actingAs($user)->get(route('profile', ['tab' => 'sessions']))->assertOk()->assertSee('own-browser')->assertDontSee('another-browser');
        $this->delete(route('profile.sessions.revoke', 'another-browser'))->assertNotFound();
        $this->assertDatabaseHas('sessions', ['id' => 'another-browser']);
        $this->delete(route('profile.sessions.revoke', 'own-browser'))->assertRedirect(route('profile', ['tab' => 'sessions']));
        $this->assertDatabaseMissing('sessions', ['id' => 'own-browser']);
    }
}
