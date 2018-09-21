#!/bin/bash

php artisan db:create
php artisan migrate:refresh --seed

php-fpm