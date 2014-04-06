Multiple users
==============

Since the begining, Miniflux was a single user software.
However, it is now possible to create multiple databases.
Each user have his own Sqlite database and people can choose which database they want to use before the authentication.

To create a new database:

1. Go to the preferences page
2. Scroll-down to the database section
3. Click on the link "Add a new database"
4. Fill the form (db name, the new username and password) and save
5. If you logout, on the login page you can choose the database you want to use

If you use the cronjob, to select the database, use the parameter `--database` like that:

```bash
php cronjob.php --database=db2.sqlite
```
