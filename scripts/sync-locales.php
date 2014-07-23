#!/usr/bin/env php
<?php

$reference_file = $argv[1];
$outdated_file = $argv[2];

$reference = include $reference_file;
$outdated = include $outdated_file;

$output = '<?php'.PHP_EOL.PHP_EOL;
$output .= 'return array('.PHP_EOL;

foreach ($reference as $key => $value) {

    if (isset($outdated[$key])) {
        //$output .= "    '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $value)."',\n";
        $output .= "    '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $outdated[$key])."',\n";
    }
    else {
        //$output .= "    // '".str_replace("'", "\'", $key)."' => '".str_replace("'", "\'", $value)."',\n";
        $output .= "    // '".str_replace("'", "\'", $key)."' => '',\n";
    }
}

$output .= ');'.PHP_EOL;

echo $output;