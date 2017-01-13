<?php

namespace Atomescrochus\ItunesStore;

use Illuminate\Support\Facades\Cache;

class ItunesSearchAPI
{
    private $cache_time;
    private $searchApiRateLimit;
    private $request_url;
    private $parameters;

    public function __construct()
    {
        $this->cache_time = empty(config('laravel-itunes-search-api.cache')) ? 60 : config('laravel-itunes-search-api.cache');
        $this->searchApiRateLimit = (object) ['numberOfCalls' => 20, 'perAmountOfSeconds' => 60];
        $this->request_url = "https://itunes.apple.com";
        $this->parameters = [];
    }

    public function setCacheDuration(int $minutes)
    {
        $this->cache_time = $minutes;

        return $this;
    }

    public function query($terms, $extra_parameters = ['limit' => 15])
    {
        $this->endpoint = "/search";

        $parameters = ['term' => $terms];
        $this->parameters = array_merge($parameters, $extra_parameters);

        return $this->search();
    }

    public function search()
    {
        $cache_name = md5($this->getRequestUrl());
        $cached_response = Cache::has($cache_name);
        
        if ($cached_response) {
            return Cache::get($cache_name);
        }

        $response = \Httpful\Request::get($this->getRequestUrl())->expectsJson()->send();
        
        if ($response->code == 200) {
            return  Cache::remember($cache_name, $this->cache_time, function () use ($response) {
                return $this->formatApiResults($response);
            });
        }

        return  $this->formatApiResults($response, false, true);
    }

    private function formatApiResults($result, $cached = true, $rateLimited = false)
    {
        $raw = $result->raw_body;
        $response = $result->body ? $result->body : null;

        return (object) [
            'results' => collect($response->results),
            'count' => $response->resultCount,
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
