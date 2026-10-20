<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Anonymous log of every Smart AI Match request.
     * Used to evaluate response time (NFR1) and recommendation quality.
     * No personal data (name/email) is stored here.
     */
    public function up(): void
    {
        Schema::create('match_logs', function (Blueprint $table) {
            $table->id('match_log_id');
            $table->text('query_text');                          // combined profile text sent to Gemini
            $table->jsonb('skill_ids')->nullable();
            $table->foreignId('category_id')->nullable()
                ->constrained('categories', 'category_id')->nullOnDelete();
            $table->string('availability', 30)->nullable();
            $table->string('state', 50)->nullable();
            $table->jsonb('results')->nullable();                // [{event_id, semantic, final}, ...]
            $table->unsignedInteger('response_ms');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_logs');
    }
};