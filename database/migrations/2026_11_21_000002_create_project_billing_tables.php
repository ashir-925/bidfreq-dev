<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->uuid('reference')->unique();
            $table->string('invoice_type', 20);
            $table->string('status', 20);
            $table->date('due_date');
            $table->json('lines');
            $table->decimal('total', 24, 2);
            $table->timestamps();
        });
        Schema::create('project_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_invoice_id')->constrained()->restrictOnDelete();
            $table->uuid('reference')->unique();
            $table->string('provider', 20);
            $table->string('provider_id')->nullable();
            $table->unique(['provider', 'provider_id']);
            $table->string('status', 20);
            $table->decimal('amount', 24, 2);
            $table->date('received_on')->nullable();
            $table->string('method', 80)->nullable();
            $table->string('external_reference', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_payments');
        Schema::dropIfExists('project_invoices');
    }
};

