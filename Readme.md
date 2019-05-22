[![Packagist](https://img.shields.io/packagist/v/clawrock/magento2-debug.svg)](https://packagist.org/packages/clawrock/magento2-debug)
[![Packagist](https://img.shields.io/packagist/dt/clawrock/magento2-debug.svg)](https://packagist.org/packages/clawrock/magento2-debug)
[![Build Status](https://travis-ci.org/clawrock/magento2-debug.svg?branch=master)](https://travis-ci.org/clawrock/magento2-debug)
[![Coverage Status](https://coveralls.io/repos/github/clawrock/magento2-debug/badge.svg)](https://coveralls.io/github/clawrock/magento2-debug)

# Magento 2 - Debug module
Module for debugging Magento 2 performance. It works without overwriting any core files and it can be installed with composer.

## Installation
1. Enable developer mode `php bin/magento deploy:mode:set developer`
2. Install module via composer `composer require --dev clawrock/magento2-debug`
3. Register module `php bin/magento setup:upgrade`
4. Enable profiler in configuration: `Stores -> Configuration -> Advanced -> Debug`

## Configuration
All settings have only default scope and config type pool is set to environment for better integration with `php bin/magento app:config:dump`

## Compatibility
* Magento 2.2 - 2.3
* PHP 7.0 - 7.2

## Profiler collectors
- Ajax
- Cache
- Config
- Customer
- Database
- Events
- Layout
- Memory
- Models
- Plugins
- Request/Response
- Performance
- Translations
    
## Additional features
- [Whoops error handler](http://filp.github.io/whoops/)

## Credits
- [Magento 1.x Web Profiler](https://github.com/ecoco/magento_profiler)
- [Symfony WebProfilerBundle](https://github.com/symfony/web-profiler-bundle)
