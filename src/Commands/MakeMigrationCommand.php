<?php

namespace JuggernautLab\MicroserviceHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\MigrationCreator;

class MakeMigrationCommand extends Command
{
    protected $signature = 'juggernaut:make-migration {name : The name of the migration}
                                                     {--create= : The table to be created}
                                                     {--table= : The table to be modified}';

    protected $description = 'Create a new migration inside the Juggernaut package';

    public function handle()
    {
        $name = $this->argument('name');
        $table = $this->option('table');
        $create = $this->option('create');

        // if (! $table && ! $create) {
        //     $this->error('You must specify either --table or --create option.');
        //     return Command::FAILURE;
        // }

        $migrationPath = __DIR__ . '/../../database/migrations';

        $creator = app(MigrationCreator::class);

        $file = $creator->create(
            $name,
            $migrationPath,
            $table,
            $create
        );

        $this->info("Migration created:");
        $this->line("  {$file}");
    }
}
