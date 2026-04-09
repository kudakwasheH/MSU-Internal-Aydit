<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_code')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('audit_type'); // internal, external, it, compliance, performance
            $table->string('priority')->default('medium'); // high, medium, low
            $table->string('status')->default('draft'); // draft, planned, in_progress, completed, cancelled
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('status');
            $table->index(['planned_start_date', 'planned_end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
