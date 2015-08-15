<?php

namespace Schema;

function version_1($pdo)
{
    $pdo->exec('CREATE TABLE test1 (column1 TEXT)');
}

function version_2($pdo)
{
    $pdo->exec('CREATE TABLE test2 (column2 TEXT)');
}

function version_3($pdo)
{
    // Simulate an error
    $pdo->exec('CREATE TABL');
}
