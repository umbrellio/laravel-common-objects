#!/usr/bin/env bash

COMPOSER_MEMORY_LIMIT=-1 composer update
composer lint
php -d pcov.directory='.' vendor/bin/phpunit --coverage-html build --coverage-text

