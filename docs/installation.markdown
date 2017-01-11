Installation instructions
=========================

Requirements
------------

- PHP >= 5.3.3 (PHP 7.x recommended)
- PHP extensions: ctype, dom, hash, iconv, json, xml, mbstring, pdq_sqlite/pdo_pgsql, pcre, session, SimpleXML (curl is recommended or set `allow_url_fopen=On`)
- Sqlite 3 or Postgres >= 9.3
- libxml2 >= 2.7.x

Installation
------------

### From the archive (stable version)

1. You must have a web server with PHP installed (version 5.3.3 minimum) with the Sqlite and XML extensions
2. Download the [latest release](https://miniflux.net/downloads) and copy the directory `miniflux` where you want
3. Check if the directory `data` is writeable (Miniflux stores everything inside a Sqlite database)
4. With your browser go to <http://yourpersonalserver/miniflux>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

### From the repository (development version)

1. `git clone https://github.com/miniflux/miniflux.git`
2. Go to the third step just above

By default, Miniflux uses Sqlite, if you would like to use Postgres or MySQL instead you will have to modify your `config.php` file.

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache but nothing for Nginx.
