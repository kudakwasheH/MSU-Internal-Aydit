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
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'title')) {
                $table->string('title')->nullable()->after('audit_id');
            }
            if (!Schema::hasColumn('reports', 'executive_summary')) {
                $table->text('executive_summary')->nullable()->after('title');
            }
            if (!Schema::hasColumn('reports', 'scope')) {
                $table->text('scope')->nullable()->after('executive_summary');
            }
            if (!Schema::hasColumn('reports', 'review_notes')) {
                $table->json('review_notes')->nullable()->after('comments');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['title', 'executive_summary', 'scope', 'review_notes']);
        });
    }
};
