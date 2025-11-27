<?php

namespace JuggernautLab\MicroserviceHelper;

use JuggernautLab\MicroserviceHelper\Commands\MakeClassCommand;
use JuggernautLab\MicroserviceHelper\Commands\MakeEnumCommand;
use JuggernautLab\MicroserviceHelper\Commands\MakeMigrationCommand;
use JuggernautLab\MicroserviceHelper\Commands\PublishMigrationsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasCommands([PublishMigrationsCommand::class, MakeMigrationCommand::class, MakeClassCommand::class, MakeEnumCommand::class]);
    }

    // public function bootingPackage()
    // {
    //     $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    // }

    public function bootingPackage()
    {
        $this->publishesMigrationsSafely();
    }

    protected function publishesMigrationsSafely()
    {
        $migrations = glob(__DIR__.'/../database/migrations/*.php');
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
    }
}
