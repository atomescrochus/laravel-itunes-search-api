# laravel-itunes-store-api

Here is to have a simple way to interact with the iTunes Store API from a Laravel >= 5.3 app.

**Not suitable for production yet.**

## Install

You can install this package via composer:

``` bash
$ composer require atomescrochus/laravel-itunes-search-api
```

Then you have to install the package' service provider and alias:

```php
// config/app.php
'providers' => [
    ...
    Atomescrochus\ItunesStore\ItunesSearchAPIProvider::class,
];
```

You will have to publish the configuration files also if you want to change the default value:
```bash
php artisan vendor:publish --provider="Atomescrochus\ItunesStore\ItunesSearchAPIProvider" --tag="config"
```

## Usage

``` php
// here is an example query to search Deezer's API
$deezer = new \Atomescrochus\ItunesStore\ItunesSearchAPI();

// You can execute a basic search, and hope for the best
$results = $itunes->search("poker face lady gaga");

// These are the options you can set with every kind of call
$deezer->cache(120) // an integer (number of minutes), for the cache to expire, can be 0, default is set in config
```

### Results
 
In the example above, what is returned in `$results` is an object containing: a collection of results; a count value for the results; raw response data; and the unformatted query sent to the API.

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