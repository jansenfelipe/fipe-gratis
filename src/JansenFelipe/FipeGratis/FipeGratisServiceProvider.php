<?php

namespace JansenFelipe\FipeGratis;

use Illuminate\Support\ServiceProvider;

class FipeGratisServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->package('JansenFelipe/fipe-gratis');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('fipe_gratis', function() {
            return new \JansenFelipe\FipeGratis\FipeGratis;
        });
    }

}
