<?php

namespace Tests\Feature;

use App\Jobs\AnalyzeRepairRequest;
use App\Jobs\SendRepairRequestNotice;
use App\Models\RepairRequest;
use App\Models\User;
use App\Services\Estimates\DraftPricing;
use App\Services\Estimates\EstimateAiProvider;
use App\Services\Library\PricingLibrary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class RepairRequestTest extends TestCase
{
    use RefreshDatabase;

    private \Illuminate\Queue\QueueManager $realQueue;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->realQueue = Queue::getFacadeRoot();
        Queue::fake();
        Http::preventStrayRequests();
        config(['estimate_requests.gemini_key' => 'test-key', 'estimate_requests.max_attempts' => 2]);
    }

    public function test_intake_validates_and_saves_once_and_queues_notices_and_analysis(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($customer)->get(route('new-estimate-request'))->assertOk()->assertSee('Total home area');
        $this->postJson(route('repair-requests.store'))->assertUnprocessable()
            ->assertJsonValidationErrors(['photos', 'home_sqft', 'stories', 'descriptions']);
        $invalid = $this->payload();
        $invalid['photos'] = [UploadedFile::fake()->image('one.jpg')];
        $this->postJson(route('repair-requests.store'), $invalid)->assertUnprocessable()->assertJsonValidationErrors('photos');

        $key = (string) Str::uuid();
        $this->postJson(route('repair-requests.store'), $this->payload($key))->assertCreated();
        $repair = RepairRequest::sole();
        $this->postJson(route('repair-requests.store'), $this->payload($key))->assertCreated()
            ->assertJsonPath('redirect', route('repair-requests.show', $repair));
        $this->assertDatabaseCount('repair_requests', 1);
        $this->assertDatabaseCount('repair_request_files', 3);
        $this->assertSame('ai_pending', $repair->status);
        $this->assertSame('15.00', $repair->nte);
        Queue::assertPushed(AnalyzeRepairRequest::class, 1);
        Queue::assertPushed(SendRepairRequestNotice::class, 2);
        Queue::assertPushed(SendRepairRequestNotice::class, fn ($job) => $job->recipientId === $admin->id && $job->kind === 'new');

        Mail::shouldReceive('raw')->once()->withArgs(fn ($body, $callback) => str_contains($body, $repair->number()));
        (new SendRepairRequestNotice($repair->id, 'received', $customer->id))->handle();

        $attachment = $repair->files()->firstOrFail();
        Storage::disk('local')->assertExists($attachment->path);
        $this->get(route('repair-requests.download', [$repair, $attachment]))->assertDownload('first.jpg');
        $this->get(route('estimate-request'))->assertOk()->assertSee($repair->number());
        $this->get(route('repair-requests.show', $repair))->assertOk()->assertSee('Under Review')->assertDontSee('ai_pending');

        $this->actingAs(User::factory()->create())->get(route('repair-requests.show', $repair))->assertForbidden();
        $this->get(route('repair-requests.download', [$repair, $attachment]))->assertForbidden();
        $this->get(route('admin.repair-requests.index'))->assertForbidden();

        $this->actingAs($admin)->get(route('admin.repair-requests.show', $repair))->assertOk()->assertSee('First test room');
        $this->get(route('admin.repair-requests.download', [$repair, $attachment]))->assertDownload('first.jpg');
    }

    public function test_gemini_draft_prices_from_library_flags_items_and_stays_private(): void
    {
        $admin = $this->importLibrary();
        $customer = User::factory()->create();
        $this->actingAs($customer)->postJson(route('repair-requests.store'), $this->payload())->assertCreated();
        $repair = RepairRequest::sole();

        $item = ['task_id' => 'BF-T-001', 'unit' => 'per sq ft of floor', 'quantity' => '20.004', 'quantity_basis' => 'customer_measurement', 'evidence' => ['Measured first room'], 'rate' => 999];
        $output = $this->sampleAnalysis([
            $item,
            array_replace($item, ['task_id' => 'BF-T-999']),
            array_replace($item, ['unit' => 'each']),
            array_replace($item, ['quantity' => null, 'quantity_basis' => 'cannot_determine']),
            array_replace($item, ['task_id' => 'BF-T-079', 'unit' => 'per job (flat)', 'quantity' => '1']),
        ]);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => json_encode($output)]]]]],
        ])]);
        $job = (new AnalyzeRepairRequest($repair->id, 1))->withFakeQueueInteractions();
        $job->handle(app(EstimateAiProvider::class), app(DraftPricing::class));
        $job->handle(app(EstimateAiProvider::class), app(DraftPricing::class));

        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            $submitted = json_decode($request['contents'][0]['parts'][0]['text'], true);
            return $request->hasHeader('x-goog-api-key', 'test-key')
                && count($submitted['library']) === 162
                && array_keys($submitted['library'][0]) === ['task_id', 'task_name', 'unit']
                && ! str_contains(json_encode($submitted), '123 Sample Lane')
                && count($request['contents'][0]['parts']) === 7;
        });
        $draft = $repair->draft()->firstOrFail();
        $this->assertSame('25.01', $draft->total);
        $this->assertTrue($draft->over_nte);
        $this->assertCount(6, $draft->lines);
        $this->assertSame('1.250000', $draft->lines[0]->rate);
        $this->assertNotNull($draft->lines[0]->library_version_id);
        $this->assertSame(1, $draft->lines->whereNotNull('line_total')->count());
        $this->assertArrayNotHasKey('total', $draft->analysis);
        $this->assertStringContainsString('999', $repair->attempts()->sole()->response);
        $this->assertDatabaseCount('repair_drafts', 1);

        $this->get(route('repair-requests.show', $repair))->assertOk()->assertDontSee('Private draft cause')->assertDontSee('25.01');
        $this->actingAs($admin)->get(route('admin.repair-requests.index'))->assertOk()->assertSee('Over NTE');
        $this->get(route('admin.repair-requests.show', $repair))->assertOk()->assertSee('Private draft cause')->assertSee('25.01');
        $this->patch(route('admin.repair-requests.nte', $repair), ['nte' => '35.00'])->assertRedirect();
        $this->assertFalse($draft->fresh()->over_nte);
    }

    public function test_retry_limit_reuses_evidence_and_staff_can_retry_or_build_manual_draft(): void
    {
        $admin = $this->importLibrary();
        $this->actingAs(User::factory()->create())->postJson(route('repair-requests.store'), $this->payload())->assertCreated();
        $repair = RepairRequest::sole();
        Http::fake(['*' => Http::sequence()->pushStatus(429)->push(['candidates' => []])->pushStatus(503)->pushStatus(503)]);

        $job = (new AnalyzeRepairRequest($repair->id, 1))->withFakeQueueInteractions();
        $job->handle(app(EstimateAiProvider::class), app(DraftPricing::class));
        $job->assertReleased(60);
        $this->assertSame('ai_retrying', $repair->fresh()->status);
        $job->handle(app(EstimateAiProvider::class), app(DraftPricing::class));

        $this->assertSame('ai_failed', $repair->fresh()->status);
        $this->assertSame($repair->attempts()->orderBy('id')->first()->input, $repair->attempts()->latest('id')->first()->input);
        $this->assertDatabaseCount('repair_drafts', 0);
        Queue::assertPushed(SendRepairRequestNotice::class, fn ($job) => $job->kind === 'failed');
        $this->get(route('repair-requests.show', $repair))->assertOk()->assertSee('Under Review')->assertDontSee('AI failed');

        $this->actingAs($admin)->get(route('admin.repair-requests.show', $repair))->assertOk()->assertSee('Prepare a manual draft');
        $this->post(route('admin.repair-requests.retry', $repair))->assertRedirect();
        $this->assertSame(2, $repair->fresh()->ai_run);
        Queue::assertPushed(AnalyzeRepairRequest::class, fn ($job) => $job->run === 2);

        $retry = (new AnalyzeRepairRequest($repair->id, 2))->withFakeQueueInteractions();
        $retry->handle(app(EstimateAiProvider::class), app(DraftPricing::class));
        $retry->handle(app(EstimateAiProvider::class), app(DraftPricing::class));
        $payload = array_intersect_key($this->sampleAnalysis([]), array_flip(['complaint', 'cause', 'correction', 'complications']));
        $this->postJson(route('admin.repair-requests.manual', $repair), $payload + [
            'lines' => [['task_id' => 'BF-T-079', 'quantity' => '1']],
        ])->assertUnprocessable()->assertJsonValidationErrors('lines.0.rate');
        $this->post(route('admin.repair-requests.manual', $repair), $payload + [
            'lines' => [['task_id' => 'BF-T-079', 'quantity' => '2', 'rate' => '11.25']],
        ])->assertRedirect();
        $this->assertSame('22.50', $repair->draft()->sole()->total);
        $this->assertSame('manual', $repair->draft()->sole()->lines()->sole()->source);
    }

    public function test_database_queue_runs_only_after_submission_returns(): void
    {
        $this->importLibrary();
        Queue::swap($this->realQueue);
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => json_encode($this->sampleAnalysis([]))]]]]],
        ])]);

        $this->actingAs(User::factory()->create())->postJson(route('repair-requests.store'), $this->payload())->assertCreated();
        Http::assertNothingSent();
        $this->assertDatabaseCount('jobs', 3);

        $queue = Queue::connection('database');
        $customerNotice = $queue->pop();
        $staffNotice = $queue->pop();
        $this->assertSame(SendRepairRequestNotice::class, $customerNotice->resolveName());
        $this->assertSame(SendRepairRequestNotice::class, $staffNotice->resolveName());
        $customerNotice->fire();
        $staffNotice->fire();
        Http::assertNothingSent();

        $job = $queue->pop();
        $this->assertSame(AnalyzeRepairRequest::class, $job->resolveName());
        $job->fire();

        Http::assertSentCount(1);
        $this->assertSame('draft', RepairRequest::sole()->status);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_staff_review_release_revision_and_customer_signature_use_versioned_real_prices(): void
    {
        $admin = $this->importLibrary();
        $customer = User::factory()->create();
        $this->actingAs($customer)->postJson(route('repair-requests.store'), $this->payload())->assertCreated();
        $repair = RepairRequest::sole();
        $item = ['task_id' => 'BF-T-003', 'unit' => 'per sq ft of floor', 'quantity' => '1500', 'quantity_basis' => 'customer_measurement', 'evidence' => ['Total floor area']];
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => json_encode($this->sampleAnalysis([$item]))]]]]],
        ])]);
        (new AnalyzeRepairRequest($repair->id, 1))->withFakeQueueInteractions()->handle(app(EstimateAiProvider::class), app(DraftPricing::class));
        $draft = $repair->draft()->sole();
        [$priced, $unmatched] = $draft->lines;
        $this->assertSame('1125.00', $draft->total);
        $this->get(route('repair-requests.show', $repair))->assertOk()->assertDontSee('1125.00');
        $this->postJson(route('admin.repair-requests.release', $repair), ['review_version' => 0])->assertForbidden();

        $this->actingAs($admin)->get(route('admin.estimates'))->assertOk()->assertSee($repair->number());
        $this->get(route('admin.estimate-review', $repair))->assertOk()->assertSee('There is no matching Library task for this line');
        $this->postJson(route('admin.repair-requests.release', $repair), ['review_version' => 0])->assertUnprocessable()->assertJsonValidationErrors('approval');
        $review = [
            'review_version' => 0, 'complaint' => 'Worn painted walls', 'cause' => 'Wear',
            'correction' => 'Paint interior walls with one coat', 'complications' => 'Confirm access',
            'review_note' => 'Reviewed dimensions and excluded unrelated work',
            'lines' => [
                ['line_id' => $priced->id, 'task_id' => 'BF-T-003', 'quantity' => '1500', 'rate' => '0.80'],
                ['line_id' => $unmatched->id, 'exclude' => true],
            ],
        ];
        $this->patchJson(route('admin.repair-requests.save', $repair), $review)->assertUnprocessable()->assertJsonValidationErrors('lines.0.override_reason');
        $review['lines'][0]['override_reason'] = 'Approved staff adjustment';
        $this->patch(route('admin.repair-requests.save', $repair), $review)->assertRedirectToRoute('admin.estimate-review', $repair);
        $this->assertSame('0.750000', $draft->lines()->sole()->original_rate);
        $this->post(route('admin.repair-requests.release', $repair), ['review_version' => 1])->assertRedirect();
        $first = $repair->releases()->sole();
        $this->assertSame('1200.00', $first->snapshot['total']);
        Queue::assertPushed(SendRepairRequestNotice::class, fn ($job) => $job->kind === 'ready' && $job->releaseId === $first->id);

        $this->actingAs(User::factory()->create())->get(route('repair-requests.pdf', [$repair, $first]))->assertForbidden();
        $this->actingAs($customer)->get(route('repair-requests.show', $repair))->assertOk()->assertSee('1200.00')->assertSee('approveModal');
        $pdf = $this->get(route('repair-requests.pdf', [$repair, $first]))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
        $decision = route('repair-requests.decision', $repair);
        $this->postJson($decision, ['release_id' => $first->id, 'decision' => 'revise'])->assertUnprocessable()->assertJsonValidationErrors('comment');
        $this->post($decision, ['release_id' => $first->id, 'decision' => 'revise', 'comment' => 'Revise the measured floor area.'])->assertRedirect();

        $this->actingAs($admin)->postJson(route('admin.repair-requests.release', $repair), ['review_version' => 1])->assertUnprocessable();
        $review['review_version'] = 1;
        $review['lines'] = [['line_id' => $draft->lines()->sole()->id, 'task_id' => 'BF-T-003', 'quantity' => '1200']];
        $this->patch(route('admin.repair-requests.save', $repair), $review)->assertRedirect();
        $this->post(route('admin.repair-requests.release', $repair), ['review_version' => 2])->assertRedirect();
        $second = $repair->fresh()->currentRelease;
        $this->assertSame(2, $second->version);
        $this->assertSame('900.00', $second->snapshot['total']);
        $this->assertSame('1200.00', $first->fresh()->snapshot['total']);

        $this->actingAs($customer);
        $signature = ['decision' => 'approve', 'signature' => 'Sample Customer', 'acceptance' => '1'];
        $this->postJson($decision, $signature + ['release_id' => $first->id])->assertUnprocessable();
        $this->postJson($decision, ['decision' => 'approve', 'release_id' => $second->id, 'signature' => 'Sample Customer'])->assertUnprocessable()->assertJsonValidationErrors('acceptance');
        $this->post($decision, $signature + ['release_id' => $second->id])->assertRedirect();
        $this->postJson($decision, $signature + ['release_id' => $second->id])->assertUnprocessable();
        $this->assertSame('customer_approved', $repair->fresh()->status);
        $this->assertSame('Sample Customer', $repair->events()->first()->details['signature']);
    }

    public function test_estimate_pages_keep_internal_state_private_and_staff_can_request_information_or_decline(): void
    {
        $staff = $this->importLibrary();
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $payload = $this->payload();
        $payload['home_sqft'] = '6';
        $payload['dimensions'] = [['label' => 'Kitchen', 'length' => '10', 'width' => '8', 'area' => '40']];
        $this->actingAs($owner)->postJson(route('repair-requests.store'), $payload)->assertCreated();
        $repair = RepairRequest::sole();
        $image = $repair->files()->firstOrFail();

        $this->get(route('estimate-request', ['trade' => 'Drywall / Paint']))->assertOk()->assertSee($repair->number());
        $this->get(route('estimate-request', ['trade' => 'Roofing']))->assertOk()->assertDontSee($repair->number());
        $this->get(route('estimate_request_detail', $repair))->assertNotFound();
        $this->get(route('repair-requests.show', $repair))->assertOk()->assertSee('Under Review')->assertDontSee('ai_pending')->assertDontSee('Not-to-Exceed');
        $this->actingAs($other)->get(route('repair-requests.image', [$repair, $image]))->assertForbidden();
        $this->get(route('repair-requests.show', $repair))->assertForbidden();
        $this->get(route('admin.estimate-review', $repair))->assertForbidden();

        $item = ['task_id' => 'BF-T-001', 'unit' => 'per sq ft of floor', 'quantity' => '10.000000',
            'quantity_basis' => 'customer_measurement', 'evidence' => ['Customer dimensions']];
        $analysis = array_replace($this->sampleAnalysis([$item]), ['unmatched' => []]);
        Http::fake(['*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => json_encode($analysis)]]]]]])]);
        (new AnalyzeRepairRequest($repair->id, 1))->withFakeQueueInteractions()->handle(app(EstimateAiProvider::class), app(DraftPricing::class));

        $this->actingAs($staff)->get(route('admin.estimates', ['trade' => 'Drywall / Paint', 'property' => '78745']))->assertOk()->assertSee($repair->number());
        $this->get(route('admin.estimate-review', $repair))->assertOk()
            ->assertSee('The reported home area is unusually small')->assertSee('length × width does not match')
            ->assertSee('Search task name or ID')->assertSee('$1.25');
        $this->get(route('admin.repair-requests.image', [$repair, $image]))->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->post(route('admin.repair-requests.disposition', $repair), ['action' => 'needs_info', 'message' => 'Confirm the full home floor area.'])->assertRedirect();
        $this->assertSame('needs_info', $repair->fresh()->status);
        Queue::assertPushed(SendRepairRequestNotice::class, fn ($job) => $job->kind === 'needs_info');

        $this->actingAs($owner)->get(route('repair-requests.show', $repair))->assertOk()->assertSee('More Information Needed')->assertSee('Confirm the full home floor area.');
        $this->postJson(route('repair-requests.respond', $repair), [])->assertUnprocessable();
        $this->post(route('repair-requests.respond', $repair), ['message' => 'The full home is 1,200 square feet.',
            'files' => [UploadedFile::fake()->image('additional.jpg')]])->assertRedirect();
        $this->assertSame('under_review', $repair->fresh()->status);
        $this->assertDatabaseHas('repair_request_files', ['repair_request_id' => $repair->id, 'name' => 'additional.jpg']);
        $this->assertSame('6.00', $repair->fresh()->home_sqft);

        $this->actingAs($staff)->post(route('admin.repair-requests.disposition', $repair), [
            'action' => 'declined', 'message' => 'This request needs a fresh inspection.',
        ])->assertRedirect();
        $this->actingAs($owner)->get(route('repair-requests.show', $repair))->assertOk()->assertSee('Declined')->assertSee('This request needs a fresh inspection.');
    }

    public function test_admin_review_numbers_and_edits_only_evidence_from_the_current_request(): void
    {
        $admin = $this->importLibrary();
        $other = User::factory()->create();
        $customer = User::factory()->create();
        $this->actingAs($other)->postJson(route('repair-requests.store'), $this->payload())->assertCreated();
        $foreignFile = RepairRequest::where('user_id', $other->id)->firstOrFail()->files()->firstOrFail();
        $this->actingAs($customer)->postJson(route('repair-requests.store'), $this->payload())->assertCreated();
        $repair = RepairRequest::where('user_id', $customer->id)->firstOrFail();
        [$first, $second] = $repair->files;
        $analysis = $this->sampleAnalysis([[
            'task_id' => 'BF-T-001', 'unit' => 'per sq ft of floor', 'quantity' => '20',
            'quantity_basis' => 'customer_measurement', 'evidence' => ['Attachment #2 shows the repair.'],
        ]]);
        $analysis['evidence_links'] = ['complaint' => [2, 999], 'cause' => [1], 'correction' => [], 'complications' => []];
        $analysis['unmatched'] = [];
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => json_encode($analysis)]]]]],
        ])]);
        (new AnalyzeRepairRequest($repair->id, 1))->withFakeQueueInteractions()->handle(app(EstimateAiProvider::class), app(DraftPricing::class));
        $draft = $repair->draft()->sole();
        $this->assertSame([$second->id], $draft->analysis['evidence_links']['complaint']);
        $this->assertSame([$first->id], $draft->analysis['evidence_links']['cause']);
        $this->get(route('repair-requests.image', [RepairRequest::where('user_id', $other->id)->firstOrFail(), $foreignFile]))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.repair-requests.image', [$repair, $foreignFile]))->assertNotFound();
        $this->get(route('admin.estimate-review', $repair))->assertOk()
            ->assertSee('id="submitted-evidence"', false)->assertSee('href="#submitted-evidence"', false)
            ->assertSee('id="review-evidence-preview"', false)->assertSee('Preview attachment #1: first.jpg')
            ->assertSee('Preview attachment #2: second.jpg')->assertSee('Attachment #2 shows the repair.')
            ->assertSee('Data v3 · Library import #')->assertSee('$1.25')->assertSee('value="20"', false)
            ->assertSee('id="review-tasks"', false)->assertSee('data-task-id="BF-T-001"', false)
            ->assertDontSee('multiple', false);
        $change = [
            'review_version' => 0, 'complaint' => 'Damaged wall', 'cause' => 'Impact', 'correction' => 'Patch',
            'complications' => 'Confirm paint match', 'review_note' => 'Checked submitted attachments',
            'lines' => [['line_id' => $draft->lines()->sole()->id, 'task_id' => 'BF-T-001', 'quantity' => '20']],
            'evidence_links' => ['correction' => [$foreignFile->id]],
        ];
        $this->patchJson(route('admin.repair-requests.save', $repair), $change)
            ->assertUnprocessable()->assertJsonValidationErrors('evidence_links.correction.0');
        $change['evidence_links'] = ['correction' => [$first->id, $second->id]];
        $this->patchJson(route('admin.repair-requests.save', $repair), $change)->assertRedirect();
        $this->assertSame($change['evidence_links'], $draft->fresh()->analysis['evidence_links']);
        $this->assertSame('25.00', $draft->fresh()->total);
    }

    public function test_estimate_display_formats_decimal_values_without_changing_them(): void
    {
        $display = \App\Services\Estimates\EstimateDisplay::class;
        $this->assertSame('123.40', $display::money('123.400000'));
        $this->assertSame('8.125', $display::quantity('8.125000'));
        $this->assertSame('$20.00', $display::currency('20.000000'));
        $this->assertSame('1.24', $display::hours('1.236000'));
        $this->assertSame('100', $display::quantity('100.000000'));
        $this->assertSame('—', $display::money(null));
    }

    private function payload(?string $key = null): array
    {
        return [
            'submission_key' => $key ?? (string) Str::uuid(),
            'street_address' => '123 Sample Lane', 'city' => 'Austin', 'state' => 'TX', 'zip' => '78745',
            'home_sqft' => '1500', 'stories' => 2, 'priority' => 'high', 'nte' => '15.00',
            'descriptions' => [['trade' => 'Drywall / Paint', 'description' => 'First test room']],
            'dimensions' => [['label' => 'First room', 'area' => '20.004']],
            'photos' => [UploadedFile::fake()->image('first.jpg'), UploadedFile::fake()->image('second.jpg')],
            'photo_trades' => ['Drywall / Paint', 'Drywall / Paint'],
            'documents' => [UploadedFile::fake()->createWithContent('note.txt', 'Sample supporting evidence.')],
            'consent' => '1',
        ];
    }

    private function sampleAnalysis(array $items): array
    {
        return [
            'complaint' => 'Sample complaint', 'cause' => 'Private draft cause',
            'correction' => 'Sample repair', 'complications' => 'Sample complications',
            'uncertainty' => 'Confirm on review.', 'confidence' => 0.7,
            'items' => $items, 'unmatched' => ['Unmatched sample work'], 'assumptions' => [], 'risk_flags' => [],
            'total' => 999,
        ];
    }

    private function importLibrary(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $library = app(PricingLibrary::class);
        $path = base_path('bidfreq-pricing-data-v3.csv');
        $library->apply($library->preview($path, basename($path), $admin)->id, $admin);

        return $admin;
    }
}
