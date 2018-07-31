<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/3/6
 * Time: 上午11:32
 */

namespace Snower\Phslock\Laravel;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Snower\Phslock\Client;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot the provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/config.php');

        if ($this->app instanceof LaravelApplication) {
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $source => config_path('phslock.php'),
                ]);
            }
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('phslock');
        }

        $this->mergeConfigFrom($source, 'phslock');
    }

    /**
     * Register the provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $phslock = new Client(config('phslock.host'), config('phslock.port'));
            return $phslock;
        });

        $this->app->alias(Client::class, 'phslock');
    }

    /**
     * Get config value by key.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function config($key, $default = null)
    {
        return $this->app->make('config')->get("phslock.{$key}", $default);
    }
}