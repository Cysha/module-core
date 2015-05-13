<?php namespace Cms\Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class BaseEventsProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [

    ];
}
