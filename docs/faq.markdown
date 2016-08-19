Frequently Asked Questions
==========================

How does Miniflux update my feeds from the user interface?
----------------------------------------------------------

Miniflux uses an Ajax request to refresh each subscription.
By default, there is only 5 feeds updated in parallel.

I have 600 subscriptions, can Miniflux handle that?
---------------------------------------------------

Probably, but your life is cluttered.

Why is feature X missing?
-------------------------

Miniflux is a minimalist software. _Less is more_.

I found a bug, what next?
-------------------------

Report the bug to the [issues tracker](https://github.com/miniflux/miniflux/issues) and I will fix it.

You can report feeds that doesn't works properly too.

What browser is compatible with Miniflux?
-----------------------------------------

Miniflux is tested with the latest versions of Mozilla Firefox, Google Chrome and Safari.

Miniflux is also tested on mobile devices Android (Moto G) and Ipad Mini (Retina).

How to setup Miniflux on OVH shared-hosting?
--------------------------------------------

OVH shared web-hosting can use different PHP versions.
To have Miniflux working properly you have to use a custom `.htaccess`, for example:

```
SetEnv PHP_VER 5_4
SetEnv ZEND_OPTIMIZER 1
SetEnv MAGIC_QUOTES 0

<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization},L]
</IfModule>
```

I want to send bookmarks to Pinboard. How do I find my Pinboard API token?
--------------------------------------------------------------------------
You can find your API token by going to [https://api.pinboard.in/v1/user/api_token/](https://api.pinboard.in/v1/user/api_token/).

Miniflux requires you to add your Pinboard username before this, followed by a colon (eg. bobsmith:12FC235692DF53DD1).
