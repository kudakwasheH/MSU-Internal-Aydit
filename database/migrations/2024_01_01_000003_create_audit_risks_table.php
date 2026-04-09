<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_risks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->onDelete('cascade');
            $table->foreignId('risk_id')->constrained('risk_registers')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['audit_id', 'risk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_risks');
    }
};
