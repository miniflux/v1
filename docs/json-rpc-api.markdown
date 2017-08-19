Json-RPC API
============

The Miniflux API is a way to interact programatically with your feeds, items, bookmarks and other data.

Developers can use this API to make desktop or mobile clients.

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
They are available in **preferences > api**.

You must have these information:

- API endpoint: `https://your_domain.tld/jsonrpc.php`
- API username: `username`
- API token: `XXXXXX` (random token)

The API username is the same as your login username and the API token is generated automatically.

Authentication
--------------

The API use the HTTP Basic Authentication scheme described in [RFC2617](http://www.ietf.org/rfc/rfc2617.txt).

Examples
--------

### Example with cURL

```bash
curl \
-u "username:password" \
-d '{"jsonrpc": "2.0", "method": "createFeed", "params": {"url": "http://images.apple.com/main/rss/hotnews/hotnews.rss"}, "id": 1}' \
https://localhost/jsonrpc.php
```

Success response:

```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": 6
}
```

The `feed_id` is 6.

Error response:

```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": false
}
```

Procedures
----------

### getVersion

- Purpose: **Get application version**
- Parameters: none
- Result on success: **version**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getVersion",
  "id": 304873928
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": "master",
    "id": 304873928
}
```

### createUser

- Purpose: **Create new user** (accessible only by administrators)
- Parameters:
    - **username** (string)
    - **password** (string)
    - **is_admin** (boolean, optional)
- Result on success: **user_id**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "createUser",
  "id": 97055228,
  "params": {
    "username": "api_test",
    "password": "test123"
  }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": 2,
    "id": 97055228
}
```

### removeUser

- Purpose: **Remove a user** (accessible only by administrators)
- Parameters:
    - **user_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "removeUser",
  "id": 2109613284,
  "params": [
    3
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 2109613284
}
```

### getUserByUsername

- Purpose: **Get user** (accessible only by administrators)
- Parameters:
    - **username** (string)
- Result on success: **user object**
- Result on failure: **false|null**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getUserByUsername",
  "id": 1456121566,
  "params": [
    "api_test"
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "id": "2",
        "username": "api_test",
        "password": "$2y$10$FOzlRrLoHRI3Xj4YuV8z5O1jI4CKda61reX.g.Fm4ctYMijpOhTGu",
        "is_admin": "0",
        "last_login": null,
        "api_token": "398c293808aaed9cf2be45cf8e4fa303be5cdbbf6c4a55fece6b585c6a6c",
        "bookmarklet_token": "965e9049138e4e78c398dc369fc4ec529226055c6fd77a3d4119bc3a1b5e",
        "cronjob_token": "a4f6e1f5fd655c7365ebddcaf1dfd5782669186ed85a788d8e52b8230399",
        "feed_token": "62ed60fb75616d3f81a938206449a20fe30f0bf9db9bf0b93259821f5938",
        "fever_token": "59253f797b3b1885e31449fa97e9348c127e4e10e4eea959a2555c1b3a1e",
        "fever_api_key": "5e379736d05847f87c37a7d2f57ed234"
    },
    "id": 1456121566
}
```


### getFeeds

- Purpose: **Get all subscriptions**
- Parameters: none
- Result on success: **list of feed objects**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getFeeds",
  "id": 1189414818
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "1",
            "user_id": "1",
            "feed_url": "https:\/\/miniflux.net\/feed",
            "site_url": "https:\/\/miniflux.net\/",
            "title": "Miniflux",
            "last_checked": "1483053994",
            "last_modified": "Sun, 31 Jul 2016 16:54:32 GMT",
            "etag": "W\/\"bdc7a83fd61620b778da350991501757\"",
            "enabled": "1",
            "download_content": "0",
            "parsing_error": "0",
            "rtl": "0",
            "cloak_referrer": "0",
            "parsing_error_message": null,
            "expiration": "1483226794",
            "groups": [
                {
                    "id": "1",
                    "title": "open source software"
                }
            ]
        }
    ],
    "id": 1189414818
}
```

### getFeed

- Purpose: **Get one subscription**
- Parameters:
    - **feed_id** (integer)
- Result on success: **feed object**
- Result on failure: **null**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getFeed",
  "id": 912101777,
  "params": [
    1
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "id": "1",
        "user_id": "1",
        "feed_url": "https:\/\/miniflux.net\/feed",
        "site_url": "https:\/\/miniflux.net\/",
        "title": "Miniflux",
        "last_checked": "1483053994",
        "last_modified": "Sun, 31 Jul 2016 16:54:32 GMT",
        "etag": "W\/\"bdc7a83fd61620b778da350991501757\"",
        "enabled": "1",
        "download_content": "0",
        "parsing_error": "0",
        "rtl": "0",
        "cloak_referrer": "0",
        "parsing_error_message": null,
        "expiration": "1483226794",
        "groups": [
            {
                "id": "1",
                "title": "open source software"
            }
        ]
    },
    "id": 912101777
}
```

### createFeed

- Purpose: **Add new subscription**
- Parameters:
    - **url** (string)
    - **download_content** (boolean, optional)
    - **rtl** (boolean, optional)
    - **group_name** (string, optional)
- Result on success: **feed_id**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "createFeed",
  "id": 315813488,
  "params": {
    "url": "https://miniflux.net/feed",
    "group_name": "open source software"
  }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": 1,
    "id": 315813488
}
```

### removeFeed

- Purpose: **Create new user**
- Parameters:
    - **feed_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "removeFeed",
  "id": 1793804609,
  "params": [
    1
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1793804609
}
```

### refreshFeed

- Purpose: **Refresh subscription** (synchronous call)
- Parameters:
    - **feed_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "refreshFeed",
  "id": 181234449,
  "params": [
    1
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 181234449
}
```

### getItems

- Purpose: **Get list of items**
- Parameters:
    - **since_id** (integer, optional) Returns only feeds from this item id
    - **item_ids** ([]integer, optional) Returns only items in this list
    - **limit** (integer, optional, default=50) Change number of items returned
- Result on success: **list of item objects**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getItems",
  "id": 84429548,
  "params": {
    "since_id": 2
  }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "3",
            "checksum": "7f4b791f",
            "title": "Miniflux 1.1.8 released",
            "updated": "1442016000",
            "url": "https:\/\/miniflux.net\/news\/version-1.1.8",
            "enclosure_url": "",
            "enclosure_type": "",
            "bookmark": "0",
            "feed_id": "1",
            "status": "unread",
            "content": "<ul>\n<li>Add feed groups (tags)<\/li>\n<li>Add custom rules directory support<\/li>\n<li>Add no referrer policy in meta tags and content security directives<\/li>\n<li>Update of PicoFeed with new scraper rules<\/li>\n<li>Enable Strict-Transport-Security header for HTTPS<\/li>\n<li>Change CSP directives to allow data url (Fix issue with Firefox 40)<\/li>\n<li>Toggle text direction for full content preview as well<\/li>\n<li>Add Russian translation<\/li>\n<li>Updated Czech translation<\/li>\n<li>Mark items on page 2+ read as well<\/li>\n<li>Allow to override the maximum feed size limit<\/li>\n<li>Added a config option to select how many concurrent refreshes are done on the subscription page<\/li>\n<li>Catch exceptions for image proxy<\/li>\n<li>Improve CSS for preview full content<\/li>\n<li>Minor feed edit dialog improvements<\/li>\n<li>Expose all feed errors to the frontend when adding a subscription<\/li>\n<li>Keep selected options on feed adding error<\/li>\n<li>Fix bug when the summery helper doesn&#039;t contains whitespace<\/li>\n<li>Fix Fever API bug: enable send bookmark to third-party services<\/li>\n<\/ul>\n<p><strong>Thanks to all contributors!<\/strong><\/p>\n<p><a href=\"https:\/\/miniflux.net\/miniflux-1.1.8.zip\" rel=\"noreferrer\" target=\"_blank\">Download archive<\/a><\/p>",
            "language": "",
            "rtl": "0",
            "author": "Fr\u00e9d\u00e9ric Guillot",
            "site_url": "https:\/\/miniflux.net\/",
            "feed_title": "Miniflux"
        },
        [..]
    ],
    "id":84429548
}
```
### getItemsByStatus

- Purpose: **Get list of items by status**
- Parameters:
    - **status** (string)
    - **feed_ids** ([]integer, optional) Returns only items from this list of feeds
    - **offset** (integer, optional) Number of offset items (used for pagination)
    - **limit** (integer, optional, default=50) Change number of items returned
    - **order_column** (string, optional, default='updated') Order table column
    - **order_direction** (string, optional, default='desc') Order direction
- Result on success: **list of item objects**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getItemsByStatus",
  "id": 84429548,
  "params": {
    "status": "unread"
  }
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "3",
            "checksum": "7f4b791f",
            "title": "Miniflux 1.1.8 released",
            "updated": "1442016000",
            "url": "https:\/\/miniflux.net\/news\/version-1.1.8",
            "enclosure_url": "",
            "enclosure_type": "",
            "bookmark": "0",
            "feed_id": "1",
            "status": "unread",
            "content": "<ul>\n<li>Add feed groups (tags)<\/li>\n<li>Add custom rules directory support<\/li>\n<li>Add no referrer policy in meta tags and content security directives<\/li>\n<li>Update of PicoFeed with new scraper rules<\/li>\n<li>Enable Strict-Transport-Security header for HTTPS<\/li>\n<li>Change CSP directives to allow data url (Fix issue with Firefox 40)<\/li>\n<li>Toggle text direction for full content preview as well<\/li>\n<li>Add Russian translation<\/li>\n<li>Updated Czech translation<\/li>\n<li>Mark items on page 2+ read as well<\/li>\n<li>Allow to override the maximum feed size limit<\/li>\n<li>Added a config option to select how many concurrent refreshes are done on the subscription page<\/li>\n<li>Catch exceptions for image proxy<\/li>\n<li>Improve CSS for preview full content<\/li>\n<li>Minor feed edit dialog improvements<\/li>\n<li>Expose all feed errors to the frontend when adding a subscription<\/li>\n<li>Keep selected options on feed adding error<\/li>\n<li>Fix bug when the summery helper doesn&#039;t contains whitespace<\/li>\n<li>Fix Fever API bug: enable send bookmark to third-party services<\/li>\n<\/ul>\n<p><strong>Thanks to all contributors!<\/strong><\/p>\n<p><a href=\"https:\/\/miniflux.net\/miniflux-1.1.8.zip\" rel=\"noreferrer\" target=\"_blank\">Download archive<\/a><\/p>",
            "language": "",
            "rtl": "0",
            "author": "Fr\u00e9d\u00e9ric Guillot",
            "site_url": "https:\/\/miniflux.net\/",
            "feed_title": "Miniflux"
        },
        [..]
    ],
    "id":84429548
}
```

### getItem

- Purpose: **Fetch one item**
- Parameters:
    - **item_id** (integer)
- Result on success: **item object**
- Result on failure: **null**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getItem",
  "id": 1323079112,
  "params": [
    1
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "id": "1",
        "user_id": "1",
        "feed_id": "1",
        "checksum": "a86e22e4",
        "status": "unread",
        "bookmark": "0",
        "url": "https:\/\/miniflux.net\/news\/version-1.1.10",
        "title": "Miniflux 1.1.10 released",
        "author": "Fr\u00e9d\u00e9ric Guillot",
        "content": "<p>Here are the main changes of this version:<\/p>\n<ul>\n<li>Code cleanup<\/li>\n<li>Do not use anymore Closure compiler for Javascript<\/li>\n<li>Added the possibility to swipe to archive an item on mobile devices<\/li>\n<li>Make the whole menu row clickable on small screens<\/li>\n<li>Add API methods for groups<\/li>\n<li>Added Beanstalkd producer\/worker<\/li>\n<li>Use HTTP_HOST instead of SERVER_NAME to guess hostname<\/li>\n<li>Run php-cs-fixer on the code base<\/li>\n<li>Add sorting direction link to the history section<\/li>\n<li>Make read\/bookmark icons more usable in mobile view<\/li>\n<li>Record last login timestamp in the database<\/li>\n<li>Add japanese language<\/li>\n<li>Replace help window by layer<\/li>\n<li>Create automatically the favicon directory if missing<\/li>\n<li>Add group filter to history and bookmarks section<\/li>\n<li>Dependencies update<\/li>\n<\/ul>\n<p><strong>Thanks to all contributors!<\/strong><\/p>\n<p><a href=\"https:\/\/miniflux.net\/miniflux-1.1.10.zip\" rel=\"noreferrer\" target=\"_blank\">Download archive<\/a><\/p>",
        "updated": "1469923200",
        "enclosure_url": "",
        "enclosure_type": "",
        "language": "",
        "rtl": "0"
    },
    "id": 1323079112
}
```

### changeItemsStatus

- Purpose: **Mark items as read/unread**
- Parameters:
    - **item_ids** ([]integer)
    - **status** (string, possible values: read or unread)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "changeItemsStatus",
  "id": 155789655,
  "params": [
    [1],
    "read"
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 155789655
}
```

### addBookmark

- Purpose: **Mark item as bookmark**
- Parameters:
    - **item_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "addBookmark",
  "id": 791748350,
  "params": [
    1
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 791748350
}
```

### removeBookmark

- Purpose: **Mark item as not bookmarked**
- Parameters:
    - **item_id** (integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "removeBookmark",
  "id": 16893793,
  "params": [
    1
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 16893793
}
```

### getGroups

- Purpose: **Get list of groups**
- Parameters: none
- Result on success: **list of group objects**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getGroups",
  "id": 1922098828
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "1",
            "user_id": "1",
            "title": "open source software"
        }
    ],
    "id": 1922098828
}
```

### createGroup

- Purpose: **Add new group**
- Parameters:
    - **title** (string)
- Result on success: **group_id**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "createGroup",
  "id": 924207274,
  "params": [
    "foobar"
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": 2,
    "id": 924207274
}
```

### setFeedGroups

- Purpose: **Assign/Unassign groups to a subscription**
- Parameters:
    - **feed_id** (integer)
    - **group_ids** ([]integer)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "setFeedGroups",
  "id": 594627291,
  "params": [
    1,
    [2, 3]
  ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 594627291
}
```

### getFavicons

- Purpose: **Get list of favicons**
- Parameters: none
- Result on success: **list of favicon objects**
- Result on failure: **false**

Request example:

```json
{
  "jsonrpc": "2.0",
  "method": "getFavicons",
  "id": 1029539064
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "feed_id": "1",
            "hash": "bec82599c771aea672bea5a9a2988f150a849390",
            "type": "image\/png",
            "data_url": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAGJwAABicBTVTYxwAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAALMSURBVHic7Zo7a1RRFIW\/I8YXaBBEJRJEU8RqQBBBQRBEWxHBwlZUsLRWUFBsA4L4G4IY0TaF2PhEEQwmhuADJIkRUUOMr2RZ3Em8mcxkzrkPtjhnwS7msveadT\/Ofc44SbSyllkHsFYEYB3AWhGAdQBrRQDWAawVAVgHsFYEYB3AWhGAdQBrLS\/L2Dm3CdgFbK3WDPC6Wi8kjWX03QBUgG3AdmAN8LFaT4CnCnjEdbW9zrk+YL3n\/AVJd2vmDwKngMNAW4O538BNoEfSfa+gzu0DzgBHl\/AFGAN6gcuSPjQ1lrSggHFAnnUsNdcO3AiYnas7wNraHCnfLcC9DL6TwNlGvvP+RQAAdgIjGULO1XOgs06WQ8BEDl8BPVRXeikAgK4CQgp4B7SnchwnOW\/k9RVwviwAp4HBgkIKuJ5aUd8K9P0JVMoA8LnAkAJmgSPA24J9BfTXA1DvKjAObOT\/k4BuScPpjWXcCM0Co8CnErynSFbHTIZZB5xYtDXnIZCuCeAkqUsa0AlcyeiXrtvAnpTvamA\/8CbQ50HR54C5egV0LHEtv5hj588t4dsBvA\/wmgbaigbwneTYanyzkayELDvf2\/RGBi4FelaKBnC1Wciq70Cg7y+gy8O3O9D3QHq+iJPgNc++R4G+\/ZJGPPqGSU68vlqX\/pAXwKCkl569XwK9b\/k0SZoleRL0VaEAngX0TgZ6Pw7obf7U91cr0x\/yAhgK6A0BIMB3ZUFyq5tJeQGELL2vAb1TkqYD+lcF9C5QXgAhO\/WjJF\/I8WYrL4CQnfoXfBep5V+KRgDWAawVAVgHsFYEYB3AWhGAdQBrRQDWAawVAVgHsFYEYB3AWi0PoN6Po3uBFZ7zA5ImvL7Iuc3ADk\/faUkPPXtxzu0m+a+Qj4Ykjc7P1gJoNbX8IRABWAewVgRgHcBaEYB1AGtFANYBrBUBWAewVssD+AMBy6wzsaDiAwAAAABJRU5ErkJggg=="
        }
    ],
    "id": 1029539064
}
```
