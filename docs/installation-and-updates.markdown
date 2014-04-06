Installation and Updates
========================

Installation
------------

### From the archive:

1. You must have a web server with PHP installed (version 5.3.3 minimum) with the Sqlite and XML extensions
2. Download the source code and copy the directory `miniflux` where you want
3. Check if the directory `data` is writeable (Miniflux stores everything inside a Sqlite database)
4. With your browser go to <http://yourpersonalserver/miniflux>
5. The default login and password is **admin/admin**
6. Start to use the software
7. Don't forget to change your password!

### From the repository:

1. `git clone https://github.com/fguillot/miniflux.git`
2. Go to the third step just above

Update
------

### From the archive:

1. Close your session (logout)
2. Rename your actual miniflux directory (to keep a backup)
3. Uncompress the new archive and copy your database file `db.sqlite` in the directory `data`
4. Make the directory `data` writeable by the web server user
5. Login and check if everything is ok
6. Remove the old miniflux directory

### From the repository:

1. Close your session (logout)
2. `git pull`
3. Login and check if everything is ok

Security
--------

- Don't forget to change the default user/password
- Don't allow everybody to access to the directory `data` from the URL. There is already a `.htaccess` for Apache but nothing for Nginx.
