<?php

namespace Tests\Feature;

use App\Models\LibraryImportPreview;
use App\Models\LibraryTask;
use App\Models\LibraryTaskChange;
use App\Models\LibraryVersion;
use App\Models\User;
use App\Services\Library\LibraryFormat;
use App\Services\Library\PricingLibrary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use RuntimeException;
use Tests\TestCase;

class PricingLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_actual_csv_json_preview_apply_and_export_round_trip(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.library.index'))->assertOk()->assertSee('Import Library');
        $this->get(route('admin.library.import'))->assertOk()->assertSee('Preview import');

        $csvPath = base_path('bidfreq-pricing-data-v3.csv');
        $jsonPath = base_path('bidfreq-pricing-data-v3.json');
        $format = app(LibraryFormat::class);
        $csv = $format->read($csvPath, 'csv');
        $json = $format->read($jsonPath, 'json');
        $this->assertSame($csv, $json);
        $this->assertCount(162, $csv[0]);

        $upload = $this->post(route('admin.library.upload'), ['file' => new UploadedFile($csvPath, basename($csvPath), null, null, true)]);
        $record = LibraryImportPreview::sole();
        $upload->assertRedirectToRoute('admin.library.preview', $record);
        $this->assertDatabaseCount('library_tasks', 0);
        $this->get(route('admin.library.preview', $record))->assertOk()->assertSee('162 new')->assertSee('0 errors');
        $this->post(route('admin.library.apply', $record))->assertRedirectToRoute('admin.library.index');
        $this->post(route('admin.library.apply', $record))->assertNotFound();
        $this->assertDatabaseCount('library_tasks', 162);
        $this->assertDatabaseCount('library_task_changes', 162);
        $this->assertDatabaseHas('library_versions', ['filename' => basename($csvPath), 'user_id' => $admin->id]);
        $this->assertSame(7, LibraryTask::where('pricing_mode', 'quote_by_scope')->whereNull('bidfreq_standard_rate')->count());
        $this->assertSame('3084.000000', LibraryTask::findOrFail('BF-T-079')->reference_rate);
        $this->assertSame('each', LibraryTask::findOrFail('BF-T-150')->unit);
        $this->assertSame(22, LibraryTask::distinct()->count('unit'));

        $super = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($super)->get(route('admin.library.index', ['mode' => 'quote_by_scope']))
            ->assertOk()->assertSee('7 matching tasks')->assertDontSee('BF-T-001');
        $this->get(route('admin.library.index', ['flag' => 'ambiguous']))->assertOk()->assertSee('8 matching tasks');
        $this->get(route('admin.library.show', 'BF-T-079'))->assertOk()->assertSee('reference only');
        $this->get(route('admin.library.edit', 'BF-T-079'))->assertOk()->assertSee('Save task');
        $this->get(route('admin.library.history', 'BF-T-079'))->assertOk()->assertSee('Task added');

        $this->artisan('bidfreq:library-import', ['file' => $jsonPath, '--email' => $super->email, '--apply' => true])->assertSuccessful();
        $this->assertSame(162, LibraryVersion::latest('id')->firstOrFail()->summary['unchanged']);
        $this->assertDatabaseCount('library_task_changes', 162);

        $this->get(route('admin.library.export-options'))->assertOk()->assertSee('Download CSV')->assertSee('Download JSON');
        foreach (['csv', 'json'] as $type) {
            $response = $this->get(route('admin.library.export', $type))->assertOk()->assertDownload('bidfreq-library.'.$type);
            $path = tempnam(sys_get_temp_dir(), 'bidfreq-export-');
            try {
                file_put_contents($path, $response->streamedContent());
                $this->assertSame($csv, $format->read($path, $type));
            } finally {
                unlink($path);
            }
        }
    }

    public function test_invalid_rows_block_the_entire_import_and_previews_are_private(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $other = User::factory()->create(['role' => 'admin']);
        $row = $this->row();
        $file = UploadedFile::fake()->createWithContent('bad.json', json_encode([
            $row, $row, array_replace($row, ['task_id' => 'BF-T-999', 'bidfreq_standard_rate' => '1.2345678']),
        ]));

        $this->actingAs($admin)->post(route('admin.library.upload'), ['file' => $file])->assertRedirect();
        $preview = LibraryImportPreview::sole();
        $this->get(route('admin.library.preview', $preview))->assertOk()->assertSee('duplicate task ID')->assertDontSee('Apply import');
        $this->postJson(route('admin.library.apply', $preview))->assertUnprocessable()->assertJsonValidationErrors('file');
        $this->actingAs($other)->get(route('admin.library.preview', $preview))->assertNotFound();
        $this->post(route('admin.library.apply', $preview))->assertNotFound();
        $this->assertDatabaseCount('library_tasks', 0);
        $this->assertDatabaseCount('library_versions', 0);
    }

    public function test_edits_keep_ids_audit_rates_and_make_older_previews_stale(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $library = app(PricingLibrary::class);
        $path = base_path('bidfreq-pricing-data-v3.json');
        $library->apply($library->preview($path, basename($path), $admin)->id, $admin);
        $stale = $library->preview($path, basename($path), $admin);
        $task = LibraryTask::findOrFail('BF-T-001');
        $payload = array_replace($this->row(), [
            'library_version_id' => $task->library_version_id, 'task_id' => 'BF-T-999',
            'bidfreq_standard_rate' => '2.10',
        ]);

        $this->actingAs($admin)->patch(route('admin.library.update', $task), $payload)
            ->assertRedirectToRoute('admin.library.show', $task);
        $this->assertDatabaseMissing('library_tasks', ['task_id' => 'BF-T-999']);
        $change = LibraryTaskChange::latest('id')->firstOrFail();
        $this->assertSame('1.25', $change->before['bidfreq_standard_rate']);
        $this->assertSame('2.1', $change->after['bidfreq_standard_rate']);
        $this->assertSame($admin->id, $change->version->user_id);
        $this->get(route('admin.library.history', $task))->assertOk()->assertSee('1.25')->assertSee('2.1');
        $this->patchJson(route('admin.library.update', $task), $payload)->assertUnprocessable()->assertJsonValidationErrors('library_version_id');
        $payload['library_version_id'] = $task->refresh()->library_version_id;
        $this->patchJson(route('admin.library.update', $task), $payload)
            ->assertRedirectToRoute('admin.library.show', $task);
        $this->assertDatabaseCount('library_versions', 2);
        $this->postJson(route('admin.library.apply', $stale))->assertUnprocessable()->assertJsonValidationErrors('file');

        $preview = $library->preview($path, basename($path), $admin);
        $this->get(route('admin.library.preview', $preview))->assertOk()->assertSee('1 rate changes')->assertSee('161 unchanged');
    }

    public function test_failed_write_rolls_back_tasks_history_and_version(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $library = app(PricingLibrary::class);
        $path = base_path('bidfreq-pricing-data-v3.csv');
        $preview = $library->preview($path, basename($path), $admin);
        $seen = 0;
        LibraryTaskChange::creating(function () use (&$seen) {
            if (++$seen === 2) {
                throw new RuntimeException('Simulated history write failure.');
            }
        });

        try {
            $library->apply($preview->id, $admin);
            $this->fail('The simulated failure was not propagated.');
        } catch (RuntimeException $e) {
            $this->assertSame('Simulated history write failure.', $e->getMessage());
        }

        $this->assertDatabaseCount('library_versions', 0);
        $this->assertDatabaseCount('library_tasks', 0);
        $this->assertDatabaseCount('library_task_changes', 0);
        $this->assertDatabaseHas('library_import_previews', ['id' => $preview->id]);
    }

    public function test_customers_are_denied_and_guests_are_redirected(): void
    {
        $this->get(route('admin.library.index'))->assertRedirectToRoute('login');
        $admin = User::factory()->create(['role' => 'admin']);
        $library = app(PricingLibrary::class);
        $path = base_path('bidfreq-pricing-data-v3.csv');
        $library->apply($library->preview($path, basename($path), $admin)->id, $admin);

        $this->actingAs(User::factory()->create());
        foreach ([
            ['GET', 'index', []], ['GET', 'import', []], ['POST', 'upload', []],
            ['GET', 'preview', ['00000000-0000-4000-8000-000000000000']],
            ['POST', 'apply', ['00000000-0000-4000-8000-000000000000']],
            ['GET', 'show', ['BF-T-001']], ['GET', 'edit', ['BF-T-001']], ['PATCH', 'update', ['BF-T-001']],
            ['GET', 'history', ['BF-T-001']], ['GET', 'export-options', []], ['GET', 'export', ['csv']],
        ] as [$method, $name, $parameters]) {
            $this->call($method, route('admin.library.'.$name, $parameters))->assertForbidden();
        }
    }

    private function row(): array
    {
        return json_decode(file_get_contents(base_path('bidfreq-pricing-data-v3.json')), true, flags: JSON_THROW_ON_ERROR)[0];
    }
}
