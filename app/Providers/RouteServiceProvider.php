<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $srcFolders = base_path('src');

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () use ($srcFolders) {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            $this->dinamicRoutes($srcFolders);
        });
    }

    private function dinamicRoutes(string $srcFolders): void
    {
        if (File::exists($srcFolders) && null !== $folders = File::allFiles($srcFolders)) {
            foreach ($folders as $folder) {
                $routeWeb = $folder->getPath() . '\web.php';
                $routeApi = $folder->getPath() . '\api.php';
                if (File::exists($routeApi)) {
                    Route::middleware('api')
                        ->prefix('api/' . strtolower(explode('\\', $folder->getRelativePath())[0] . 's'))
                        ->group($routeApi);
                }

                if (File::exists($routeWeb)) {
                    Route::middleware('web')
                        ->prefix(strtolower(explode('\\', $folder->getRelativePath())[0] . 's'))
                        ->group($routeWeb);
                }
            }
        }
    }
}
