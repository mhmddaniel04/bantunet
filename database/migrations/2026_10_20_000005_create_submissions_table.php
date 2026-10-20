<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Volunteer applications. Volunteers have no account; they track an
     * application using their email + tracking code.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id('submission_id');
            $table->foreignId('event_id')->constrained('events', 'event_id')->cascadeOnDelete();
            $table->string('tracking_code', 12)->unique();       // e.g. BN-7K2QX9

            // Volunteer details
            $table->string('volunteer_name');
            $table->string('volunteer_email', 100)->index();     // stored lowercase
            $table->string('volunteer_phone', 20);
            $table->unsignedTinyInteger('age');
            $table->string('gender', 20);
            $table->string('tshirt_size', 5);
            $table->string('availability', 30);                  // Weekdays | Weekends | Any
            $table->string('emergency_name');
            $table->string('emergency_phone', 20);
            $table->text('skills_summary')->nullable();
            $table->string('resume_path')->nullable();           // private storage path
            $table->string('resume_original_name')->nullable();

            // How the volunteer found the event (for AI evaluation in Chapter 4)
            $table->string('source', 20)->default('browse');     // browse | smart_match
            $table->decimal('match_score', 5, 2)->nullable();    // 0-100 when source = smart_match

            // Review by the NPO
            $table->string('review_status', 20)->default('Pending');
            $table->text('reviewer_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            // Engagement data (for annual reports)
            $table->boolean('attended')->default(false);
            $table->decimal('hours_volunteered', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'volunteer_email']);     // one application per event per email
            $table->index(['event_id', 'review_status']);
        });

        DB::statement("ALTER TABLE submissions ADD CONSTRAINT submissions_status_check
            CHECK (review_status IN ('Pending', 'Approved', 'Rejected', 'Withdrawn'))");
        DB::statement("ALTER TABLE submissions ADD CONSTRAINT submissions_source_check
            CHECK (source IN ('browse', 'smart_match'))");
        DB::statement('ALTER TABLE submissions ADD CONSTRAINT submissions_age_check
            CHECK (age BETWEEN 13 AND 100)');
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};