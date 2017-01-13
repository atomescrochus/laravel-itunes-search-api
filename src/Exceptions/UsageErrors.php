<?php

namespace Atomescrochus\ItunesStore\Exceptions;

use Exception;

class UsageErrors extends Exception
{
    public static function lookupTypes()
    {
        return new static('This type of lookup is invalid.');
    }

    public static function parameters($parameter)
    {
        return new static("The parameter '{$parameter}' is invalid.");
    }
}
