<?php

namespace App\Providers;

use App\Helper\Pagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->alias(Pagination::class, LengthAwarePaginator::class);
        $this->app->alias(Pagination::class, LengthAwarePaginatorContract::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $srcFolders = base_path('src');
        $migrationFolders = $this->dinamicMigrations($srcFolders);
        $this->loadMigrationsFrom($migrationFolders);
    }

    private function dinamicMigrations(string $srcFolders): array
    {
        $migrations = [];
        if (File::exists($srcFolders) && null !== $folders = File::allFiles($srcFolders)) {
            foreach ($folders as $folder) {
                $migrations[] = $folder->getPath();
            }
        }

        return $migrations;
    }
}
