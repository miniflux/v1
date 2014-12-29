Fever API
=========

Miniflux support the [Fever API](http://feedafever.com/api).
That means you can use mobile applications compatible with Fever.

This feature have been tested with the following apps:

- [Press for Android](http://twentyfivesquares.com/press/)
- [Reeder 2](http://reederapp.com/) (iOS and OSX)

Configuration
-------------

Miniflux generates a random password for the Fever API.
All information are available from the page **preferences > api**.

- URL: http://your_miniflux_url/fever/
- Username: Your username
- Password: random (visible on the settings page)

Multiple databases/users
------------------------

Multiple databases can be used with the Fever API if you have Apache and the `mod_rewrite` enabled.

The Fever URL becomes `http:///your_miniflux_url/myuser.sqlite/`.

Notes
-----

- Links, sparks, kindling and groups are not supported.
- All feeds will be under a category "All" because Miniflux doesn't support categories.
- Only JSON responses are handled.
