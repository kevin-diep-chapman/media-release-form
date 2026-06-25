<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->date('date_of_birth');
            $table->string('photo_path');
            $table->boolean('consent_agreed');
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_releases');
    }
};
