#!/bin/bash

mkdir -p /var/www/app/data/favicons
chown -R nginx:nginx /var/www/app/data
exec /bin/s6-svscan /etc/services.d

