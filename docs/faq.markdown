FAQ
===

### How does Miniflux update my feeds from the user interface?

Miniflux uses an Ajax request to refresh each subscription.
By default, there is only 5 feeds updated in parallel.

### I have 600 subscriptions, can Miniflux handle that?

Probably, but your life is cluttered.

### Why are there no categories? Why is feature X missing?

Miniflux is a minimalist software. _Less is more_.

Sorry, I don't plan to have categories or tags.

### I found a bug, what next?

Report the bug to the [issues tracker](https://github.com/fguillot/miniflux/issues) and I will fix it.

You can report feeds that doesn't works properly too.

### What browser is compatible with Miniflux?

Miniflux is tested with the latest versions of Mozilla Firefox, Google Chrome and Safari.

I don't use Microsoft products, and as such I have no idea if Miniflux works correctly with Internet Explorer.

### How to setup Miniflux on OVH?

OVH shared web-hosting can use different PHP versions.
To have Miniflux working properly you have to use a custom `.htaccess` like that:

```
SetEnv PHP_VER 5_4
SetEnv ZEND_OPTIMIZER 1
SetEnv MAGIC_QUOTES 0
```

You can also just rename the file `.htaccess_ovh` to `.htaccess`.
