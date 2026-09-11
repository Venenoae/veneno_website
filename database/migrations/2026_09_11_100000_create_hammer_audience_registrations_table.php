<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hammer_audience_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('confirmation_token', 64)->unique();
            $table->string('full_name');
            $table->string('mobile')->index();
            $table->string('email')->nullable()->index();
            $table->enum('status', ['registered', 'checked_in', 'attended'])
                ->default('registered')
                ->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hammer_audience_registrations');
    }
};
