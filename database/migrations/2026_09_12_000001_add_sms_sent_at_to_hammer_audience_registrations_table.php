<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hammer_audience_registrations', function (Blueprint $table) {
            $table->timestamp('sms_sent_at')->nullable()->after('prize_claimed');
        });
    }

    public function down(): void
    {
        Schema::table('hammer_audience_registrations', function (Blueprint $table) {
            $table->dropColumn('sms_sent_at');
        });
    }
};
