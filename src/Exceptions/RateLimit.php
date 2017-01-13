<?php

namespace Atomescrochus\ItunesStore\Exceptions;

use Exception;

class RateLimit extends Exception
{
    public static function maxedOut()
    {
        return new static("Too close to iTunes Search API rate limit of 20 calls per minutes. Try again in a few seconds.");
    }
}
