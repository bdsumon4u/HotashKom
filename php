#!/bin/bash

# PHP executable script that uses the specified PHP binary
# Usage: ./php artisan [command] [options]


####################################
# FIND WORKING PHP BINARY
####################################
find_php() {
    for php in /opt/cpanel/ea-php84/root/usr/bin/php /opt/alt/php84/usr/bin/php /usr/bin/php; do
        if [[ -x "$php" ]]; then
            echo "$php"
            return 0
        fi
    done
    return 1
}

PHP=$(find_php) || {
    echo "❌ No PHP binary found"
    exit 1
}

echo "▶ Using PHP: $PHP"

# Check if the PHP binary exists
if [ ! -f "$PHP" ]; then
    echo "Error: PHP binary not found at $PHP"
    exit 1
fi

# Check if the binary is executable
if [ ! -x "$PHP" ]; then
    echo "Error: PHP binary is not executable at $PHP"
    exit 1
fi

# Pass all arguments to the PHP binary
exec "$PHP" "$@"
