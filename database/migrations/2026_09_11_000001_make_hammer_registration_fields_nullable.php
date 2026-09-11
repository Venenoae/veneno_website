<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hammer_challenge_registrations', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->unsignedTinyInteger('age')->nullable()->change();
            $table->string('emergency_contact_name')->nullable()->change();
            $table->string('emergency_contact_number')->nullable()->change();
            $table->boolean('age_declaration')->default(true)->change();
            $table->boolean('health_declaration')->default(true)->change();
            $table->boolean('challenge_declaration')->default(true)->change();
            $table->boolean('voluntary_participation')->default(true)->change();
            $table->boolean('media_consent')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('hammer_challenge_registrations', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->unsignedTinyInteger('age')->nullable(false)->change();
            $table->string('emergency_contact_name')->nullable(false)->change();
            $table->string('emergency_contact_number')->nullable(false)->change();
        });
    }
};
