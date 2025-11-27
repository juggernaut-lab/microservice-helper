<?php

namespace Gopaddi\PaddiHelper\Commands;

use Illuminate\Console\Command;

class PublishMigrationsCommand extends Command
{
    protected $signature = 'juggernaut:publish-migrations 
                            {--force : Overwrite existing migration files}';

    protected $description = 'Publishes gopaddi shared package migrations safely with analytics.';

    public function handle()
    {
        $this->info("\nPublishing Juggernaut migrations...");
        $this->line("──────────────────────────────────────────");

        $sourcePath = __DIR__ . '/../../database/migrations';
        $migrationFiles = glob($sourcePath . '/*.php');

        $new = [];
        $existingCount = 0;
        $total = count($migrationFiles);

        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            $target = database_path("migrations/{$filename}");

            if (file_exists($target) && ! $this->option('force')) {
                $existingCount++;
                continue;
            }

            // Publish new migrations (or overwrite if --force)
            copy($file, $target);
            $new[] = $filename;
        }

        // ───── SUMMARY OUTPUT ─────────────────────────────────

        $this->line("\nSummary:");
        $this->line("──────────────────────────────────────────");

        $this->info("Total migrations in package:   {$total}");
        $this->info("Existing in project already:   {$existingCount}");
        $this->info("Newly published migrations:    " . count($new));

        if (count($new) > 0) {
            $this->line("\nNew migrations added:");
            foreach ($new as $filename) {
                $this->line("  ✔ {$filename}");
            }
        } else {
            $this->warn("\nNo new migrations were published.");
        }

        $this->line("\nDone.\n");

        return Command::SUCCESS;
    }
}
