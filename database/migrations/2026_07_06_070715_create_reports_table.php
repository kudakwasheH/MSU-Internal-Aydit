<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->foreignId('prepared_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('senior_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('chief_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comments')->nullable();
            $table->timestamp('draft_issue_date')->nullable();
            $table->timestamp('final_issue_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
