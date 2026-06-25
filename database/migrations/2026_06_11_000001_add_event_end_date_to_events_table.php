<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->date('event_end_date')->nullable()->after('event_date');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->date('event_date')->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('event_date')->change();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_end_date');
        });
    }
};
