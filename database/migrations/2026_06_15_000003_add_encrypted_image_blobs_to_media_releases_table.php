<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_releases', function (Blueprint $table) {
            $table->longText('photo_encrypted')->nullable()->after('photo_path');
            $table->string('photo_mime', 64)->nullable()->after('photo_encrypted');
            $table->longText('signature_encrypted')->nullable()->after('signature_path');
            $table->string('signature_mime', 64)->nullable()->after('signature_encrypted');
        });

        Schema::table('media_releases', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('media_releases', function (Blueprint $table) {
            $table->dropColumn([
                'photo_encrypted',
                'photo_mime',
                'signature_encrypted',
                'signature_mime',
            ]);
        });
    }
};
