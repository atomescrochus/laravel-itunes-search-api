<?php

namespace Atomescrochus\ItunesStore\Facades;

use Illuminate\Support\Facades\Facade;

class ItunesSearch extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'atomescrochus.itunes';
    }
}
