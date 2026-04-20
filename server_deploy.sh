#!/bin/sh
set -e

echo "Deploying application ..."

# Enter maintenance mode
(./php artisan down) || true
    # Update codebase
    # git fetch origin production
    # git reset --hard origin/production
    git pull origin master --force

    COMPOSER="$([ -f "./composer.phar" ] && echo "./composer.phar" || command -v composer || echo /opt/cpanel/composer/bin/composer)"
    ./php "$COMPOSER" install \
        --no-interaction --prefer-dist --optimize-autoloader --no-progress \
        $(if [ "$1" = "--no-dev" ]; then echo "--no-dev"; fi)

    # Ensure all tables use InnoDB before running migrations
    ./php artisan db:convert-innodb || true

    # Migrate database
    ./php artisan migrate --force

    # Note: If you're using queue workers, this is the place to restart them.
    # ...

    ./php artisan responsecache:clear || true

    # Clear cache
    ./php artisan optimize:clear

    # Warm up caches
    ./php artisan optimize

    # Reload PHP to update opcache
    # echo "" | sudo -S service php7.4-fpm reload
# Exit maintenance mode
./php artisan up

echo "Application deployed!"
