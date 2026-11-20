<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('action', 20);
            $table->string('filename')->nullable();
            $table->json('summary');
            $table->timestamps();
        });

        Schema::create('library_tasks', function (Blueprint $table) {
            $table->string('task_id', 32)->primary();
            $table->foreignId('library_version_id')->constrained();
            $table->string('task_name');
            $table->string('unit', 100)->index();
            foreach (['evergreen_low', 'evergreen_typical', 'evergreen_high', 'standard_hours', 'labor_cost_market_avg'] as $field) {
                $table->decimal($field, 16, 6);
            }
            $table->decimal('bidfreq_standard_rate', 16, 6)->nullable();
            $table->decimal('reference_rate', 16, 6)->nullable();
            $table->unsignedInteger('times_seen_scopes');
            $table->string('pricing_mode', 20)->index();
            $table->string('rate_label')->nullable();
            $table->string('hours_basis');
            $table->text('source');
            $table->date('effective_date');
            $table->string('version', 50);
            $table->date('last_updated');
            $table->timestamps();
        });

        Schema::create('library_task_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_version_id')->constrained();
            $table->string('task_id', 32);
            $table->foreign('task_id')->references('task_id')->on('library_tasks');
            $table->json('before')->nullable();
            $table->json('after');
            $table->timestamps();
            $table->index(['task_id', 'id']);
        });

        Schema::create('library_reference_labor_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_version_id')->constrained();
            $table->string('market');
            $table->string('occupation');
            $table->decimal('hourly_rate', 16, 6);
            $table->text('source');
            $table->timestamps();
        });

        Schema::create('library_import_previews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->longText('rows');
            $table->longText('errors');
            $table->string('fingerprint', 64);
            $table->timestamp('expires_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_import_previews');
        Schema::dropIfExists('library_reference_labor_rates');
        Schema::dropIfExists('library_task_changes');
        Schema::dropIfExists('library_tasks');
        Schema::dropIfExists('library_versions');
    }
};
