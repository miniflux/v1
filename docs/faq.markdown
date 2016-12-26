Frequently Asked Questions
==========================

Does Miniflux supports other databases than Sqlite?
---------------------------------------------------

Yes, Sqlite and Postgres are supported since the version 1.2.0.

How does Miniflux update my feeds from the user interface?
----------------------------------------------------------

Miniflux uses an Ajax request to refresh each subscription.
By default, there is only 5 feeds updated in parallel.

I have 600 subscriptions, can Miniflux handle that?
---------------------------------------------------

Probably, but your life is too cluttered.

Why is feature X missing?
-------------------------

Miniflux is a minimalist software. _Less is more_.

I found a bug, what next?
-------------------------

Report the bug to the [issues tracker](https://github.com/miniflux/miniflux/issues).

You can report feeds that doesn't works properly too.

What browser is compatible with Miniflux?
-----------------------------------------

Miniflux is tested with the latest versions of Mozilla Firefox, Google Chrome and Safari.


I want to send bookmarks to Pinboard. How do I find my Pinboard API token?
--------------------------------------------------------------------------

You can find your API token by going to [https://api.pinboard.in/v1/user/api_token/](https://api.pinboard.in/v1/user/api_token/).

The Pinboard token should be formatted like that: `bobsmith:12FC235692DF53DD1`.
