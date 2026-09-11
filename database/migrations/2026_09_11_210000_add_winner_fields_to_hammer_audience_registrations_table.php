<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hammer_audience_registrations', function (Blueprint $table) {
            $table->boolean('is_winner')->default(false)->after('google_review_name')->index();
            $table->timestamp('won_at')->nullable()->after('is_winner');
            $table->boolean('prize_claimed')->default(false)->after('won_at');
        });
    }

    public function down(): void
    {
        Schema::table('hammer_audience_registrations', function (Blueprint $table) {
            $table->dropColumn(['is_winner', 'won_at', 'prize_claimed']);
        });
    }
};
