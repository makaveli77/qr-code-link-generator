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
        if (config('database.default') === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
        }

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content');
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        // Add a vector column with 3072 dimensions (for Gemini embeddings)
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE documents ADD COLUMN embedding vector(3072);');
        } else {
            Schema::table('documents', function (Blueprint $table) {
                $table->json('embedding')->nullable();
            });
        }
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
