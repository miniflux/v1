<?php

namespace Miniflux\Schema;

use PDO;
use Miniflux\Helper;

const VERSION = 3;

function version_3(PDO $pdo)
{
    $pdo->exec('ALTER TABLE feeds ADD COLUMN ignore_expiration TINYINT(1) DEFAULT 0');
}

function version_2(PDO $pdo)
{
    $pdo->exec('ALTER TABLE items CHANGE title title TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
    $pdo->exec('ALTER TABLE items CHANGE author author TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('ALTER TABLE items CHANGE content content LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('ALTER TABLE feeds CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
}

function version_1(PDO $pdo)
{
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT '0',
        last_login BIGINT,
        api_token VARCHAR(255) NOT NULL UNIQUE,
        bookmarklet_token VARCHAR(255) NOT NULL UNIQUE,
        cronjob_token VARCHAR(255) NOT NULL UNIQUE,
        feed_token VARCHAR(255) NOT NULL UNIQUE,
        fever_token VARCHAR(255) NOT NULL UNIQUE,
        fever_api_key VARCHAR(255) NOT NULL UNIQUE
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE user_settings (
        `user_id` INT NOT NULL,
        `key` VARCHAR(255) NOT NULL,
        `value` TEXT NOT NULL,
        PRIMARY KEY(`user_id`, `key`),
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE feeds (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        feed_url VARCHAR(255) NOT NULL,
        site_url VARCHAR(255),
        title VARCHAR(255) NOT NULL,
        expiration BIGINT DEFAULT 0,
        last_checked BIGINT DEFAULT 0,
        last_modified VARCHAR(255),
        etag VARCHAR(255),
        enabled TINYINT(1) DEFAULT TRUE,
        download_content TINYINT(1) DEFAULT '0',
        parsing_error INT DEFAULT 0,
        parsing_error_message VARCHAR(255),
        rtl TINYINT(1) DEFAULT '0',
        cloak_referrer TINYINT(1) DEFAULT '0',
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, feed_url)
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE items (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        feed_id BIGINT NOT NULL,
        checksum VARCHAR(255) NOT NULL,
        status VARCHAR(10) NOT NULL,
        bookmark INT DEFAULT 0,
        url TEXT NOT NULL,
        title TEXT NOT NULL,
        author TEXT,
        content LONGTEXT,
        updated BIGINT,
        enclosure_url TEXT,
        enclosure_type VARCHAR(50),
        language VARCHAR(50),
        rtl TINYINT(1) DEFAULT '0',
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE,
        UNIQUE(feed_id, checksum)
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE `groups` (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(user_id, title)
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE `feeds_groups` (
        feed_id BIGINT NOT NULL,
        group_id INT NOT NULL,
        PRIMARY KEY(feed_id, group_id),
        FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE favicons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        hash VARCHAR(255) UNIQUE,
        type VARCHAR(50)
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE `favicons_feeds` (
        feed_id BIGINT NOT NULL,
        favicon_id INT NOT NULL,
        PRIMARY KEY(feed_id, favicon_id),
        FOREIGN KEY(favicon_id) REFERENCES favicons(id) ON DELETE CASCADE,
        FOREIGN KEY(feed_id) REFERENCES feeds(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE remember_me (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        ip VARCHAR(255),
        user_agent VARCHAR(255),
        token VARCHAR(255),
        sequence VARCHAR(255),
        expiration BIGINT,
        date_creation BIGINT,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB CHARSET=utf8");

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
