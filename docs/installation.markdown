Installation instructions
=========================

Installation
------------

### From the archive (stable version)

1. You must have a web server with PHP installed (version 5.3.3 minimum) with the Sqlite and XML extensions
2. Download the source code and copy the directory `miniflux` where you want
3. Check if the directory `data` is writeable (Miniflux stores everything inside a Sqlite database)
4. With your browser go to <http://yourpersonalserver/miniflux>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

### From the repository (development version)

1. `git clone https://github.com/miniflux/miniflux.git`
2. Go to the third step just above

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache but nothing for Nginx.
