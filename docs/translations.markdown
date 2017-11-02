Translations
============

How to translate Miniflux to a new language?
--------------------------------------------

- Translations are stored inside the directory `locales`
- There is a sub-directory for each language, for example in French we have `fr_FR`, Italian `it_IT` etc...
- A translation is a PHP file that return an Array with a key-value pairs
- The key is the original text in English and the value is the translation of the corresponding language
- **French translations are always up to date**
- Always use the last version (branch master)

### Plurals

Each translation file has a plural formula defined. This formula is used to determine whether a plural for the current number is needed. For languages with multiple plurals, the formula determine which plural form is the right one.

You can get the formula for your language from https://localization-guide.readthedocs.io/en/latest/l10n/pluralforms.html.

The formula need to be adjusted to be valid php code. Do not hesitate to ask for an adjusted formula in the bug tracker.

You need to create a list of strings for terms that have a plural. For example, the plural of the English translation looks like the following:

```
    'plural' => function($n) { return ($n != 1); },
    'After %d day' => array('After %d day', 'After %d days'),
```

### Create a new translation:

1. Make a new directory: `locales/xx_XX` by example `locales/fr_CA` for French Canadian
2. Create a new file for the translation: `locales/xx_XX/translations.php`
3. Use the content of the French locale and replace the values
4. Inside the file `models/config.php`, add a new entry for your translation inside the function `get_languages()`
5. Check with your local installation of Miniflux if everything is ok
6. Send a [pull-request with Github](https://help.github.com/articles/using-pull-requests/)

How to update an existing translation?
--------------------------------------

1. Open the translation file `locales/xx_XX/translations.php`
2. Missing translations are commented with `//` and the values are empty, just fill blank and remove the comment
3. Check with your local installation of Miniflux and send a [pull-request](https://help.github.com/articles/using-pull-requests/)

How to add new translated text in the application?
--------------------------------------------------

Translations are displayed with the following functions in the source code:

- `t()`: escaped HTML text
- `tne()`: displayed with no escaping
- `dt()`: date using `strftime()` formats

Always use the English version in the source code.

How to find missing translations in the applications?
-----------------------------------------------------

From a Unix shell run:

```bash
./scripts/find-strings.sh
```

All missing translations are displayed on the screen. Put that in the French locale and sync other locales (see below).

How to synchronize translation files?
-------------------------------------

From a Unix shell run this command:

```bash
./scripts/sync-locales.php
```

The French translation is used a reference to other locales.
