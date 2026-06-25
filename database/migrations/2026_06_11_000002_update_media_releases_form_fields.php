<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_releases', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('event_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('zip_code')->nullable()->after('state');
            $table->string('affiliation')->nullable()->after('zip_code');
            $table->text('affiliation_details')->nullable()->after('affiliation');
        });

        foreach (DB::table('media_releases')->orderBy('id')->get() as $release) {
            $parts = preg_split('/\s+/', trim((string) $release->full_name), 2);

            DB::table('media_releases')->where('id', $release->id)->update([
                'first_name' => $parts[0] ?? '',
                'last_name' => $parts[1] ?? '',
            ]);
        }

        Schema::table('media_releases', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('media_releases', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
            $table->dropColumn([
                'first_name',
                'last_name',
                'city',
                'state',
                'zip_code',
                'affiliation',
                'affiliation_details',
            ]);
        });
    }
};
