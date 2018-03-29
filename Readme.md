# Magento 2 Debug
This module is based on [Magento 1 Profiler](https://github.com/ecoco/magento_profiler) with some extra features. The main goal was to create it as a module with simplified installation process.

## Requirements  
- Magento 2

## Instalation
It should be simple as:
```
composer require --dev clawrock/m2-debug
php bin/magento setup:upgrade
```

## Features
- Data collectors:
    - Ajax
    - Cache
    - Config
    - Customer
    - Database
    - Events
    - Layout
    - Memory
    - Models
    - Request/Response
    - Performance
    - Translations
- Whoops error handler

## Configuration
Please navigate to `Stores -> Configuration -> ClawRock -> Debug`

For Database Collector you have to [enable Magento DB Profiler](http://devdocs.magento.com/guides/v2.2/config-guide/db-profiler/db-profiler.html)

## TODO
- Improved template hints
