Miniflux - Minimalist News Reader
=================================

Miniflux is a minimalist and web-based RSS reader.

Features
--------

### Host almost anywhere

- Your Raspberry Pi, a shared web-hosting, a virtual machine or localhost
- Easy setup => copy and paste the source code and you are done!
- Use a lightweight Sqlite database instead of Mysql or Postgresql

### Readability

- CSS optimized for readability
- Responsive design

### Privacy and security

- Remove Feedburner Ads and analytics trackers (1x1 pixels)
- Open external links inside a new tab with a `rel="noreferrer"` attribute
- Use secure HTTP headers (only external images and Youtube/Vimeo/Dailymotion videos are allowed)
- Article content is filtered before being displayed

### Polyglot

- Translated in English, French, German, Italian, Czech, Spanish, Portuguese and Simplified Chinese
- RTL languages support

### Awesome features

- Keyboard shortcuts
- Full article download for feeds that display only a summary
- Enclosure support (videos and podcasts)
- Feed updates via a cronjob or with the user interface with one click

### More

- Keeps history of read items
- Basic bookmarks
- Import/Export of OPML feeds
- Themes
- Auto-update from the user interface
- Multiple databases (each user has his own database)

Requirements
------------

- Recent version of libxml2 >= 2.7.x (version 2.6.32 on Debian Lenny is not supported anymore)
- PHP >= 5.3.3
- PHP XML extensions (SimpleXML and DOM)
- PHP Sqlite extension
- cURL extension for PHP or Stream Context with `allow_url_fopen=On`

Documentation
-------------

- [Installation](docs/installation.markdown)
- [Upgrade to a new version](docs/upgrade.markdown)
- [Cronjob](docs/cronjob.markdown)
- [Advanced configuration](docs/config.markdown)
- [Full article download](docs/full-article-download.markdown)
- [Multiple users](docs/multiple-users.markdown)
- [Translations](docs/translations.markdown)
- [Themes](docs/themes.markdown)
- [Json-RPC API](docs/json-rpc-api.markdown)
- [Fever API](docs/fever.markdown)
- [Run Miniflux with Docker](docs/docker.markdown)
- [FAQ](docs/faq.markdown)

Todo and known bugs
-------------------

- See Issues: <https://github.com/fguillot/miniflux/issues>

License
-------

- AGPL: <http://www.gnu.org/licenses/agpl-3.0.txt>

Authors
-------

Original author: [Frédéric Guillot](http://fredericguillot.com/)

### Contributors

People who sent a pull-request, report a bug, make a new theme or share a super cool idea:

- [André Kelpe](https://github.com/fs111)
- [Augustin Lacour](https://github.com/gugu4-9)
- [Ayodio](https://github.com/ayodio)
- [Bjauy](https://github.com/bjauy)
- [Bohwaz](https://github.com/bohwaz)
- [Chase Arnold](https://github.com/chase4926)
- [Chris Lemonier](https://github.com/chrislemonier)
- [Delehef](https://github.com/delehef)
- [Derjus](https://github.com/derjus)
- [Eauland](https://github.com/eauland)
- [Félix](https://github.com/dysosmus)
- [Geriel Castro](https://github.com/GerielCastro)
- [Hika0](https://github.com/hika0)
- [Horsely](https://github.com/horsley)
- [Ing. Jan Kaláb](https://github.com/Pitel)
- [Itoine](https://github.com/itoine)
- [James Scott-Brown](https://github.com/jamesscottbrown)
- [James Barwell](https://github.com/JamesBarwell)
- [Julian Oster](https://github.com/jlnostr)
- [Jarek](https://github.com/jarek)
- [Luca Marra](https://github.com/facciocose)
- [Martin Simon](https://github.com/c0ding)
- [Mathias Kresin](https://github.com/mkresin)
- [Maxime](https://github.com/EpocDotFr)
- [Meradoou](https://github.com/meradoou)
- [MonsieurPaulLeBoulanger](https://github.com/MonsieurPaulLeBoulanger)
- [Necku](https://github.com/Necku)
- [Nicolas Dewaele](http://adminrezo.fr/)
- [Pcwalden](https://github.com/pcwalden)
- [Pitel](https://github.com/Pitel)
- [Silvus](https://github.com/Silvus)
- [Skasi7](https://github.com/skasi7)
- [Thiriot Christophe](https://github.com/doubleface)
- [Tobi](https://github.com/tobir)
- Vincent Ozanam
- [Ygbillet](https://github.com/ygbillet)

Many people also sent bug reports and feature requests.
