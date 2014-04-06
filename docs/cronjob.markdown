Cronjob (background feeds update)
=================================

How do I update my feeds with a cronjob?
----------------------------------------

You just need to be inside the directory `miniflux` and run the script `cronjob.php`.


Parameters          | Type                           | Value
--------------------|--------------------------------|-----------------------------
--database          | optional                       | Database filename, default is db.sqlite (ex: db2.sqlite)
--limit             | optional                       | number of feeds
--call-interval     | optional, excluded by --limit, require --update-interval | time in minutes < update interval time
--update-interval   | optional, excluded by --limit, require --call-interval   | time in minutes >= call interval time


Examples:

    crontab -e

    # Update all feeds every 4 hours
    0 */4 * * *  cd /path/to/miniflux && php cronjob.php >/dev/null 2>&1

    # Update the 10 oldest feeds each time
    0 */4 * * *  cd /path/to/miniflux && php cronjob.php --limit=10 >/dev/null 2>&1

    # Update all feeds in 60 minutes (updates the 8 oldest feeds each time with a total of 120 feeds).
    * */4 * * *  cd /path/to/miniflux && php cronjob.php --call-interval=4 --update-interval=60 >/dev/null 2>&1

Note: cronjob.php can also be called from the web, in this case specify the options as GET variables.

Example: <http://yourpersonalserver/miniflux/cronjob.php?call-interval=4&update-interval=60>
