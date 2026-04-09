<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('staff_id')->unique()->after('id');
            $table->string('department')->nullable()->after('email');
            $table->string('position')->nullable()->after('department');
            $table->integer('approval_level')->default(1)->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['staff_id', 'department', 'position', 'approval_level']);
        });
    }
};
