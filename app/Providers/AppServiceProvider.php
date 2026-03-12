<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register broadcasting auth route with Sanctum (Bearer token) middleware.
        // NOTE: channels: must NOT be in withRouting() in bootstrap/app.php — if it is,
        // Laravel calls Broadcast::routes() with web middleware AFTER this runs, overriding it.
        Broadcast::routes(['middleware' => ['auth:sanctum']]);

        // Load channel authorization definitions manually (since we removed channels: from withRouting).
        require base_path('routes/channels.php');
    }
}
