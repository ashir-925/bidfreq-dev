<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_drafts', function (Blueprint $table) {
            $table->unsignedInteger('review_version')->default(0);
        });
        Schema::table('repair_draft_lines', function (Blueprint $table) {
            $table->decimal('original_rate', 16, 6)->nullable();
            $table->string('override_reason', 1000)->nullable();
        });
        Schema::create('repair_estimate_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approved_by')->constrained('users');
            $table->unsignedInteger('version');
            $table->unique(['repair_request_id', 'version'], 'repair_release_version_unique');
            $table->json('snapshot');
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->string('decision', 24)->nullable();
            $table->text('customer_comment')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
        Schema::create('repair_estimate_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('action', 30);
            $table->json('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_estimate_events');
        Schema::dropIfExists('repair_estimate_releases');
        Schema::table('repair_draft_lines', fn (Blueprint $table) => $table->dropColumn(['original_rate', 'override_reason']));
        Schema::table('repair_drafts', fn (Blueprint $table) => $table->dropColumn('review_version'));
    }
};
