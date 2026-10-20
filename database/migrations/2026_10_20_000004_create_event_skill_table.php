<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot: an event can require many skills; a skill can belong to many events.
     */
    public function up(): void
    {
        Schema::create('event_skill', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained('events', 'event_id')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills', 'skill_id')->cascadeOnDelete();
            $table->primary(['event_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_skill');
    }
};