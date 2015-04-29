Json-RPC API
============

The Miniflux API is a way to interact programatically with your feeds, items, bookmarks and other data.

Developers can use this API to make a desktop or a mobile client on Android, iOS, etc...

Protocol
--------

The API use the [JSON-RPC](http://www.jsonrpc.org/) protocol because it's very simple.

JSON-RPC is a remote procedure call protocol encoded in JSON.
Almost the same thing as XML-RPC but with JSON.

We use the [version 2](http://www.jsonrpc.org/specification) of the protocol.
You must call the API with a **POST** HTTP request.

Credentials
-----------

The first step is to retrieve API credentials and the URL endpoint.

Under the web user interface of Miniflux, go to the menu **preferences**, scroll down until you reach the API section.

You must have these information:

- API endpoint: `https://username.miniflux.net/jsonrpc.php`
- API username: `username`
- API token: `swB3/nSo1CB1X2F` (random token)

The API username is the same as your login username and the API token is generated automatically during the database creation.

Authentication
--------------

The API use the HTTP Basic Authentication scheme described in [RFC2617](http://www.ietf.org/rfc/rfc2617.txt).

Based on the above example, the username is "demo" and the password is the "API token" (swB3/nSo1CB1X2F).

Examples
--------

### Example with cURL

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.create", "params": {"url": "http://images.apple.com/main/rss/hotnews/hotnews.rss"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Success output:

```json
{"jsonrpc":"2.0","id":1,"result":6}
```

The `feed_id` is 6.

Error output:

{"jsonrpc":"2.0","id":1,"result":false}

### Example with PHP

I developed a very simple [JSON-RPC client/server PHP library](https://github.com/fguillot/JsonRPC).
Here is an example to fetch all bookmarks.

```php
use JsonRPC\Client;

$client = new Client('https://demo.miniflux.net/jsonrpc.php');
$client->authentication('demo', 'swB3/nSo1CB1X2F');

$result = $client->execute('item.bookmark.list');
print_r($result);
```

Output:

```php
Array
(
    [0] => Array
        (
            [id] => be1403d8
            [title] => Data Structures for PHP Devs: Heaps
            [updated] => 1374503433
            [url] => http://phpmaster.com/data-structures-3/
            [status] => read
            [site_url] => http://phpmaster.com
            [feed_title] => PHP Master
        )

    [1] => Array
        (
            [id] => 49c2f23c
            [title] => Has Mozilla Lost Its Values?
            [updated] => 1374171372
            [url] => https://www.iab.net/iablog/2013/07/has-mozilla-lost-its-values.html
            [status] => read
            [site_url] => https://lobste.rs/
            [feed_title] => lobste.rs
        )
)
```

Procedures
----------

### app.version

Get the application version.

- **Arguments:** None
- **Return:** Software version

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "app.version", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{
  "jsonrpc":"2.0",
  "id":1,
  "result": {
    "version":"master"
  }
}
```

### feed.list

Get the list of subscriptions.

- **Arguments:** None
- **Return on success:** List of feeds
- **Return on failure:** false

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.list", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
      { "download_content" : "0",
        "enabled" : "1",
        "etag" : null,
        "feed_url" : "http://www.lemonde.fr/rss/une.xml",
        "id" : "1",
        "last_checked" : null,
        "last_modified" : null,
        "parsing_error" : "0",
        "site_url" : "http://www.lemonde.fr/rss/une.xml",
        "title" : "Le Monde.fr - Actualité à la Une"
      },
      { "download_content" : "1",
        "enabled" : "1",
        "etag" : null,
        "feed_url" : "http://www.futura-sciences.com/rss/actualites.xml",
        "id" : "6",
        "last_checked" : null,
        "last_modified" : null,
        "parsing_error" : "0",
        "site_url" : "http://www.futura-sciences.com",
        "title" : "Les dernières actualités de Futura-Sciences"
      },
      { "download_content" : "0",
        "enabled" : "1",
        "etag" : null,
        "feed_url" : "http://www.mac4ever.com/rss/actu",
        "id" : "2",
        "last_checked" : null,
        "last_modified" : null,
        "parsing_error" : "0",
        "site_url" : "http://www.mac4ever.com/actu",
        "title" : "Mac4Ever.com - Actualité"
      },
      ...
    ]
}
```

### feed.info

Fetch one subscription

- **Arguments:** feed_id (integer)
- **Return on success:** Key-value pair
- **Return on failure:** false

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.info", "params": {"feed_id": 1}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:
```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
    {
      "download_content" : "0",
      "enabled" : "1",
      "etag" : null,
      "feed_url" : "http://www.lemonde.fr/rss/une.xml",
      "id" : "1",
      "last_checked" : null,
      "last_modified" : null,
      "parsing_error" : "0",
      "site_url" : "http://www.lemonde.fr/rss/une.xml",
      "title" : "Le Monde.fr - Actualité à la Une"
    }
  ]
}
```

### feed.create

Add a new subscription (synchronous operation).

- **Arguments:** url (string)
- **Return on success:** feed_id (integer)
- **Return on failure:** false

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.create", "params": {"url": "http://images.apple.com/main/rss/hotnews/hotnews.rss"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{"jsonrpc":"2.0","id":1,"result":6}
```

### feed.delete

Remove one subscription.

- **Arguments:** feed_id (integer)
- **Return on success:** true
- **Return on failure:** false

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.delete", "params": {"feed_id": 5}, id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result": true
}
```

### feed.delete_all

Remove all subscriptions.

- **Arguments:** None
- **Return on success:** true
- **Return on failure:** false

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.delete_all", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### feed.update

Refresh one subscription (synchronous operation).

- **Arguments:** feed_id (integer)
- **Return on success:** true
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "feed.update", "params": {"feed_id": 1}, id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.feed.list

Get all items for a specific feed.

- **Arguments:** feed_id, offset = null, limit = null (integer)
- **Return on success:** List of items
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.feed.list", "params": {"feed_id": 1}, id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
    {
        "bookmark" : "0",
        "content" : "&lt;p&gt;La fermeture de quatre usines affecte 1 250 salariés, soit 30 % des effectifs de la marque en Espagne.&lt;/p&gt;",
        "feed_id" : "1",
        "id" : "bcc94722",
        "site_url" : "http://www.lemonde.fr/rss/une.xml",
        "status" : "unread",
        "title" : "Des milliers de manifestants à Madrid contre la fermeture d'usines Coca-Cola",
        "updated" : "1392486765",
        "url" : "http://www.lemonde.fr/europe/article/2014/02/15/des-milliers-de-manifestants-a-madrid-contre-la-fermeture-d-usines-coca-cola_4367428_3214.html#xtor=RSS-3208"
    },
    {
        "bookmark" : "0",
        "content" : "&lt;p&gt;Le Français a passé samedi la barre des 6,16 mètres, dépassant ainsi le saut de l'Ukrainien à 6,15 mètres atteint en 1993.&lt;/p&gt;",
        "feed_id" : "1",
        "id" : "c659783b",
        "site_url" : "http://www.lemonde.fr/rss/une.xml",
        "status" : "unread",
        "title" : "Saut à la perche : Lavillenie bat le record du monde de Bubka",
        "updated" : "1392486633",
        "url" : "http://www.lemonde.fr/sport/article/2014/02/15/saut-a-la-perche-lavillenie-bat-le-record-du-monde-de-bubka_4367434_3242.html#xtor=RSS-3208"
    },
    ...
  ]
}
```

### item.feed.count

Count all items for a specific feed.

- **Arguments:** feed_id (integer)
- **Return on success:** Number of items (integer)
- **Return on failure:** false

Request:

```bash
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.feed.count", "params": {"feed_id": 1}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : 25
}
```

### item.bookmark.list

Get all bookmarks.

- **Arguments:** offset = null, limit = null (integer)
- **Return on success:** List of items
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.bookmark.list", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
    {
        "bookmark" : "1",
        "content" : "La fermeture de quatre usines affecte 1 250 salariés, soit 30 % des effectifs de la marque en Espagne.",
        "feed_id" : "1",
        "feed_title" : "Le Monde.fr - Actualité à la Une",
        "id" : "bcc94722",
        "site_url" : "http://www.lemonde.fr/rss/une.xml",
        "status" : "unread",
        "title" : "Des milliers de manifestants à Madrid contre la fermeture d'usines Coca-Cola",
        "updated" : "1392486765",
        "url" : "http://www.lemonde.fr/europe/article/2014/02/15/des-milliers-de-manifestants-a-madrid-contre-la-fermeture-d-usines-coca-cola_4367428_3214.html#xtor=RSS-3208"
      },
      ...
  ]
}
```

### item.bookmark.count

Count the number of bookmarks.

- **Arguments:** Nothing
- **Return on success:** Number of items (integer)
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.bookmark.count", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : 3
}
```

### item.bookmark.create

Add a new bookmark.

- **Arguments:** item_id
- **Return on success:** true
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.bookmark.create", "params": {"item_id": "1fd17ad3"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.bookmark.delete

Remove a bookmark.

- **Arguments:** item_id
- **Return on success:** 1 (integer)
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.bookmark.delete", "params": {"item_id": "1fd17ad3"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.list_unread

Get all unread items.

- **Arguments:** offset = null, limit = null (integer)
- **Return on success:** List of items
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.list_unread", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
      {
        "bookmark" : "0",
        "content" : "La fermeture de quatre usines affecte 1 250 salariés, soit 30 % des effectifs de la marque en Espagne.",
        "feed_id" : "1",
        "feed_title" : "Le Monde.fr - Actualité à la Une",
        "id" : "bcc94722",
        "site_url" : "http://www.lemonde.fr/rss/une.xml",
        "status" : "unread",
        "title" : "Des milliers de manifestants à Madrid contre la fermeture d'usines Coca-Cola",
        "updated" : "1392486765",
        "url" : "http://www.lemonde.fr/europe/article/2014/02/15/des-milliers-de-manifestants-a-madrid-contre-la-fermeture-d-usines-coca-cola_4367428_3214.html#xtor=RSS-3208"
      },
      ...
  ]
}
```

### item.count_unread

Count all unread items.

- **Arguments:** Nothing
- **Return on success:** Number of items (integer)
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.count_unread", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : 18
}
```

### item.list_read

Get all read items.

- **Arguments:** offset = null, limit = null (integer)
- **Return on success:** List of items
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.list_read", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" :
    {
        "bookmark" : "0",
        "content" : "La fermeture de quatre usines affecte 1 250 salariés, soit 30 % des effectifs de la marque en Espagne.",
        "feed_id" : "1",
        "feed_title" : "Le Monde.fr - Actualité à la Une",
        "id" : "bcc94722",
        "site_url" : "http://www.lemonde.fr/rss/une.xml",
        "status" : "read",
        "title" : "Des milliers de manifestants à Madrid contre la fermeture d'usines Coca-Cola",
        "updated" : "1392486765",
        "url" : "http://www.lemonde.fr/europe/article/2014/02/15/des-milliers-de-manifestants-a-madrid-contre-la-fermeture-d-usines-coca-cola_4367428_3214.html#xtor=RSS-3208"
      },
      ...
}
```

### item.count_read

Count all read items.

- **Arguments:** Nothing
- **Return on success:** Number of items (integer)
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.count_read", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : 254
}
```

### item.info

Fetch one item.

- **Arguments:** item_id
- **Return on success:** Key-value pair
- **Return on failure:** false

Request:
```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.info", "params": {"item_id": "bcc94722"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : {
      "author" : "",
      "bookmark" : "1",
      "content" : "La fermeture de quatre usines affecte 1 250 salariés, soit 30 % des effectifs de la marque en Espagne.",
      "feed_id" : "1",
      "id" : "bcc94722",
      "status" : "unread",
      "title" : "Des milliers de manifestants à Madrid contre la fermeture d'usines Coca-Cola",
      "updated" : "1392486765",
      "url" : "http://www.lemonde.fr/europe/article/2014/02/15/des-milliers-de-manifestants-a-madrid-contre-la-fermeture-d-usines-coca-cola_4367428_3214.html#xtor=RSS-3208"
    }
}
```

### item.delete

Remove one item.

- **Arguments:** item_id
- **Return on success:** true
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.delete", "params": {"item_id": "bcc94722"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.mark_as_read

Mark an item as read.

- **Arguments:** item_id
- **Return on success:** true
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.mark_as_read", "params": {"item_id": "1fd17ad3"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.mark_as_unread

Mark an item as unread.

- **Arguments:** item_id
- **Return on success:** true
- **Return on failure: **false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.mark_as_read", "params": {"item_id": "1fd17ad3"}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.flush

Flush all read items.

- **Arguments:** Nothing
- **Return on success:** true
- **Return on failure:** false

Request:
```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.flush", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.mark_all_as_read

Mark all unread items as read.

- **Arguments:** Nothing
- **Return on success:** true
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.mark_all_as_read", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.set_list_status

Change the status of a list of item id.

- **Arguments:** status (read, unread or removed), items (list of item id: ["id-1", "id-2", ...])
- **Return on success:** true
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.set_list_status", "params": {"status": "unread", "items": ["1fd17ad3", "bcc94722"]}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : true
}
```

### item.get_all

Get all items (unread and read)

- **Arguments:** Nothing
- **Return on success:** List of items
- **Return on failure:** false

Request:
```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.get_all", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
      {
        "bookmark" : "0",
        "content" : "&lt;p&gt;Le comportement de molécules biologiques importantes est probablement différent dans un milieu ...&lt;/p&gt;",
        "feed_id" : "6",
        "feed_title" : "Les dernières actualités de Futura-Sciences",
        "id" : "947c27f1",
        "site_url" : "http://www.futura-sciences.com",
        "status" : "read",
        "title" : "Des bulles de graphène piègent des moélcules sous le microscope",
        "updated" : "1392467820",
        "url" : "http://www.futura-sciences.com/magazines/matiere/infos/actu/d/physique-bulles-graphene-piegent-moelcules-sous-microscope-52264/#xtor=RSS-8"
      },
      ...
    ]
}
```

### item.get_all_status

Get all items status (unread and read)

- **Arguments:** Nothing
- **Return on success:** List of items id and the status
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.get_all_status", "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : {
      "02043706" : "unread",
      "03b2f912" : "read",
      "087619e5" : "unread",
      "0a05d7f2" : "read",
      "0ab36a48" : "read",
      "0d55fd54" : "read",
      "0f64bd6d" : "read",
      "0fff2adc" : "unread",
      "10fc26ac" : "unread",
      ....
    }
}
```

### item.get_all_since

Get all items since a date (unread and read)

- **Arguments:** Unix timestamp
- **Return on success:** List of items
- **Return on failure:** false

Request:

```json
curl \
-u "demo:swB3/nSo1CB1X2F" \
-d '{"jsonrpc": "2.0", "method": "item.get_all_since", "params": {"timestamp": 1392467820}, "id": 1}' \
https://demo.miniflux.net/jsonrpc.php
```

Response:

```json
{ "id" : 1,
  "jsonrpc" : "2.0",
  "result" : [
      {
        "bookmark" : "0",
        "content" : "&lt;p&gt;...&lt;/p&gt;",
        "feed_id" : "6",
        "feed_title" : "Les dernières actualités de Futura-Sciences",
        "id" : "dccc2a20",
        "site_url" : "http://www.futura-sciences.com",
        "status" : "read",
        "title" : "Curiosity a franchi avec succès la dune de Dingo Gap",
        "updated" : "1392475200",
        "url" : "http://www.futura-sciences.com/magazines/espace/infos/actu/d/astronautique-curiosity-franchi-succes-dune-dingo-gap-52289/#xtor=RSS-8"
      },
      {
        "bookmark" : "0",
        "content" : "&lt;p&gt;...&lt;/p&gt;",
        "feed_id" : "6",
        "feed_title" : "Les dernières actualités de Futura-Sciences",
        "id" : "947c27f1",
        "site_url" : "http://www.futura-sciences.com",
        "status" : "read",
        "title" : "Des bulles de graphène piègent des moélcules sous le microscope",
        "updated" : "1392467820",
        "url" : "http://www.futura-sciences.com/magazines/matiere/infos/actu/d/physique-bulles-graphene-piegent-moelcules-sous-microscope-52264/#xtor=RSS-8"
      },
      ...
    ]
}
```
