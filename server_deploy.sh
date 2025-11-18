#!/bin/sh
set -e

echo "Deploying application ..."

# Enter maintenance mode
(/opt/alt/php83/usr/bin/php artisan down) || true
    # Update codebase
    # git fetch origin production
    # git reset --hard origin/production
    git pull origin master --force

    /opt/alt/php83/usr/bin/php "$([ -f "./composer.phar" ] && echo "./composer.phar" || command -v composer || echo /opt/cpanel/composer/bin/composer)" install \
        --no-interaction --prefer-dist --optimize-autoloader --no-progress \
        $(if [ "$1" = "--no-dev" ]; then echo "--no-dev"; fi)

    # Ensure all tables use InnoDB before running migrations
    /opt/alt/php83/usr/bin/php artisan db:convert-innodb || true

    # Migrate database
    /opt/alt/php83/usr/bin/php artisan migrate --force

    # Note: If you're using queue workers, this is the place to restart them.
    # ...

    /opt/alt/php83/usr/bin/php artisan responsecache:clear || true

    # Clear cache
    /opt/alt/php83/usr/bin/php artisan optimize:clear

    # Warm up caches
    /opt/alt/php83/usr/bin/php artisan optimize

    # Reload PHP to update opcache
    # echo "" | sudo -S service php7.4-fpm reload
# Exit maintenance mode
/opt/alt/php83/usr/bin/php artisan up

echo "Application deployed!"
