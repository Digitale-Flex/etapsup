<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Opcodes\LogViewer\Facades\LogViewer;

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
        Vite::prefetch(concurrency: 6);
        Inertia::encryptHistory(true);
        JsonResource::withoutWrapping();

        Gate::before(function ($user, $ability) {
            return $user->hasRole(['admin', 'dev']) ? true : null;
        });

        //DB::prohibitDestructiveCommands($this->app->isProduction());

        LogViewer::auth(function ($request) {
            return  $request->user()?->hasRole(['admin', 'dev']);
        });

        // Sprint1 Feature 1.8.1 - Rate limiting pour upload documents
        RateLimiter::for('documents', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
