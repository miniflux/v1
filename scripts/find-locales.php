<?php

const REF_LOCALE = 'fr_FR';

function show(array $strings)
{
    foreach ($strings as $string) {
        echo "    '".str_replace("'", "\'", $string)."' => '',".PHP_EOL;
    }
}

function compare(array $strings)
{
    $reference_file = __DIR__.'/../app/locales/'.REF_LOCALE.'/translations.php';
    $reference = include($reference_file);

    echo str_repeat('#', 70).PHP_EOL;
    echo 'MISSING STRINGS'.PHP_EOL;
    echo str_repeat('#', 70).PHP_EOL;
    show(array_diff($strings, array_keys($reference)));

    echo str_repeat('#', 70).PHP_EOL;
    echo 'USELESS STRINGS'.PHP_EOL;
    echo str_repeat('#', 70).PHP_EOL;
    show(array_diff(array_keys($reference), $strings));
}

function search($filename)
{
    $content = file_get_contents($filename);
    $strings = array();

    if (preg_match_all('/\b[tne]\((\'\K.*?\') *[\)\,]/', $content, $matches) && isset($matches[1])) {
        $strings = $matches[1];
    }

    if (preg_match_all('/\btne\((\'\K.*?\') *[\)\,]/', $content, $matches) && isset($matches[1])) {
        $strings = array_merge($strings, $matches[1]);
    }

    if (preg_match_all('/\bdt\((\'\K.*?\') *[\)\,]/', $content, $matches) && isset($matches[1])) {
        $strings = array_merge($strings, $matches[1]);
    }

    array_walk($strings, function (&$value) {
        $value = trim($value, "'");
        $value = str_replace("\'", "'", $value);
    });

    return $strings;
}

function execute()
{
    $strings = array();
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app'));
    $it->rewind();

    while ($it->valid()) {
        if (! $it->isDot() && substr($it->key(), -4) === '.php') {
            $strings = array_merge($strings, search($it->key()));
        }

        $it->next();
    }

    compare(array_unique($strings));
}

execute();
