<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each application gets an AI vector of the volunteer's skills & experience,
     * so NPOs can rank applicants by how well they fit the initiative.
     */
    public function up(): void
    {
        // 768 must match GEMINI_EMBED_DIM in .env
        DB::statement('ALTER TABLE submissions ADD COLUMN embedding vector(768)');

        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('embedded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['embedding', 'embedded_at']);
        });
    }
};