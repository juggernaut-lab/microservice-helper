<?php

namespace Gopaddi\PaddiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeEnumCommand extends Command
{
    protected $signature = 'juggernaut:make-enum {name : Enum name}';

    protected $description = 'Create a new PHP enum inside the Juggernaut package';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $name = Str::studly($this->argument('name'));

        $dir = __DIR__ . '/../../src/Enums';
        $ns = 'Gopaddi\\PaddiHelper\\Enums';

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/' . $name . '.php';

        if ($this->files->exists($path)) {
            $this->error("Enum already exists: {$path}");

            return Command::FAILURE;
        }

        $stub = file_get_contents(__DIR__ . '/../../stubs/enum.stub');
        $stub = str_replace(['{{namespace}}', '{{enum}}'], [$ns, $name], $stub);

        $this->files->put($path, $stub);

        $this->info("Created enum: {$path}");

        return Command::SUCCESS;
    }
}
