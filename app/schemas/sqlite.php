<?php

namespace Miniflux\Schema;

use PDO;
use Miniflux\Helper;

const VERSION = 4;

function version_4(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN ignore_expiration INTEGER DEFAULT 0');
}

function version_3(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN expiration INTEGER DEFAULT 0');
}

function version_2(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN parsing_error_message TEXT');
}

function version_1(PDO $pdo)
{
    $pdo->exec('CREATE TABLE users (
        id INTEGER PRIMARY KEY,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        is_admin INTEGER DEFAULT 0,
        last_login INTEGER,
        api_token TEXT NOT NULL UNIQUE,
        bookmarklet_token TEXT NOT NULL UNIQUE,
        cronjob_token TEXT NOT NULL UNIQUE,
        feed_token TEXT NOT NULL UNIQUE,
        fever_token TEXT NOT NULL UNIQUE,
        fever_api_key TEXT NOT NULL UNIQUE
    )');

    $pdo->exec('CREATE TABLE user_settings (
        "user_id" INTEGER NOT NULL,
        "key" TEXT NOT NULL,
        "value" TEXT NOT NULL,
        PRIMARY KEY("user_id", "key"),
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE feeds (
        id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        feed_url TEXT NOT NULL,
        site_url TEXT,
        title TEXT COLLATE NOCASE NOT NULL,
        last_checked INTEGER DEFAULT 0,
        last_modified TEXT,
        etag TEXT,
        enabled INTEGER DEFAULT 1,
        download_content INTEGER DEFAULT 0,
        parsing_error INTEGER DEFAULT 0,
        rtl INTEGER DEFAULT 0,
        cloak_referrer INTEGER DEFAULT 0,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, feed_url)
    )');

    $pdo->exec('CREATE TABLE items (
        id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        feed_id INTEGER NOT NULL,
        checksum TEXT NOT NULL,
        status TEXT NOT NULL,
        bookmark INTEGER DEFAULT 0,
        url TEXT NOT NULL,
        title TEXT COLLATE NOCASE NOT NULL,
        author TEXT,
        content TEXT,
        updated INTEGER,
        enclosure_url TEXT,
        enclosure_type TEXT,
        language TEXT,
        rtl INTEGER DEFAULT 0,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE,
        UNIQUE(feed_id, checksum)
    )');

    $pdo->exec('CREATE TABLE "groups" (
        id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        title TEXT COLLATE NOCASE NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, title)
    )');

    $pdo->exec('CREATE TABLE "feeds_groups" (
        feed_id INTEGER NOT NULL,
        group_id INTEGER NOT NULL,
        PRIMARY KEY(feed_id, group_id),
        FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE favicons (
        id INTEGER PRIMARY KEY,
        hash TEXT UNIQUE,
        type TEXT
    )');

    $pdo->exec('CREATE TABLE "favicons_feeds" (
        feed_id INTEGER NOT NULL,
        favicon_id INTEGER NOT NULL,
        PRIMARY KEY(feed_id, favicon_id),
        FOREIGN KEY(favicon_id) REFERENCES favicons(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE remember_me (
        id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        ip TEXT,
        user_agent TEXT,
        token TEXT,
        sequence TEXT,
        expiration INTEGER,
        date_creation INTEGER,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $fever_token = Helper\generate_token();
    $rq = $pdo->prepare('
      INSERT INTO users
      (username, password, is_admin, api_token, bookmarklet_token, cronjob_token, feed_token, fever_token, fever_api_key)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $rq->execute(array(
        'admin',
        password_hash('admin', PASSWORD_BCRYPT),
        '1',
        Helper\generate_token(),
        Helper\generate_token(),
        Helper\generate_token(),
        Helper\generate_token(),
        $fever_token,
        md5('admin:'.$fever_token),
    ));
}
