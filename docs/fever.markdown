Fever API
=========

Miniflux support the [Fever API](http://feedafever.com/api).
That means you can use mobile applications compatible with Fever.

This feature have been tested with the following apps:

- [Press for Android](http://twentyfivesquares.com/press/)
- [Reeder 2](http://reederapp.com/)

Configuration
-------------

Miniflux generates a random password for the Fever API.
All information are available from the page **preferences > api**.

- URL: http://your_miniflux_url/fever/
- Username: Your username
- Password: random (visible on the settings page)

Notes
-----

- Links, sparks, kindling, favicons and groups are not supported.
- All feeds will be under a category "All" because Miniflux doesn't support categories.
- Only JSON responses are handled.
- If you have multiple users with Miniflux, that will works only with the default user.
