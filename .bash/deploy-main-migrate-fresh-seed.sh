#!/bin/sh

composer self-update --2 --stable

php artisan down --render="errors::503" --refresh=5

php artisan optimize:clear

git pull origin main

composer i --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service php8.4-fpm reload ) 9>/tmp/fpmlock-ecommerce

php artisan migrate:fresh --seed --force

composer i --no-interaction --prefer-dist --optimize-autoloader --no-dev

php artisan optimize

php artisan reload

php artisan up
