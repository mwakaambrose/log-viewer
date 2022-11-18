<?php

namespace App\Providers;

use App\LogFile;
use App\LogFolder;
use Livewire\Livewire;
use App\LogViewerService;
use App\Facades\LogViewer;
use App\Events\LogFileDeleted;
use App\Http\Livewire\LogList;
use App\Http\Livewire\FileList;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Console\Commands\GenerateDummyLogsCommand;

class LogViewerServiceProvider extends ServiceProvider
{
    private string $name = 'log-viewer';

    public function register()
    {
        $path = config_path();
        $this->mergeConfigFrom("$path/{$this->name}.php", $this->name);

        $this->app->bind('log-viewer', LogViewerService::class);
        $this->app->singleton(PreferenceStore::class, PreferenceStore::class);
    }

    public function boot()
    {
        $path = config_path();
        if ($this->app->runningInConsole()) {
            // registering the command
            $this->commands([GenerateDummyLogsCommand::class]);
        }

        if (! $this->isEnabled()) {
            return;
        }

        // registering routes
        $this->loadRoutesFrom($this->basePath('/routes/web.php'));

        // registering views
        $this->loadViewsFrom($this->basePath('/resources/views'), $this->name);

        Livewire::component('log-viewer::file-list', FileList::class);
        Livewire::component('log-viewer::log-list', LogList::class);

        Event::listen(LogFileDeleted::class, function (LogFileDeleted $event) {
            LogViewer::clearFileCache();
        });

        if (! Gate::has('downloadLogFile')) {
            Gate::define('downloadLogFile', fn (mixed $user, LogFile $file) => true);
        }

        if (! Gate::has('downloadLogFolder')) {
            Gate::define('downloadLogFolder', fn (mixed $user, LogFolder $folder) => true);
        }

        if (! Gate::has('deleteLogFile')) {
            Gate::define('deleteLogFile', fn (mixed $user, LogFile $file) => true);
        }

        if (! Gate::has('deleteLogFolder')) {
            Gate::define('deleteLogFolder', fn (mixed $user, LogFolder $folder) => true);
        }
    }

    private function basePath(string $path): string
    {
        return __DIR__ . '/../..' . $path;
    }

    private function isEnabled(): bool
    {
        return (bool) $this->app['config']->get("{$this->name}.enabled", true);
    }
}
