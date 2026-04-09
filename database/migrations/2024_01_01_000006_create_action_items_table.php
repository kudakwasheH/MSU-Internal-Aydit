<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained()->onDelete('cascade');
            $table->text('action_description');
            $table->foreignId('assigned_to')->constrained('users');
            $table->date('due_date');
            $table->string('status')->default('pending'); // pending, in_progress, completed, overdue
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index(['due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_items');
    }
};
