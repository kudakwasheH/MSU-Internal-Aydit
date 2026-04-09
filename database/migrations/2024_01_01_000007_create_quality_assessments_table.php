<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->onDelete('cascade');
            $table->string('assessment_type'); // internal, external
            $table->foreignId('reviewer_id')->constrained('users');
            $table->date('review_date');
            $table->integer('score');
            $table->text('strengths');
            $table->text('improvements');
            $table->string('status')->default('draft'); // draft, final
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_assessments');
    }
};
