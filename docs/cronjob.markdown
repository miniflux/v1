Background Job (cronjob)
========================

The cronjob is a background task to update your feeds automatically.

Command line usage
------------------

Technically, you just need to be inside the directory `miniflux` and run the script `cronjob.php`.


Parameters          | Type                           | Value
--------------------|--------------------------------|-----------------------------
--limit             | optional                       | number of feeds
--call-interval     | optional, excluded by --limit, require --update-interval | time in minutes < update interval time
--update-interval   | optional, excluded by --limit, require --call-interval   | time in minutes >= call interval time


Examples:

```bash
crontab -e

# Update all feeds every 4 hours
0 */4 * * *  cd /path/to/miniflux && php cronjob.php >/dev/null 2>&1

# Update the 10 oldest feeds each time
0 */4 * * *  cd /path/to/miniflux && php cronjob.php --limit=10 >/dev/null 2>&1

# Update all feeds in 60 minutes (updates the 8 oldest feeds each time with a total of 120 feeds).
* */4 * * *  cd /path/to/miniflux && php cronjob.php --call-interval=4 --update-interval=60 >/dev/null 2>&1
```

Web usage
---------

The cronjob script can also be called from the web, in this case specify the options as GET variables.

Example: <http://yourpersonalserver/miniflux/cronjob.php?call-interval=4&update-interval=60&token=XXX>

- The cronjob URL is visible on the page **preferences > about**.
- The access is protected by a private token.
- You can disable the web cronjob by changing the config parameter `ENABLE_CRONJOB_HTTP_ACCESS` to `false`.
