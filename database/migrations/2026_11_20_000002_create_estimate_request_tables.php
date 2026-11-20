<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('user_id')->constrained();
            $table->uuid('submission_key');
            $table->unique(['user_id', 'submission_key']);
            $table->string('property_name')->nullable();
            $table->string('property_type')->nullable();
            $table->string('street_address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->decimal('home_sqft', 12, 2);
            $table->unsignedTinyInteger('stories');
            $table->json('descriptions');
            $table->json('dimensions');
            $table->string('priority', 8)->index();
            $table->decimal('nte', 14, 2)->nullable();
            $table->string('status', 20)->index();
            $table->unsignedInteger('ai_run')->default(1);
            $table->timestamps();
        });

        Schema::create('repair_request_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 15);
            $table->string('trade')->nullable();
            $table->string('name');
            $table->string('mime');
            $table->string('path');
            $table->unsignedInteger('bytes');
            $table->timestamps();
        });

        Schema::create('repair_ai_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('run');
            $table->unsignedInteger('attempt');
            $table->unique(['repair_request_id', 'run', 'attempt'], 'repair_ai_attempt_unique');
            $table->string('model')->nullable();
            $table->longText('input')->nullable();
            $table->longText('response')->nullable();
            $table->string('outcome', 20);
            $table->string('error')->nullable();
            $table->timestamps();
        });

        Schema::create('repair_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('repair_ai_attempt_id')->nullable()->constrained();
            $table->json('analysis');
            $table->decimal('total', 24, 2);
            $table->boolean('over_nte');
            $table->timestamps();
        });

        Schema::create('repair_draft_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_draft_id')->constrained()->cascadeOnDelete();
            $table->string('task_id', 32)->nullable();
            $table->foreign('task_id')->references('task_id')->on('library_tasks');
            $table->foreignId('library_version_id')->nullable()->constrained();
            $table->string('name');
            $table->string('unit', 100)->nullable();
            $table->decimal('quantity', 14, 6)->nullable();
            $table->decimal('rate', 16, 6)->nullable();
            $table->decimal('line_total', 22, 2)->nullable();
            $table->date('priced_on')->nullable();
            $table->string('source', 10);
            $table->string('quantity_basis', 50)->nullable();
            $table->json('evidence');
            $table->json('flags');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_draft_lines');
        Schema::dropIfExists('repair_drafts');
        Schema::dropIfExists('repair_ai_attempts');
        Schema::dropIfExists('repair_request_files');
        Schema::dropIfExists('repair_requests');
    }
};
