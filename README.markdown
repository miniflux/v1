Miniflux - Minimalist News Reader
=================================

[![Build Status](https://travis-ci.org/miniflux/miniflux.svg?branch=master)](https://travis-ci.org/miniflux/miniflux)

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/miniflux/miniflux/tree/master)

Miniflux is a minimalist and web-based RSS reader.

Features
--------

- Self-hosted
- Readability (CSS optimized for readability, responsive design, compatible with mobile and tablet devices)
- Easy setup => **copy and paste the source code and you are done!**
- Remove Feedburner Ads and analytic trackers (1x1 pixels)
- Open external links inside a new tab with a `rel="noreferrer"` attribute
- Use secure HTTP headers (only external images and Youtube/Vimeo/Dailymotion videos are allowed)
- Article content is filtered before being displayed
- Translated in Arabic, English, French, German, Italian, Japanese, Czech, Spanish, Turkish, Portuguese, Russian, Serbian and Simplified Chinese
- RTL languages support
- Keyboard shortcuts
- Full article download for feeds that display only a summary
- Bookmarks
- Groups for categorization of feeds (like folders or tags)
- Send your favorite articles to Pinboard, Instapaper, Shaarli or Wallabag
- Enclosure support (videos and podcasts)
- Feed updates via a cronjob or with the user interface in one click
- Keeps history of read items
- Import/Export of OPML feeds
- Themes
- Multiple users
- Image proxy to avoid mixed content warnings with HTTPS

Documentation
-------------

- [ChangeLog](https://github.com/miniflux/miniflux/blob/master/ChangeLog)
- [Installation and Requirements](docs/installation.markdown)
- [Upgrade to a new version](docs/upgrade.markdown)
- [Cronjob](docs/cronjob.markdown)
- [Advanced configuration](docs/config.markdown)
- [Full article download](docs/full-article-download.markdown)
- [Translations](docs/translations.markdown)
- [Themes](docs/themes.markdown)
- [Json-RPC API](docs/json-rpc-api.markdown)
- [Fever API](docs/fever.markdown)
- [Run Miniflux with Docker](docs/docker.markdown)
- [FAQ](docs/faq.markdown)

License
-------

- AGPLv3: <http://www.gnu.org/licenses/agpl-3.0.txt>

Authors
-------

- Original author: [Frédéric Guillot](https://github.com/fguillot)
- [List of contributors](CONTRIBUTORS.md)

Related projects
----------------

External projects build around Miniflux:

- [Miniflux embedded](https://github.com/repat/miniflux-embedded-android) is an Android app for Miniflux. It's basically an embedded WebView that saves your Miniflux URL and cookies. [Download on the Play Store](https://play.google.com/store/apps/details?id=de.repat.embeddedminiflux).
- [munin-miniflux](https://github.com/dewey/munin-plugins/tree/master/munin-miniflux) is a munin wildcard plugin to draw graphs of your miniflux read and unread count.
- [List of themes](docs/themes.markdown)
