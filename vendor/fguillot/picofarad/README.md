PicoFarad
=========

PicoFarad is a minimalist micro-framework for PHP.
Perfect to build a REST API or a small webapp.

Features
--------

- No dependency
- Easy to use, fast and very lightweight
- Only 4 files: Request, Response, Router and Session
- License: Do what the fuck you want with that

Requirements
------------

- PHP >= 5.3

Router
------

### Example for a single file webapp:

```php
<?php

use PicoFarad\Router;
use PicoFarad\Response;
use PicoFarad\Request;
use PicoFarad\Session;

// Called before each action
Router\before(function($action) {

    // Open a session only for the specified directory
    Session\open(dirname($_SERVER['PHP_SELF']));

    // HTTP secure headers
    Response\csp();
    Response\xframe();
    Response\xss();
    Response\nosniff();
});

// GET ?action=show-help
Router\get_action('show-help', function() {
    Response\text('help me!');
});

// POST ?action=hello (with a form value "name")
Router\post_action('show-help', function() {
    Response\text('Hello '.Request\value('name'));
});

// Default action executed
Router\notfound(function() {
    Response\text('Sorry, page not found!');
})
```

### Split your webapp in different files:

```php
<?php

use PicoFarad\Router;
use PicoFarad\Response;

// Include automatically those files:
// __DIR__.'/controllers/controller1.php'
// __DIR__.'/controllers/controller2.php'
Router\bootstrap(__DIR__.'/controllers', 'controller1', 'controller2');

// Page not found
Router\notfound(function() {
    Response\redirect('?action=unread');
});
```

### Example for a REST API:

```php
<?php

// POST /foo
Router\post('/foo', function() {
    $values = Request\values();
    ...
    Response\json(['result' => true], 201);
});

// GET /foo/123
Router\get('/foo/:id', function($id) {
    Response\json(['result' => true]);
});

// PUT /foo/123
Router\put('/foo/:id', function($id) {
    $values = Request\values();
    ...
    Response\json(['result' => true]);
});

// DELETE /foo/123
Router\delete('/foo/:id', function($id) {
    Response\json(['result' => true]);
});
```

Response
--------

### Send a JSON response

```php
<?php

use PicoFarad\Response;

$data = array(....);

// Output the encoded JSON data with a HTTP status 200 Ok
Response\json($data);

// Change the default HTTP status code by a 400 Bad Request
Response\json($data, 400);
```

### Send text response

```php
Response\text('my text data');
```

### Send HTML response

```php
Response\html('<html...>');
```

### Send XML response

```php
Response\xml('<xml ... >');
```

### Send a binary response

```php
Response\binary($my_file_content);
```

### Force browser download

```php
Response\force_download('The name of the ouput file');
```

### Modify the HTTP status code

```php
Response\status(403);
```

### Temporary redirection

```php
Response\redirect('http://....');
```

### Permanent redirection

```php
Response\redirect('http://....', 301);
```

### Secure headers

```php
// Send the header X-Content-Type-Options: nosniff
Response\nosniff();

// Send the header X-XSS-Protection: 1; mode=block
Response\xss()

// Send the header Strict-Transport-Security: max-age=31536000
Response\hsts();

// Send the header X-Frame-Options: DENY
Response\xframe();
```

### Content Security Policies

```php
Response\csp(array(
    'img-src' => '*'
));

// Send these headers:
Content-Security-Policy: img-src *; default-src 'self';
X-Content-Security-Policy: img-src *; default-src 'self';
X-WebKit-CSP: img-src *; default-src 'self';
```

Request
-------

### Get querystring variables

```php
use PicoFarad\Request;

// Get from the URL: ?toto=value
echo Request\param('toto');

// Get only integer value: ?toto=2
echo Request\int_param('toto');
```

### Get the raw body

```php
echo Request\body();
```

### Get decoded JSON body or form values

If a form is submited, you got an array of values.
If the body is a JSON encoded string you got an array of the decoded JSON.

```php
print_r(Request\values());
```

### Get a form variable

```php
echo Request\value('myvariable');
```

### Get the content of a uploaded file

```php
echo Request\file_content('field_form_name');
```

### Check if the request is a POST

```php
if (Request\is_post()) {
    ...
}
```

### Check if the request is a GET

```php
if (Request\is_get()) {
    ...
}
```

### Get the request uri

```php
echo Request\uri();
```

Session
-------

### Open and close a session

The session cookie have the following settings:

- Cookie lifetime: 2678400 seconds (31 days)
- Limited to a specified path (http://domain/mywebapp/) or not (http://domain/)
- If the connection is HTTPS, the cookie use the secure flag
- The cookie is HttpOnly, not available from Javascript

Example:

```php
use PicoFarad\Session;

// Session start
Session\open('mywebappdirectory');

// Destroy the session
Session\close();
```

### Flash messages

Set the session variables: `$_SESSION['flash_message']` and `$_SESSION['flash_error_message']`.
In your template, use a helper to display and delete these messages.

```php
// Standard message
Session\flash('My message');

// Error message
Session\flash_error('My error message');
```
