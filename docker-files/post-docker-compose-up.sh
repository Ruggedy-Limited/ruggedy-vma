#!/usr/bin/env bash
cd /usr/share/nginx/html/ruggedy-vma
composer install
php artisan key:generate
php artisan doctrine:generate:proxies
php artisan migrate
