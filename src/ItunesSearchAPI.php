<?php

namespace Atomescrochus\ItunesStore;

use Atomescrochus\ItunesStore\Exceptions\UsageErrors;
use Illuminate\Support\Facades\Cache;

class ItunesSearchAPI
{
    private $cache_time;
    private $searchApiRateLimit;
    private $request_url;
    private $parameters;
    private $endpoint;
    private $possible_lookup_types;
    private $possible_parameters;
    private $cacheOnly;

    public function __construct()
    {
        $this->cache_time = empty(config('laravel-itunes-search-api.cache')) ? 60 : config('laravel-itunes-search-api.cache');
        $this->searchApiRateLimit = (object) ['numberOfCalls' => 20, 'perAmountOfSeconds' => 60];
        $this->request_url = "https://itunes.apple.com";
        $this->parameters = [];
        $this->cacheOnly = false;
        $this->possible_lookup_types = [
            'id',
            'amgArtistId',
            'amgAlbumId',
            'amgVideoId',
            'upc',
            'isbn'
        ];
        $this->possible_parameters = [
            'term',
            'country',
            'media',
            'entity',
            'attribute',
            'callback',
            'limit',
            'lang',
            'version',
            'explicit'

        ];
    }

    public function setCacheDuration(int $minutes)
    {
        $this->cache_time = $minutes;

        return $this;
    }

    public function cacheOnly(bool $cacheOnly = true)
    {
        $this->cacheOnly = $cacheOnly;

        return $this;
    }

    public function query($terms, $extra_parameters = ['limit' => 15])
    {
        $this->endpoint = "/search";

        $parameters = array_merge(['term' => $terms], $extra_parameters);
        $this->setParameters($parameters);

        return $this->search();
    }

    public function lookup($id, $type = 'id', $extra_parameters = [])
    {
        if (!in_array($type, $this->possible_lookup_types)) {
            throw UsageErrors::lookupTypes();
        }

        $this->endpoint = "/lookup";
        $parameters = array_merge([$type => $id], $extra_parameters);
        $this->setParameters($parameters, true);

        return $this->search();
    }

    public function search()
    {
        $cache_name = md5($this->getRequestUrl());
        $cache = $this->checkForCache($cache_name);
        
        if (isset($cache->content)) {
            $cached = $cache->content;
            $cached->cacheOnly = $this->cacheOnly;
            return $cached;
        }

        if ($this->cacheOnly) { // here, there is no cached content, and we've asked only for the cache...
            return (object) [
                'results' => collect([]),
                'count' => 0,
                'rateLimited' => false,
                'cached' => false,
                'cacheOnly' => true,
                'query' => urldecode($this->getRequestUrl())
            ];
        }

        $response = \Httpful\Request::get($this->getRequestUrl())->expectsJson()->send();
        
        if ($response->code == 200 && $cache->shouldCache == true) {
            return  Cache::remember($cache_name, $this->cache_time, function () use ($response) {
                return $this->formatApiResults($response);
            });
        }

        if ($response->code == 403) {
            return  $this->formatApiResults($response, false, true);
        }

        return  $this->formatApiResults($response, false);
    }

    private function setParameters($parameters, $isLookup = false)
    {
        $possibilities = $this->possible_parameters;

        if ($isLookup == true) {
            $possibilities = array_merge($possibilities, $this->possible_lookup_types);
        }

        foreach ($parameters as $parameter => $value) {
            if (!in_array($parameter, $possibilities)) {
                throw UsageErrors::parameters($parameter);
            }
        }

        $this->parameters = $parameters;
    }

    private function checkForCache($name)
    {
        $cache = (object) ['content' => null, 'shouldCache' => true];

        if (Cache::has($name)) {
            $cache->content = Cache::get($name);
        }

        if ($this->cache_time == 0) {
            $cache->shouldCache = false;
            $cache->content = null;
        }

        return $cache;
    }

    private function formatApiResults($result, $cached = true, $rateLimited = false)
    {
        $raw = $result->raw_body;

        if (isset($result->body)) {
            return (object) [
                'results' => collect($result->body->results),
                'count' => $result->body->resultCount,
                'rateLimited' => $rateLimited,
                'cached' => $cached,
                'cacheOnly' => $this->cacheOnly,
                'raw' => json_decode($raw),
                'query' => urldecode($this->getRequestUrl()),
            ];
        }

        return (object) [
            'results' => collect([]),
            'count' => 0,
            'rateLimited' => $rateLimited,
            'cached' => $cached,
            'raw' => json_decode($raw),
            'query' => urldecode($this->getRequestUrl()),
        ];
    }

    private function getRequestUrl()
    {
        $parameters = http_build_query($this->parameters);
        return "{$this->request_url}{$this->endpoint}?{$parameters}";
    }
}
