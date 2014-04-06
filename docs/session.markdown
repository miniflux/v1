Session
=======

How to change the session save path?
------------------------------------

With several shared hosting providers, sessions are cleaned very frequently, to avoid to login too often,
you can save sessions in a custom directory.

- Create a directory, by example `sessions`
- This directory must be writeable by the web server user
- This directory must NOT be accessible from the outside world (add a `.htaccess` if necessary)
- Override the application variable like described [here](config.markdown): `define('SESSION_SAVE_PATH', 'sessions');`
- Now, your sessions are saved in the directory `sessions`
