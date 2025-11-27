<?php

namespace Gopaddi\PaddiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeClassCommand extends Command
{
    protected $signature = 'juggernaut:make-class {name : Class name} {--namespace= : Sub-namespace inside package src}';

    protected $description = 'Create a new PHP class inside the Juggernaut package';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $sub = trim($this->option('namespace') ?? '', '\\/ ');

        $dir = __DIR__ . '/../../src';
        $ns = 'Gopaddi\\PaddiHelper';

        if ($sub) {
            $dir .= '/' . str_replace('\\', '/', $sub);
            $ns .= '\\' . str_replace('/', '\\', $sub);
        }

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/' . $name . '.php';

        if ($this->files->exists($path)) {
            $this->error("Class already exists: {$path}");

            return Command::FAILURE;
        }

        $stub = file_get_contents(__DIR__ . '/../../stubs/class.stub');
        $stub = str_replace(['{{namespace}}', '{{class}}'], [$ns, $name], $stub);

        $this->files->put($path, $stub);

        $this->info("Created class: {$path}");

        return Command::SUCCESS;
    }
}
