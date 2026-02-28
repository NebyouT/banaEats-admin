<?php

namespace App\Providers;

use App\Services\AIService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService();
        });

        // Register AI service alias
        $this->app->alias(AIService::class, 'ai');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Configure logging channel for AI
        $this->configureLogging();
    }

    /**
     * Configure AI logging
     *
     * @return void
     */
    private function configureLogging()
    {
        if (config('ai.logging.enabled', true)) {
            $this->app->make('config')->set('logging.channels.ai', [
                'driver' => 'single',
                'path' => storage_path('logs/ai.log'),
                'level' => config('ai.logging.level', 'info'),
                'replace_placeholders' => true,
            ]);
        }
    }
}
