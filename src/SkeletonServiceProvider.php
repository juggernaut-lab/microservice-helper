<?php

namespace Gopaddi\PaddiHelper;

use Gopaddi\PaddiHelper\Commands\PublishMigrationsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Gopaddi\PaddiHelper\Commands\SkeletonCommand;

class SkeletonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('juggernaut')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations()
            ->hasCommands([PublishMigrationsCommand::class]);
    }

    // public function bootingPackage()
    // {
    //     $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    // }

    public function bootingPackage()
    {
       $this->publishesMigrationsSafely();

        // Hook into vendor:publish AFTER it runs
        $this->app->afterResolving('command.vendor.publish', function ($command) {
            $command->getOutput()->writeln('');
            $command->getOutput()->writeln('<info>Juggernaut Migration Summary:</info>');

            $published = cache('juggernaut.published_migrations', []);

            if (empty($published)) {
                $command->getOutput()->writeln('<comment>No new migrations were published.</comment>');
            } else {
                foreach ($published as $file) {
                    $command->getOutput()->writeln("<info>✔ {$file}</info>");
                }
            }

            cache()->forget('juggernaut.published_migrations');
        });
    }

    protected function publishesMigrationsSafely()
    {
        $migrations = glob(__DIR__ . '/../database/migrations/*.php');
        $published = [];

        foreach ($migrations as $migration) {
            $filename = basename($migration);
            $target = database_path("migrations/{$filename}");

            if (! file_exists($target)) {
                $this->publishes([
                    $migration => $target,
                ], 'juggernaut-migrations');

                $published[] = $filename;
            }
        }

        cache(['juggernaut.published_migrations' => $published], now()->addMinutes(5));
    }



}


