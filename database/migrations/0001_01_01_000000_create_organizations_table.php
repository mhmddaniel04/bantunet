<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Organizations are the authenticated users of the system (NPO organisers).
     * Volunteers do not have accounts.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('org_id');
            $table->string('org_name');
            $table->string('ros_number', 50)->unique();          // Registry of Societies number
            $table->string('org_email', 100)->unique();          // login email
            $table->string('org_phone', 20)->nullable();
            $table->text('org_hq_location')->nullable();
            $table->string('state', 50)->nullable();             // Malaysian state
            $table->string('website')->nullable();
            $table->text('description')->nullable();             // mission statement
            $table->string('logo_path')->nullable();
            $table->boolean('is_verified')->default(false);      // ROS verified badge
            $table->string('password');                          // bcrypt hash
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();   // holds org_id when logged in
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('organizations');
    }
};