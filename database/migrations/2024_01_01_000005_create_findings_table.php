<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('root_cause');
            $table->text('impact');
            $table->text('recommendation');
            $table->string('severity')->default('medium'); // critical, high, medium, low
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->integer('escalation_level')->default(1);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
