#!/usr/bin/env bash


# bash to check if a file exists
if [ -f /tmp/queued_restart.pid ]; then
    echo "queued.pid exists."
    nginx -s reload
    php8.2-fpm restart
    echo "reloaded nginx and restarted php-fpm"
    rm /tmp/queued_restart.pid
fi
