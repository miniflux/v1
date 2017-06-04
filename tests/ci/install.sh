#!/bin/sh

sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf

if [ "$TRAVIS_PHP_VERSION" = "7.0" -a -n "$(ls -A ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d)" ]; then
    sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf
fi

echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "always_populate_raw_post_data = -1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
echo "opcache.enable = 0" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm

sudo a2enmod rewrite actions fastcgi alias ssl

sudo cp -f tests/ci/apache_vhost.conf /etc/apache2/sites-available/default
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/default
sudo service apache2 restart

cp assets/img/

ls -al
