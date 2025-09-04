#!/bin/bash

# PHP executable script that uses the specified PHP binary
# Usage: ./php artisan [command] [options]

PHP_BINARY="/opt/alt/php83/usr/bin/php"

# Check if the PHP binary exists
if [ ! -f "$PHP_BINARY" ]; then
    echo "Error: PHP binary not found at $PHP_BINARY"
    exit 1
fi

# Check if the binary is executable
if [ ! -x "$PHP_BINARY" ]; then
    echo "Error: PHP binary is not executable at $PHP_BINARY"
    exit 1
fi

# Pass all arguments to the PHP binary
exec "$PHP_BINARY" "$@"
