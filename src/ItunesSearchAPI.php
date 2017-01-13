<?php

namespace Atomescrochus\ItunesStore;

use Illuminate\Support\Facades\Cache;

class ItunesSearchAPI
{
    private $cache_time;
    private $searchApiRateLimit;
    private $request_url;
    private $parameters;
    private $endpoint;

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

    public function lookup($id, $type = 'id', $extra_parameters = [])
    {
        $this->endpoint = "/lookup";
        $parameters = [$type => $id];
        $this->parameters = array_merge($parameters, $extra_parameters);

        return $this->search();
    }

    public function search()
    {
        $cache_name = md5($this->getRequestUrl());
        $cache = $this->checkForCache($cache_name);
        
        if (isset($cache->content)) {
            return $cache->content;
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
