<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_id')->constrained('risk_registers')->onDelete('cascade');
            $table->string('strategy'); // Avoid, Reduce/Mitigate, Transfer, Accept
            $table->text('description');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('not_started'); // Not Started, In Progress, Partially Complete, Complete
            $table->date('due_date')->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_treatments');
    }
};
