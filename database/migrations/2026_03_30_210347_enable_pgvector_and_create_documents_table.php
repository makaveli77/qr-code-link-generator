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
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content');
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        // Add a vector column with 768 dimensions (for Gemini embeddings)
        DB::statement('ALTER TABLE documents ADD COLUMN embedding vector(3072);');
        // Add an HNSW index to speed up vector searches
        // DB::statement('CREATE INDEX documents_embedding_index.*');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        DB::statement('DROP EXTENSION IF EXISTS vector;');
    }
};
