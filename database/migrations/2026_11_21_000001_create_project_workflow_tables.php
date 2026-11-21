<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email')->unique();
            $table->string('phone', 40);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('repair_request_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('repair_estimate_release_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('estimator_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('address');
            $table->string('status', 20)->index();
            $table->date('start_date')->nullable();
            $table->date('estimated_completion_date')->nullable();
            $table->json('materials')->nullable();
            $table->json('crew_contacts')->nullable();
            $table->date('walkthrough_date')->nullable();
            $table->string('walkthrough_status', 25)->nullable();
            $table->text('walkthrough_notes')->nullable();
            $table->string('certificate_reference')->nullable();
            $table->date('certificate_date')->nullable();
            $table->timestamps();
        });
        Schema::create('project_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('report_date');
            $table->string('reported_by', 150);
            $table->text('progress');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'report_date']);
        });
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_daily_report_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('kind', 20);
            $table->string('name');
            $table->string('mime', 120);
            $table->string('path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
        Schema::create('project_change_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_daily_report_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('status', 20);
            $table->json('lines');
            $table->decimal('total', 24, 2);
            $table->timestamps();
        });
        Schema::create('project_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('action');
            $table->json('details');
            $table->timestamps();
        });

        DB::table('repair_estimate_releases as releases')
            ->join('repair_requests as repairs', 'repairs.id', '=', 'releases.repair_request_id')
            ->where('releases.decision', 'approve')
            ->whereIn('repairs.status', ['customer_approved', 'in_progress', 'completed'])
            ->select('releases.id', 'releases.snapshot', 'releases.decided_at', 'repairs.id as repair_id', 'repairs.status as repair_status', 'repairs.user_id', 'repairs.street_address', 'repairs.city', 'repairs.state', 'repairs.zip')
            ->chunkById(100, function ($releases) {
                foreach ($releases as $release) {
                    $snapshot = json_decode($release->snapshot, true, 512, JSON_THROW_ON_ERROR);
                    DB::table('projects')->insert([
                        'reference' => (string) Str::uuid(),
                        'user_id' => $release->user_id,
                        'repair_request_id' => $release->repair_id,
                        'repair_estimate_release_id' => $release->id,
                        'address' => $snapshot['address'] ?? "{$release->street_address}, {$release->city}, {$release->state} {$release->zip}",
                        'status' => $release->repair_status === 'customer_approved' ? 'scheduled' : $release->repair_status,
                        'created_at' => $release->decided_at ?? now(),
                        'updated_at' => now(),
                    ]);
                }
            }, 'releases.id', 'id');
    }

    public function down(): void
    {
        Schema::dropIfExists('project_events');
        Schema::dropIfExists('project_change_orders');
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('project_daily_reports');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('estimators');
    }
};

