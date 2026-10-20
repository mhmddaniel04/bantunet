<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lookup table: skills an event may need (e.g. Teaching, First Aid).
     */
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id('skill_id');
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};