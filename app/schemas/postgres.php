<?php

namespace Miniflux\Schema;

use PDO;
use Miniflux\Helper;

const VERSION = 5;

function version_5(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN ignore_expiration BOOLEAN DEFAULT FALSE');
}

function version_4(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ALTER COLUMN feed_url TYPE TEXT');
    $pdo->exec('ALTER TABLE feeds ALTER COLUMN site_url TYPE TEXT');
}

function version_3(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN expiration BIGINT DEFAULT 0');
}

function version_2(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN parsing_error_message VARCHAR(255)');
}

function version_1(PDO $pdo)
{
    $pdo->exec("CREATE TABLE users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin BOOLEAN DEFAULT FALSE,
        last_login BIGINT,
        api_token VARCHAR(255) NOT NULL UNIQUE,
        bookmarklet_token VARCHAR(255) NOT NULL UNIQUE,
        cronjob_token VARCHAR(255) NOT NULL UNIQUE,
        feed_token VARCHAR(255) NOT NULL UNIQUE,
        fever_token VARCHAR(255) NOT NULL UNIQUE,
        fever_api_key VARCHAR(255) NOT NULL UNIQUE
    )");

    $pdo->exec('CREATE TABLE user_settings (
        "user_id" INTEGER NOT NULL,
        "key" VARCHAR(255) NOT NULL,
        "value" TEXT NOT NULL,
        PRIMARY KEY("user_id", "key"),
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE feeds (
        id BIGSERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL,
        feed_url VARCHAR(255) NOT NULL,
        site_url VARCHAR(255),
        title VARCHAR(255) NOT NULL,
        last_checked BIGINT DEFAULT 0,
        last_modified VARCHAR(255),
        etag VARCHAR(255),
        enabled BOOLEAN DEFAULT TRUE,
        download_content BOOLEAN DEFAULT FALSE,
        parsing_error INTEGER DEFAULT 0,
        rtl BOOLEAN DEFAULT FALSE,
        cloak_referrer BOOLEAN DEFAULT FALSE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, feed_url)
    )');

    $pdo->exec('CREATE TABLE items (
        id BIGSERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL,
        feed_id BIGINT NOT NULL,
        checksum VARCHAR(255) NOT NULL,
        status VARCHAR(10) NOT NULL,
        bookmark INTEGER DEFAULT 0,
        url TEXT NOT NULL,
        title TEXT NOT NULL,
        author TEXT,
        content TEXT,
        updated BIGINT,
        enclosure_url TEXT,
        enclosure_type VARCHAR(50),
        language VARCHAR(50),
        rtl BOOLEAN DEFAULT FALSE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE,
        UNIQUE(feed_id, checksum)
    )');

    $pdo->exec('CREATE TABLE "groups" (
        id SERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL,
        title VARCHAR(255) NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, title)
    )');

    $pdo->exec('CREATE TABLE "feeds_groups" (
        feed_id BIGINT NOT NULL,
        group_id INTEGER NOT NULL,
        PRIMARY KEY(feed_id, group_id),
        FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE favicons (
        id SERIAL PRIMARY KEY,
        hash VARCHAR(255) UNIQUE,
        type VARCHAR(50)
    )');

    $pdo->exec('CREATE TABLE "favicons_feeds" (
        feed_id BIGINT NOT NULL,
        favicon_id INTEGER NOT NULL,
        PRIMARY KEY(feed_id, favicon_id),
        FOREIGN KEY(favicon_id) REFERENCES favicons(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE remember_me (
        id SERIAL PRIMARY KEY,
        user_id INTEGER NOT NULL,
        ip VARCHAR(255),
        user_agent VARCHAR(255),
        token VARCHAR(255),
        sequence VARCHAR(255),
        expiration BIGINT,
        date_creation BIGINT,
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

    $pdo->exec('CREATE INDEX items_user_status_idx ON items(user_id, status)');
    $pdo->exec('CREATE INDEX items_user_feed_idx ON items(user_id, feed_id)');
}
