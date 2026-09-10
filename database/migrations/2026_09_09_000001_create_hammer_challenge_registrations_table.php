<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hammer_challenge_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('confirmation_token', 64)->unique();
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->unsignedTinyInteger('age');
            $table->string('mobile')->unique();
            $table->string('email')->unique();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number');
            $table->boolean('age_declaration');
            $table->boolean('health_declaration');
            $table->boolean('challenge_declaration');
            $table->boolean('voluntary_participation');
            $table->boolean('terms_accepted');
            $table->boolean('media_consent');
            $table->enum('status', ['registered', 'checked_in', 'participated', 'disqualified', 'finished'])
                ->default('registered')
                ->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hammer_challenge_registrations');
    }
};
