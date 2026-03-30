<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Enums\ModelType;

class IndexKnowledgeBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rag:index-docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads markdown files from resources/docs, gets AI embeddings, and stores them in pgvector.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting knowledge base indexing...');
        $docsPath = resource_path('docs');

        if (!File::exists($docsPath)) {
            $this->error("Directory $docsPath does not exist.");
            return;
        }

        $files = File::files($docsPath);

        // Optional: clear existing documents so we don't duplicate on re-run
        DB::table('documents')->truncate();

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $title = str_replace('.md', '', $file->getFilename());
            $content = File::get($file->getPathname());
            
            $this->info("Generating embedding for: $title...");

            try {
                // Get embedding from Gemini using their embedding model
                // The new default embedding model for Gemini is embedding-001
                $response = Gemini::embeddingModel('gemini-embedding-001')->embedContent($content);
                $embedding = $response->embedding->values;

                // Format the embedding array for pgvector, e.g., "[0.1, 0.2, 0.3]"
                $vectorString = '[' . implode(',', $embedding) . ']';

                // Insert into DB using raw SQL for the vector column
                DB::statement('
                    INSERT INTO documents (title, content, embedding, created_at, updated_at) 
                    VALUES (?, ?, ?::vector, NOW(), NOW())
                ', [
                    $title,
                    $content,
                    $vectorString
                ]);

                $this->line("✔ Successfully embedded and indexed: $title");

            } catch (\Exception $e) {
                $this->error("Failed to embed $title: " . $e->getMessage());
            }
        }

        $this->info('Knowledge base indexing complete!');
    }
}
