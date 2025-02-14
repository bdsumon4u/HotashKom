#!/bin/sh
set -e

echo "Deploying application ..."

# Check if --dev argument is passed
if [ "$1" = "--dev" ]; then
    branch="dev"
else
    branch="master"
fi

# Enter maintenance mode
(/opt/alt/php82/usr/bin/php artisan down) || true
    # Update codebase
    # git fetch origin production
    # git reset --hard origin/production
    git pull origin $branch --force

    # Install dependencies based on lock file
    /opt/alt/php82/usr/bin/php /opt/cpanel/composer/bin/composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress $(if [ branch != "dev" ]; then echo "--no-dev"; fi)

    # Migrate database
    /opt/alt/php82/usr/bin/php artisan migrate --force

    # Note: If you're using queue workers, this is the place to restart them.
    # ...

    # Clear cache
    /opt/alt/php82/usr/bin/php artisan optimize

    # Reload PHP to update opcache
    # echo "" | sudo -S service php7.4-fpm reload
# Exit maintenance mode
/opt/alt/php82/usr/bin/php artisan up

echo "Application deployed!"
