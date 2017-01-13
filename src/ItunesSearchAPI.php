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

    public function search($terms, $extra_parameters = ['limit' => 15])
    {
        $this->endpoint = "/search";

        $parameters = ['term' => $terms];
        $this->parameters = array_merge($parameters, $extra_parameters);

        $cache_name = bcrypt($this->getRequestUrl());
        $response = \Httpful\Request::get($this->getRequestUrl())->expectsJson()->send();

        $results =  Cache::remember($cache_name, $this->cache_time, function () use ($response) {
            return $this->formatApiResults($response);
        });

        return $results;
    }

    public function cache(int $minutes)
    {
        $this->cache_time = $minutes;

        return $this;
    }

    private function formatApiResults($result)
    {
        $raw = $result->raw_body;
        $response = $result->body ? $result->body : null;

        return (object) [
            'results' => collect($response->results),
            'count' => $response->resultCount,
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
