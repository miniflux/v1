Multiple users
==============

Since the beginning, Miniflux was a single user software.
However, it is now possible to create multiple databases.
Each user has his own Sqlite database and people can choose which database they want to use before the authentication.

To create a new database:

1. Go to the page **preferences > database**
2. Click on the link "Add a new database"
3. Fill the form (db name, the new username and password) and save
4. If you logout, on the login page you can choose the database you want to use

If you use the cronjob, to select the database, use the parameter `--database` like that:

```bash
php cronjob.php --database=db2.sqlite
```
