<?php

use JsonRPC\Client;

require_once __DIR__.'/../../app/common.php';

abstract class BaseApiTest extends PHPUnit_Framework_TestCase
{
    protected $adminUser = array();

    public static function setUpBeforeClass()
    {
        if (DB_DRIVER === 'postgres') {
            $pdo = new PDO('pgsql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $pdo->exec("SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '".DB_NAME."' AND pid <> pg_backend_pid()");
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME.' WITH OWNER '.DB_USERNAME);
            $pdo = null;
        } else if (DB_DRIVER === 'mysql') {
            $pdo = new PDO('mysql:host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
            $stmt = $pdo->query("SELECT information_schema.processlist.id FROM information_schema.processlist WHERE information_schema.processlist.DB = '".DB_NAME."' AND id <> connection_id()");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdo->exec('KILL '.$row['id']);
            }
            $pdo->exec('DROP DATABASE '.DB_NAME);
            $pdo->exec('CREATE DATABASE '.DB_NAME);
            $pdo = null;
        } else if (file_exists(DB_FILENAME)) {
            unlink(DB_FILENAME);
        }
    }

    public function setUp()
    {
        $db = Miniflux\Database\get_connection();
        $this->adminUser = $db->table(Miniflux\Model\User\TABLE)->eq('username', 'admin')->findOne();
    }

    protected function getApiClient(array $user = array())
    {
        if (empty($user)) {
            $user = $this->adminUser;
        }

        $apiUserClient = new Client(API_URL);
        $apiUserClient->authentication($user['username'], $user['api_token']);

        return $apiUserClient;
    }
}
