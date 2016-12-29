How to run integration tests?
=============================

[PHPUnit](https://phpunit.de/) and [Selenium server](http://www.seleniumhq.org) is used to run automatic tests on Miniflux.

You can run tests across different browser to be sure that the result is the same everywhere.

Requirements
------------

- PHP command line
- PHPUnit (including phpunit-selenium & dbunit) installed
- Java
- Selenium server


Install the latest version of PHPUnit
-------------------------------------

Download the PHPUnit PHAR (includes dbunit & phpunit-selenium) and copy the file somewhere in your `$PATH`:

```bash
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
phpunit --version
PHPUnit 4.4.0 by Sebastian Bergmann.
```


Install the latest version of Selenium Server
---------------------------------------------

Download the distribution archive of [Selenium Server](http://www.seleniumhq.org/download/) and the platform and browser specific driver. The following browser driver exists:

- Firefox webdriver is default included
- [Chrome webdriver](https://sites.google.com/a/chromium.org/chromedriver/downloads)
- [Internet Explorer webdriver](http://www.seleniumhq.org/download/) (windows only, obviously)
- Safari ([broken at the moment](https://code.google.com/p/selenium/issues/detail?id=4136))

Start the Selenium Server on the machine which has the browser(s) to test against installed by running:

```cmd
java -jar "C:\selenium\selenium-server-standalone-2.44.0.jar" -Dwebdriver.ie.driver="C:\selenium\IEDriverServer.exe" -Dwebdriver.chrome.driver="C:\selenium\chromedriver.exe"
```


Running integration tests
-------------------------

PHPUnit creates a new database with the default credentials for the unit tests. You have to run the tests within the Miniflux directory that is accessible via webserver.

You need to setup the Miniflux url and the browser you would like to test against in the configfile `phpunit.xml`. Its highly recommend to test only against one browser per run to speed-up testing. Browser session reusing does not work with selenium server and phpunit-selenium browser sharing feature is limited to cases where only a single browser is used.

The following `phpunit.xml` is used to run phpunit on a linux system with apache2 installed and test against a Internet Explorer on a remote windows with Selenium Server + Internet Explorer webdriver installed:

```xml
<phpunit>
    <php>
        <const name="DB_FILENAME" value="unittest.sqlite" />
        <const name="PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BASEURL" value="http://linux.example.org/miniflux/" />
        <const name="PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_BROWSERS" value='
            [
              {
                "browserName": "internet explorer",
                "host": "windows.example.org",
                "port": 4444,
                "sessionStrategy": "shared"
              }
            ]
        '/>
    </php>
    <listeners>
        <listener class="PHPUnit_Extensions_Selenium2TestCase_ScreenshotListener">
            <arguments>
                <string>./tests/</string>
            </arguments>
        </listener>
    </listeners>
    <testsuites>
        <testsuite name="Miniflux">
            <directory>tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>

```

Some tests don't run with every browser. You have to exclude those tests by using the ```--exclude-group``` command line parameter. The following exclude groups exist:

* moz_unsupported (Due to https://github.com/SeleniumHQ/selenium/issues/386)
* ie_unsupported (Due to https://code.google.com/p/selenium/issues/detail?id=4973)

You can run the tests by executing phpunit within the Miniflux directory:

```bash
/usr/local/bin/phpunit --exclude-group ie_unsupported

PHPUnit 4.4.0 by Sebastian Bergmann.

Configuration read from /var/www/miniflux/phpunit.xml

................................................................. 65 / 83 ( 78%)
..................

Time: 3.74 minutes, Memory: 21.00Mb

OK (83 tests, 485 assertions)

```

In case of unsuccessful tests, you will find screenshots from the failed website in the ./tests/ directory.


Limitations
-----------

As the webdrivers using javascript to execute the tests within the browsers, it is not possible to disable javascript and tests the non-javascript fall-back functionality.