<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Volunteer initiatives published by organizations.
     * The `embedding` column stores the Gemini vector used for Smart AI Matching.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->foreignId('org_id')->constrained('organizations', 'org_id')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories', 'category_id')->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();            // e.g. "Bring own gloves"
            $table->string('location');                          // venue / address
            $table->string('state', 50);                         // Malaysian state, for filtering
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->date('application_deadline')->nullable();    // null = until event date
            $table->unsignedSmallInteger('capacity')->nullable(); // volunteer slots, null = unlimited
            $table->string('image_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('published');  // draft | published | completed | cancelled
            $table->timestamp('embedded_at')->nullable();        // when the AI vector was last generated
            $table->timestamps();

            $table->index(['status', 'event_date']);
            $table->index('state');
        });

        // 768 must match GEMINI_EMBED_DIM in .env
        DB::statement('ALTER TABLE events ADD COLUMN embedding vector(768)');

        // HNSW index makes cosine-distance search fast as events grow
        DB::statement('CREATE INDEX events_embedding_hnsw_idx ON events USING hnsw (embedding vector_cosine_ops)');

        DB::statement("ALTER TABLE events ADD CONSTRAINT events_status_check
            CHECK (status IN ('draft', 'published', 'completed', 'cancelled'))");
        DB::statement('ALTER TABLE events ADD CONSTRAINT events_capacity_check
            CHECK (capacity IS NULL OR capacity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};