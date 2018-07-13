#!/bin/sh

cp -r /var/www/alternate-html/* /var/www/html/wp-content/plugins/
exec "$@"