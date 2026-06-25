<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rag_chunks', function (Blueprint $table) {
            $table->id();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->unsignedSmallInteger('chunk_index')->default(0);
            $table->text('content');
            $table->json('embedding');
            $table->json('metadata')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();

            $table->unique(['source_type', 'source_id', 'chunk_index']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rag_chunks');
    }
};
