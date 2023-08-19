<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Matches 'id' parameter in routes to only allow digits (e.g., 1, 42, 100).
        Route::pattern('id', '\d+');

        // Matches 'offset' parameter in routes to only allow one or more digits (e.g., 0, 5, 50).
        Route::pattern('offset', '[0-9]+');

        // Matches 'limit' parameter in routes to only allow one or more digits (e.g., 10, 20, 50).
        Route::pattern('limit', '[0-9]+');

        // Matches 'hash' parameter in routes to allow lowercase letters and digits (e.g., ab123, 4de56).
        Route::pattern('hash', '[a-z0-9]+');

        // Matches 'hex' parameter in routes to allow hexadecimal values (e.g., af12bc, deadbeef).
        Route::pattern('hex', '[a-f0-9]+');

        // Matches 'uuid' parameter in routes to match the common UUID format (e.g., 550e8400-e29b-41d4-a716-446655440000).
        Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

        // Matches 'base' parameter in routes to allow alphanumeric characters (e.g., ab12, AbC123).
        Route::pattern('base', '[a-zA-Z0-9]+');

        // Matches 'slug' parameter in routes to allow lowercase letters, digits, and dashes (e.g., post-title, article-123).
        Route::pattern('slug', '[a-z0-9-]+');

        // Matches 'username' parameter in routes to allow lowercase letters, digits, underscores, and dashes with a length of 3 to 16 characters.
        Route::pattern('username', '[a-z0-9_-]{3,16}');

        // Matches strings that consist solely of letters, numbers, underscores, and hyphens from start to finish.
        Route::pattern('param', '^[\w-]+$');

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
