# Changelog

All Notable changes to `laravel-itunes-store-api` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 2.0.1 - 2016-10-13
### Fixed
- Caching logic

## 2.0.0 - 2016-10-13
### Added
- Now with facades, as `ItunesSearch`

### Fixed
- Setting a cache time of `0` now corrently ignore caching

### Deprecated
- Method `search()` is now `query()` to be more consistent with actual usage
- Method `executeSearch()` is now `search()` to be more consistent with actual usage
- Method `cache()` is now `setCacheDuration()` to be more consistent with actual usage

## 1.0.3 - 2016-10-13
### Fixed
- Cache name would change on every request, causing the package to store, but not being able to retreive old cached items

## 1.0.2 - 2016-10-12
### Added
- Fixed a typo in composer.json making the pakcage unrocognizable in packagist

## 1.0.1 - 2016-10-12
### Added
- A parameter to the result object to know if the result is coming from cached results

## 1.0.0 - 2016-10-12
### Added
- Config file with default cache time
- Added a search method to search the Store API
- Added a cache method to set a number of minutes to cache for, overriding the default one in config
- Added a mechanism to guess if we're rate limited and a parameter to the result object to display if we are

## 0.0.1 - 2016-10-12

### Added
- Base config. Tagging a release for packagist convenience

## Please ignore below this line!
## Unreleased - YYYY-MM-DD
### Added
### Deprecated
### Fixed
### Removed
### Security
