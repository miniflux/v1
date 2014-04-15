<?php

namespace Model\Database;

require_once __DIR__.'/../vendor/SimpleValidator/Validator.php';
require_once __DIR__.'/../vendor/SimpleValidator/Base.php';
require_once __DIR__.'/../vendor/SimpleValidator/Validators/Required.php';
require_once __DIR__.'/../vendor/SimpleValidator/Validators/MaxLength.php';
require_once __DIR__.'/../vendor/SimpleValidator/Validators/MinLength.php';
require_once __DIR__.'/../vendor/SimpleValidator/Validators/Equals.php';
require_once __DIR__.'/../vendor/SimpleValidator/Validators/AlphaNumeric.php';

use SimpleValidator\Validator;
use SimpleValidator\Validators;

// Create a new database for a new user
function create($filename, $username, $password)
{
    $filename = DATA_DIRECTORY.DIRECTORY_SEPARATOR.$filename;

    if (ENABLE_MULTIPLE_DB && ! file_exists($filename)) {

        $db = new \PicoDb\Database(array(
            'driver' => 'sqlite',
            'filename' => $filename,
        ));

        if ($db->schema()->check(\Model\Config\DB_VERSION)) {

            $db->table('config')->update(array(
                'username' => $username,
                'password' => \password_hash($password, PASSWORD_BCRYPT)
            ));

            return true;
        }
    }

    return false;
}

// Get or set the current database
function select($filename = '')
{
    static $current_filename = DB_FILENAME;

    if (ENABLE_MULTIPLE_DB && $filename !== '' && in_array($filename, get_all())) {
        $current_filename = $filename;
        $_SESSION['config'] = \Model\Config\get_all();
    }

    return $current_filename;
}

// Get database path
function get_path()
{
    return DATA_DIRECTORY.DIRECTORY_SEPARATOR.\Model\Database\select();
}

// Get the list of available databases
function get_all()
{
    $listing = array();

    $dir = new \DirectoryIterator(DATA_DIRECTORY);

    foreach ($dir as $fileinfo) {
        if ($fileinfo->getExtension() === 'sqlite') {
            $listing[] = $fileinfo->getFilename();
        }
    }

    return $listing;
}

// Get the formated db list
function get_list()
{
    $listing = array();

    foreach (get_all() as $filename) {

        if ($filename === DB_FILENAME) {
            $label = t('Default database');
        }
        else {
            $label = ucfirst(substr($filename, 0, -7));
        }

        $listing[$filename] = $label;
    }

    return $listing;
}

// Validate database form
function validate(array $values)
{
    $v = new Validator($values, array(
        new Validators\Required('name', t('The database name is required')),
        new Validators\AlphaNumeric('name', t('The name must have only alpha-numeric characters')),
        new Validators\Required('username', t('The user name is required')),
        new Validators\MaxLength('username', t('The maximum length is 50 characters'), 50),
        new Validators\Required('password', t('The password is required')),
        new Validators\MinLength('password', t('The minimum length is 6 characters'), 6),
        new Validators\Required('confirmation', t('The confirmation is required')),
        new Validators\Equals('password', 'confirmation', t('Passwords doesn\'t match')),
    ));

    return array(
        $v->execute(),
        $v->getErrors()
    );
}
