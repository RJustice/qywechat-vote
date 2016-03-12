<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class QyWechat extends ServiceProvider
{

    protected $defer = false;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('qywechat',function(){
            return \App\Classes\QyWechat::class;
        });
        
    }
}
