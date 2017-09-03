# laravel-itunes-store-api

Here is to have a simple way to interact with the iTunes Store API from a Laravel >= 5.3 app.

This package is usable in production, but should still be considered as a _work in progress_. Found a bug? Got a feature request? Open an issue or better, send a PR!

## Install

You can install this package via composer:

``` bash
$ composer require atomescrochus/laravel-itunes-search-api
```

If you are _on Laravel >=5.5, the package will be working with the auto-discovery feature_. For earlier versions, you will have to install the package' service provider and alias:

```php
// config/app.php
'providers' => [
    ...
    Atomescrochus\ItunesStore\ItunesSearchAPIProvider::class,
];

// no need to add aliases, the packages bind itselfs as "ItunesSearch"
```

You will have to publish the configuration files also if you want to change the default value:
```bash
php artisan vendor:publish --provider="Atomescrochus\ItunesStore\ItunesSearchAPIProvider" --tag="config"
```

## Usage

``` php
// here is an example query to search iTunes Store's API

// Set cache duration as an integer (number of minutes), can be 0
ItunesSearch::setCacheDuration(120) // optional, default is set in config

// You can execute a basic search, and hope for the best
$results = ItunesSearch::query("poker face lady gaga"); // limited to 15 results by default

// You can also send an optional array of other parameters supported by the API, for example
$results = ItunesSearch::query("poker face lady gaga", ['country' => 'CA', 'limit' => 10]);

// You can also execute a lookup (https://affiliate.itunes.apple.com/resources/documentation/itunes-store-web-service-search-api/#lookup)
$results = ItunesSearch::lookup(902122445); // defaults to lookup by iTunes Store ID
$results = ItunesSearch::lookup(468749, 'amgArtistId') // you can do other type of lookups
$results = ItunesSearch::lookup("468749,909253", 'amgArtistId'); // you can also search for multiple ids like so

// And like the query() method, you can send an optional array of parameters
$results = ItunesSearch::lookup(909253, 'id', ['country' => 'CA', 'limit' => 10]); 

// If you ever find yourself that you want to check your local cache for a result, without actually
// polling the Search API (for example, in cache you already know you've been rate limited):
$results = ItunesSearch::cacheOnly()->query("poker face lady gaga");
// if you want to poll again after setting cacheOnly(), you need to reverse the behavior
$results = ItunesSearch::cacheOnly(false)->lookup(909253); 

// In case you only want to check if the query exists in the cache, without actually pulling the data:
$existInCache = ItunesSearch::inCache()->query("poker face lady gaga"); // returns boolean
$existInCache = ItunesSearch::inCache()->lookup(909253); // returns boolean
```

### Caching and iTunes Store API's rate limiting
Curently, the API is "[limited to approximately 20 calls per minute (subject to change)](https://affiliate.itunes.apple.com/resources/documentation/itunes-store-web-service-search-api/)". (Approvimately!)

For now, the only way to know that you're on the rather erratic Store's rate limit is if we hit an HTTP response of `403 Forbidden`. There is no way to know when it expires, or how many call you have left, or anything usefull for that matter (yep, this sucks).

To help you manage the rate limiting, we  provide a parameter to the result object returned by the search called `rateLimited`.  If set to `true`, we encountered a `403` and it means that you are rate limited.

Of course, we cannot stop you from hitting the API even if you are rate limited, so it's _your_ duty to make sure you stope for a little while if `rateLimited` is set to true.

One last thing on rate limiting: since we usually cache results by default, if we ever encounter a `403`, we return an empty result _without_ caching results, without consideration to the caching setting you could have set. This way, if you make the same call again within the normal caching time, but are not rate limited again, you won't get an empty result.

### Results
 
In the example above, what is returned in `$results` is an object containing: a collection of results; a count value for the results; a boolean to know if we are rate limited, a boolean to know if the result is coming from cached data, a boolean to know if we requested to search in the cache only, raw response data; and the unformatted query sent to the API.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

Soon.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jp@atomescroch.us instead of using the issue tracker.

## Credits

- [Jean-Philippe Murray][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.