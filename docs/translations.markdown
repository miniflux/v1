Translations
============

How to translate Miniflux to a new language?
--------------------------------------------

- Translations are stored inside the directory `locales`
- There is sub-directory for each language, by example for french we have `fr_FR`, for italian `it_IT` etc...
- A translation is a PHP file that return an Array with a key-value pairs
- The key is the original text in english and the value is the translation for the corresponding language

French translations are always up to date (because I am french).

### Create a new translation:

1. Make a new directory: `locales/xx_XX` by example `locales/fr_CA` for French Canadian
2. Create a new file for the translation: `locales/xx_XX/translations.php`
3. Use the content of the french locales and replace the values
4. Inside the file `models/config.php`, add a new entry for your translation inside the function `get_languages()`
5. Check with your local installation of Miniflux if everything is ok
6. Send a pull-request with Github

How to update an existing translation?
--------------------------------------

1. Open the translation file `locales/xx_XX/translations.php`
2. Missing translations are commented and the values are empty, just fill blank and remove comments
3. Check with your local installation of Miniflux and send a pull-request
