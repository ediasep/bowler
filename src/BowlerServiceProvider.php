<?php

namespace Ediasep\Bowler;

use Illuminate\Support\ServiceProvider;

class BowlerServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Ediasep\Bowler\Console\MigrationCommand',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */

    public function register(){
        $this->commands($this->commands);
    }
}