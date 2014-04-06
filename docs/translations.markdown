Translations
============

How to create or update a translation?
--------------------------------------

- Translations are stored inside the directory `locales`
- There is sub-directory for each language, by example for french we have `fr_FR`, for italian `it_IT` etc...
- A translation is a PHP file that return an Array with a key-value pairs
- The key is the original text in english and the value is the translation for the corresponding language

French translations are always the most recent (because I am french).

Create a new translation:

1. Make a new directory: `locales/xx_XX` by example `locales/fr_CA` for French Canadian
2. Create a new file for the translation: `locales/xx_XX/translations.php`
3. Use the content of the french locales to have the most recent keys and replace the values
4. Inside the file `models/config.php`, add a new entry for your translation inside the function `get_languages()`
5. Check with your local installation of Miniflux if everything is ok
6. Send a pull-request with Github
