<?php

namespace Tests\Feature;

use App\Jobs\SendProjectCompletedNotice;
use App\Models\{Estimator, Project, RepairRequest, User};
use App\Services\Library\PricingLibrary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Http, Queue, Storage};
use Illuminate\Support\Str;
use Tests\TestCase;

class ProjectWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_estimate_becomes_one_private_project_and_staff_can_finish_it(): void
    {
        Http::preventStrayRequests();
        Queue::fake();
        Storage::fake('local');
        $customer = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $repair = RepairRequest::create([
            'user_id' => $customer->id, 'submission_key' => (string) Str::uuid(), 'reference' => (string) Str::uuid(),
            'street_address' => '17 Elm Street', 'city' => 'Austin', 'state' => 'TX', 'zip' => '78745',
            'home_sqft' => 1200, 'stories' => 1, 'descriptions' => [], 'dimensions' => [], 'priority' => 'normal', 'status' => 'approved',
        ]);
        $release = $repair->releases()->create(['approved_by' => $admin->id, 'version' => 1, 'snapshot' => [
            'address' => '17 Elm Street, Austin, TX 78745', 'total' => '100.00', 'lines' => [],
        ]]);
        $decision = ['decision' => 'approve', 'release_id' => $release->id, 'signature' => $customer->name, 'acceptance' => 1];
        $this->actingAs($customer)->post(route('repair-requests.decision', $repair), $decision)->assertRedirect()->assertSessionHasNoErrors();
        $project = Project::sole();
        $this->assertSame('scheduled', $project->status);
        $this->assertTrue($project->customer->is($customer));
        $this->assertTrue($project->release->is($release));
        $this->postJson(route('repair-requests.decision', $repair), $decision)->assertUnprocessable();
        $this->assertDatabaseCount('projects', 1);
        $this->get(route('project-detail', $project))->assertOk()->assertSee('17 Elm Street')->assertDontSee('Assignment and dates');
        $this->post(route('admin.projects.report', $project), [])->assertForbidden();
        $this->actingAs($other)->get(route('project-detail', $project))->assertForbidden();
        $this->get(route('projects'))->assertOk()->assertDontSee('17 Elm Street');

        $this->actingAs($admin)->get(route('admin.users.index', ['search' => $other->email]))->assertOk()->assertSee($other->email);
        $this->get(route('admin.customers'))->assertOk()->assertSee($customer->email)->assertDontSee($other->email);
        $this->get(route('admin.customers.show', $other))->assertNotFound();
        $this->post(route('admin.estimators.store'), ['name' => 'Field Contact', 'email' => 'field@example.test', 'phone' => '555-0100', 'active' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $estimator = Estimator::sole();
        $this->get(route('admin.estimators.show', $estimator))->assertOk();
        $this->patch(route('admin.estimators.update', $estimator), [
            'name' => 'Updated Field Contact', 'email' => $estimator->email, 'phone' => '555-0199', 'active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('555-0199', $estimator->fresh()->phone);
        $this->get(route('admin.estimators', ['search' => 'Updated Field']))->assertOk()->assertSee('Updated Field Contact');
        $this->patch(route('admin.projects.update', $project), ['status' => 'in_progress', 'start_date' => today()->toDateString(), 'estimator_id' => $estimator->id])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue($project->fresh()->estimator->is($estimator));
        $this->get(route('admin.projects.show', $project))->assertOk()->assertSee('Updated Field Contact');
        $replacement = Estimator::create(['name' => 'Replacement Contact', 'email' => 'replacement@example.test', 'phone' => '555-0111', 'active' => true]);
        $this->patch(route('admin.projects.update', $project), [
            'status' => 'in_progress', 'start_date' => today()->toDateString(), 'estimator_id' => $replacement->id,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue($project->fresh()->estimator->is($replacement));

        $report = ['report_date' => today()->toDateString(), 'reported_by' => 'Field Contact', 'progress' => 'Drywall installed'];
        $this->post(route('admin.projects.report', $project), $report + [
            'photos' => [UploadedFile::fake()->image('wall.jpg')], 'captions' => ['Completed wall'],
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->postJson(route('admin.projects.report', $project), $report)->assertUnprocessable();
        $file = $project->files()->sole();
        Storage::disk('local')->assertExists($file->path);
        $this->get(route('admin.reports', ['search' => 'Elm']))->assertOk()->assertSee('Drywall installed');
        $this->actingAs($other)->get(route('projects.file', [$project, $file]))->assertForbidden();
        $this->actingAs($customer)->get(route('projects.file', [$project, $file]))->assertOk();
        $this->get(route('project-detail', $project))->assertOk()->assertSee('Drywall installed')->assertDontSee('Field Contact');
        $this->actingAs($admin);
        $submissionKey = (string) Str::uuid();
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->postJson(route('admin.projects.document', $project), [
                'submission_key' => $submissionKey,
                'document' => UploadedFile::fake()->createWithContent('project-notes.txt', 'Test notes'),
            ])->assertOk()->assertJsonPath('saved', true);
        }
        $document = $project->files()->where('kind', 'document')->sole();
        Storage::disk('local')->assertExists($document->path);
        $this->actingAs($customer)->delete(route('admin.projects.files.remove', [$project, $document]))->assertForbidden();
        $this->actingAs($admin)->delete(route('admin.projects.files.remove', [$project, $document]))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('project_files', ['id' => $document->id]);
        Storage::disk('local')->assertMissing($document->path);

        $library = app(PricingLibrary::class);
        $path = base_path('bidfreq-pricing-data-v3.csv');
        $library->apply($library->preview($path, basename($path), $admin)->id, $admin);
        $this->post(route('admin.projects.orders.save', $project), ['title' => 'Additional touch-up', 'lines' => [['task_id' => 'BF-T-006', 'quantity' => '1']]])
            ->assertRedirect()->assertSessionHasNoErrors();
        $order = $project->changeOrders()->sole();
        $this->assertSame('100.00', $order->total);
        foreach (['pending', 'approved'] as $status) {
            $this->patch(route('admin.projects.orders.status', [$project, $order]), ['status' => $status])->assertRedirect()->assertSessionHasNoErrors();
        }
        $this->assertSame('100.00', $release->fresh()->snapshot['total']);
        $initial = ['reference' => (string) Str::uuid(), 'invoice_type' => 'initial', 'initial_amount' => '35.00',
            'due_date' => today()->toDateString(), 'status' => 'sent'];
        $this->post(route('admin.projects.invoice', $project), $initial)->assertRedirect()->assertSessionHasNoErrors();
        $this->post(route('admin.projects.invoice', $project), $initial)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(1, $project->invoices()->where('invoice_type', 'initial')->count());
        $additional = ['reference' => (string) Str::uuid(), 'invoice_type' => 'additional', 'orders' => [$order->id], 'due_date' => today()->toDateString(), 'status' => 'sent'];
        $this->post(route('admin.projects.invoice', $project), $additional)->assertRedirect()->assertSessionHasNoErrors();
        $this->postJson(route('admin.projects.invoice', $project), array_replace($additional, ['reference' => (string) Str::uuid()]))->assertUnprocessable();
        $this->actingAs($customer)->get(route('project-detail', $project))->assertOk()->assertSee('$200.00');

        $this->actingAs($admin)->patchJson(route('admin.projects.update', $project), ['status' => 'completed'])->assertUnprocessable();
        $this->post(route('admin.projects.walkthrough', $project), [
            'walkthrough_date' => today()->toDateString(), 'walkthrough_status' => 'complete',
            'certificate_reference' => 'CERT-001', 'certificate_date' => today()->toDateString(),
            'certificate' => UploadedFile::fake()->image('certificate.jpg'),
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->patch(route('admin.projects.update', $project), ['status' => 'completed', 'start_date' => today()->toDateString(), 'estimator_id' => $estimator->id])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('completed', $project->fresh()->status);
        $this->assertSame('completed', $repair->fresh()->status);
        Queue::assertPushed(SendProjectCompletedNotice::class);
        $this->actingAs($admin)->deleteJson(route('admin.projects.files.remove', [$project, $file]))->assertUnprocessable();
        $notice = \App\Models\AccountNotice::where('user_id', $customer->id)->where('title', 'Project created')->firstOrFail();
        $this->actingAs($other)->patch(route('notifications.read', $notice))->assertNotFound();
        $this->delete(route('notifications.delete', $notice))->assertNotFound();
        $this->actingAs($customer)->get(route('notifications', ['category' => 'projects']))->assertOk()->assertSee('Project created')->assertSee('Project status: Completed');
        $this->patch(route('notifications.read', $notice))->assertRedirect();
        $this->assertNotNull($notice->fresh()->read_at);
        $this->patch(route('notifications.read-all'))->assertRedirect();
        $this->assertSame(0, \App\Models\AccountNotice::where('user_id', $customer->id)->whereNull('read_at')->count());
        $this->delete(route('notifications.delete', $notice))->assertRedirect();
        $this->assertDatabaseMissing('account_notices', ['id' => $notice->id]);
        $this->actingAs($admin);
        $this->post(route('admin.projects.invoice', $project), [
            'reference' => (string) Str::uuid(), 'invoice_type' => 'final', 'due_date' => today()->subDay()->toDateString(), 'status' => 'sent',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $invoice = $project->invoices()->where('invoice_type', 'final')->sole();
        $this->assertSame('65.00', $invoice->total);
        $this->exerciseCardPayment($project, $invoice, $customer, $other, $admin);
    }

    private function exerciseCardPayment(Project $project, \App\Models\ProjectInvoice $invoice, User $customer, User $other, User $admin): void
    {
        config(['project_payments.stripe.key' => 'pk_test_fixture', 'project_payments.stripe.secret' => 'sk_test_fixture']);
        $reference = (string) Str::uuid();
        $intent = ['id' => 'pi_fixture', 'client_secret' => 'fixture', 'currency' => 'usd', 'amount' => 2500,
            'metadata' => ['payment_reference' => $reference]];
        $remoteStatus = 'requires_payment_method';
        $remoteError = ['code' => 'card_declined'];
        Http::fake(['https://api.stripe.com/v1/payment_intents*' => function ($request) use ($intent, &$remoteStatus, &$remoteError) {
            return Http::response($intent + [
                'status' => $remoteStatus, 'last_payment_error' => $remoteError,
                'amount_received' => $remoteStatus === 'succeeded' ? 2500 : 0,
            ]);
        }]);
        $start = route('projects.payment.start', [$project, $invoice]);
        $verify = route('projects.payment.verify', [$project, $invoice]);
        $payload = ['amount' => '25.00', 'reference' => $reference];
        $this->actingAs($other)->postJson($start, $payload)->assertForbidden();
        $this->actingAs($customer)->get(route('project-detail', $project))->assertOk()->assertSee('Enter card details');
        $this->postJson($start, ['amount' => '70.00', 'reference' => (string) Str::uuid()])->assertUnprocessable();
        $this->postJson($start, $payload)->assertOk()->assertJsonPath('client_secret', 'fixture');
        $this->postJson($start, $payload)->assertOk();
        $this->assertDatabaseCount('project_payments', 1);
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request->hasHeader('Idempotency-Key', 'bidfreq-'.$reference)
            && (int) $request['amount'] === 2500);
        $this->postJson($verify, ['status' => 'completed'])->assertOk()->assertJsonPath('status', 'failed');
        $this->assertSame('65.00', (string) $invoice->fresh()->balance());
        $remoteStatus = 'processing';
        $remoteError = null;
        $this->postJson($verify)->assertOk()->assertJsonPath('status', 'pending');
        $remoteStatus = 'succeeded';
        $this->postJson($verify)->assertOk()->assertJsonPath('status', 'completed');
        $this->postJson($verify)->assertOk()->assertJsonPath('status', 'completed');
        $this->postJson($start, $payload)->assertUnprocessable();
        $this->assertSame('40.00', (string) $invoice->fresh()->balance());
        $this->assertSame('Partially paid', $invoice->fresh()->paymentStatus());
        $this->assertTrue($invoice->fresh()->overdue());

        $record = route('admin.projects.payment', [$project, $invoice]);
        $data = ['reference' => (string) Str::uuid(), 'status' => 'refunded', 'received_on' => today()->toDateString(), 'method' => 'External card refund'];
        $this->actingAs($admin)->postJson($record, $data + ['amount' => '30.00'])->assertUnprocessable();
        $this->post($record, $data + ['amount' => '5.00'])->assertRedirect()->assertSessionHasNoErrors();
        $this->post($record, $data + ['amount' => '5.00'])->assertRedirect()->assertSessionHasNoErrors();
        $refund = $invoice->payments()->where('status', 'refunded')->sole();
        $this->patch(route('admin.projects.refund', [$project, $invoice, $refund]), [
            'received_on' => today()->toDateString(), 'method' => 'External card refund', 'external_reference' => 'EXTERNAL-REFUND',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('45.00', (string) $invoice->fresh()->balance());
        $this->assertSame('EXTERNAL-REFUND', $refund->fresh()->external_reference);
    }
}





