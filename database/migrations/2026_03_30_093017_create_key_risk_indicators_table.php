<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('key_risk_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('risk_category'); // Financial, Compliance, Reputational, etc.
            $table->string('threshold_green'); // E.g., '< 10%'
            $table->string('threshold_yellow');
            $table->string('threshold_red');
            $table->string('current_value');
            $table->string('status')->default('green'); // green, yellow, red
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('key_risk_indicators');
    }
};
