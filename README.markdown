Miniflux - Minimalist News Reader
=================================

Miniflux is a minimalist web-based news reader. _Less is more_.

Features
--------

- Host anywhere (shared hosting, vps or localhost)
- Easy setup => copy and paste and you are done!
- CSS optimized for readability
- Keeps history of read items
- Remove Feedburner Ads and analytics trackers
- Import/Export of OPML feeds
- Feed updates via a cronjob or with the user interface with one click
- Use secure HTTP headers (only external images and Youtube/Vimeo/Dailymotion videos are allowed)
- Open external links inside a new tab with a `rel="noreferrer"` attribute
- Mobile CSS (responsive design)
- Keyboard shortcuts
- Basic bookmarks
- Translated in English, French, German, Italian, Czech, Spanish, Portuguese and Simplified Chinese
- RTL languages support
- Themes
- Alternative login with a Google Account or Mozilla Persona
- **Full article download for feeds that display only a summary** (website scraper based on Xpath rules)
- Auto-update from the user interface
- Multiple databases (each user has his own database)

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

- André Kelpe: https://github.com/fs111
- Ayodio: https://github.com/ayodio
- Bjauy: https://github.com/bjauy
- Bohwaz: https://github.com/bohwaz
- Chase Arnold: https://github.com/chase4926
- Chris Lemonier: https://github.com/chrislemonier
- Delehef: https://github.com/delehef
- Derjus: https://github.com/derjus
- Eauland: https://github.com/eauland
- Félix: https://github.com/dysosmus
- Geriel Castro: https://github.com/GerielCastro
- Hika0: https://github.com/hika0
- Horsely: https://github.com/horsley
- Ing. Jan Kaláb: https://github.com/Pitel
- Itoine: https://github.com/itoine
- James Scott-Brown: https://github.com/jamesscottbrown
- Luca Marra: https://github.com/facciocose
- Maxime: https://github.com/EpocDotFr
- MonsieurPaulLeBoulanger: https://github.com/MonsieurPaulLeBoulanger
- Necku: https://github.com/Necku
- Nicolas Dewaele: http://adminrezo.fr/
- Pcwalden: https://github.com/pcwalden
- Pitel: https://github.com/Pitel
- Silvus: https://github.com/Silvus
- Skasi7: https://github.com/skasi7
- Thiriot Christophe: https://github.com/doubleface
- Vincent Ozanam
- Ygbillet: https://github.com/ygbillet

Many people also sent bug reports and feature requests.

ChangeLog
---------

- <http://miniflux.net/changes.html>

Requirements
------------

- Recent version of libxml2 >= 2.7.x (version 2.6.32 on Debian Lenny is not supported anymore)
- PHP >= 5.3.3
- PHP XML extensions (SimpleXML, DOM...)
- PHP Sqlite extension
- cURL extension for PHP or stream context with (`allow_url_fopen=On`)
- Short tags enabled for PHP < 5.4

Documentation
-------------

- [Installation and updates](docs/installation-and-updates.markdown)
- [Cronjob](docs/cronjob.markdown)
- [Advanced configuration](docs/config.markdown)
- [Full article download](docs/full-article-download.markdown)
- [Translations](docs/translations.markdown)
- [Themes](docs/themes.markdown)
- [Session](docs/session.markdown)
- [API documentation](http://miniflux.net/api.html)
- [FAQ](docs/faq.markdown)
