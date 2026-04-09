<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_registers', function (Blueprint $table) {
            $table->id();
            $table->string('risk_code')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('category'); // operational, financial, compliance, strategic, it
            $table->integer('inherent_likelihood')->default(3); // 1-5
            $table->integer('inherent_impact')->default(3); // 1-5
            $table->integer('inherent_risk_score')->virtualAs('inherent_likelihood * inherent_impact');
            $table->integer('residual_likelihood')->default(2); // 1-5
            $table->integer('residual_impact')->default(2); // 1-5
            $table->integer('residual_risk_score')->virtualAs('residual_likelihood * residual_impact');
            $table->string('status')->default('active'); // active, mitigated, obsolete
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_registers');
    }
};
