<?php

namespace ArleyDu\Weather;

/**
 * Class ServiceProvider
 *
 * @package \ArleyDu\Weather
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register ()
    {
        $this->app->singleton( Weather::class, function () {
            return new Weather( config( 'services.weather.key' ) );
        } );
        $this->app->alias( Weather::class, 'weather' );
    }

    public function provider ()
    {
        return [ Weather::class, 'weather' ];
    }
}