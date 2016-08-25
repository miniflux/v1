<?php

namespace Miniflux\Model\Database;

use DirectoryIterator;
use Miniflux\Schema;
use Miniflux\Model\Config;

// Create a new database for a new user
function create($filename, $username, $password)
{
    $filename = DATA_DIRECTORY.DIRECTORY_SEPARATOR.$filename;

    if (ENABLE_MULTIPLE_DB && ! file_exists($filename)) {
        $db = new \PicoDb\Database(array(
            'driver' => 'sqlite',
            'filename' => $filename,
        ));

        if ($db->schema('\Miniflux\Schema')->check(Schema\VERSION)) {
            $credentials = array(
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT)
            );

            $db->hashtable('settings')->put($credentials);

            return true;
        }
    }

    return false;
}

// Get or set the current database
function select($filename = '')
{
    static $current_filename = DB_FILENAME;

    // function gets called with a filename at least once the database
    // connection is established
    if (! empty($filename)) {
        if (ENABLE_MULTIPLE_DB && in_array($filename, get_all())) {
            $current_filename = $filename;

            // unset the authenticated flag if the database is changed
            if (empty($_SESSION['database']) || $_SESSION['database'] !== $filename) {
                if (isset($_SESSION)) {
                    unset($_SESSION['loggedin']);
                }

                $_SESSION['database'] = $filename;
                $_SESSION['config'] = Config\get_all();
            }
        } else {
            return false;
        }
    }

    return $current_filename;
}

// Get database path
function get_path()
{
    return DATA_DIRECTORY.DIRECTORY_SEPARATOR.select();
}

// Get the list of available databases
function get_all()
{
    $listing = array();

    $dir = new DirectoryIterator(DATA_DIRECTORY);

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
        } else {
            $label = ucfirst(substr($filename, 0, -7));
        }

        $listing[$filename] = $label;
    }

    return $listing;
}
