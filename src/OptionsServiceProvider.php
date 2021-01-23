<?php


namespace Vinlon\Laravel\Options;


use Illuminate\Support\ServiceProvider;

class OptionsServiceProvider extends ServiceProvider
{

     /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // 加载 migration 文件
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');
    }
}