PicoDb
======

PicoDb is a minimalist database query builder for PHP.
**It's not an ORM**.

Features
--------

- Easy to use, easy to hack, fast and very lightweight
- Supported drivers: Sqlite, Mysql, Postgresql
- Requires only PDO
- Use prepared statements
- Handle schema versions (migrations)
- License: [WTFPL](http://www.wtfpl.net)

Requirements
------------

- PHP >= 5.3
- PDO
- A database: Sqlite, Mysql or Postgresql

Documentation
-------------

### Installation

```bash
composer require fguillot/picodb @stable
```

### Connect to your database

```php
use PicoDb\Database;

// Sqlite driver
$db = new Database(['driver' => 'sqlite', 'filename' => ':memory:']);

// Mysql driver
// Optional options: "schema_table" (the default table name is "schema_version")
$db = new Database(array(
    'driver' => 'mysql',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'my_db_name',
    'charset' => 'utf8',
));
```

### Execute a SQL request

```php
$db->execute('CREATE TABLE toto (column1 TEXT)');
```

### Insert some data

```php
$db->table('toto')->save(['column1' => 'test']);
```

or

```php
$db->table('toto')->insert(['column1' => 'test']);
```

### Transactions

```php
$db->transaction(function ($db) {
    $db->table('toto')->save(['column1' => 'foo']);
    $db->table('toto')->save(['column1' => 'bar']);
});
```

or

```php
$db->startTransaction();
// Do something...
$db->closeTransaction();

// Rollback
$db->cancelTransaction();
```

### Fetch all data

```php
$records = $db->table('toto')->findAll();

foreach ($records as $record) {
    var_dump($record['column1']);
}
```

### Update something

```php
$db->table('toto')->eq('id', 1)->save(['column1' => 'hey']);
```

You just need to add a condition to perform an update.

### Remove rows

```php
$db->table('toto')->lowerThan('column1', 10)->remove();
```

### Sorting

```php
$db->table('toto')->asc('column1')->findAll();
```

or

```php
$db->table('toto')->desc('column1')->findAll();
```

or 

```php
#db->table('toto')->orderBy('column1', 'ASC')->findAll();
```

### Limit and offset

```php
$db->table('toto')->limit(10)->offset(5)->findAll();
```

### Fetch only some columns

```php
$db->table('toto')->columns('column1', 'column2')->findAll();
```

### Fetch only one column

Many rows:

```php
$db->table('toto')->findAllByColumn('column1');
```

One row:

```php
$db->table('toto')->findOneColumn('column1');
```

### Equals condition

```php
$db->table('toto')
   ->equals('column1', 'hey')
   ->findAll();
```

or

```php
$db->table('toto')
   ->eq('column1', 'hey')
   ->findAll();
```

Yout got: 'SELECT * FROM toto WHERE column1=?'

### IN condition

```php
$db->table('toto')
       ->in('column1', ['hey', 'bla'])
       ->findAll();
```

### Like condition

Case-sensitive (only Mysql and Postgres):

```php
$db->table('toto')
   ->like('column1', '%Foo%')
   ->findAll();
```

Not case-sensitive:

```php
$db->table('toto')
   ->ilike('column1', '%foo%')
   ->findAll();
```

### Lower than

```php
$db->table('toto')
   ->lowerThan('column1', 2)
   ->findAll();
```

or

```php
$db->table('toto')
   ->lt('column1', 2)
   ->findAll();
```

### Lower than or equals

```php
$db->table('toto')
   ->lowerThanOrEquals('column1', 2)
   ->findAll();
```

or

```php
$db->table('toto')
   ->lte('column1', 2)
   ->findAll();
```

### Greater than

```php
$db->table('toto')
   ->greaterThan('column1', 3)
   ->findAll();
```

or

```php
$db->table('toto')
   ->gt('column1', 3)
   ->findAll();
```

### Greater than or equals

```php
$db->table('toto')
   ->greaterThanOrEquals('column1', 3)
   ->findAll();
```

or

```php
$db->table('toto')
    ->gte('column1', 3)
    ->findAll();
```

### Multiple conditions

Each condition is joined by a AND.

```php
$db->table('toto')
    ->like('column2', '%toto')
    ->gte('column1', 3)
    ->findAll();
```

How to make a OR condition:

```php
$db->table('toto')
    ->beginOr()
    ->like('column2', '%toto')
    ->gte('column1', 3)
    ->closeOr()
    ->eq('column5', 'titi')
    ->findAll();
```

### Debugging

Log generated queries:

```php
$db->log_queries = true;
```

Mesure each query time:

```php
$db->stopwatch = true;
```

Get the number of queries executed:

```php
echo $db->nb_queries;
```

Get log messages:

```php
print_r($db->getLogMessages());
```

### Hashtable (key/value store)

How to use a table as a key/value store:

```php
$db->execute(
     'CREATE TABLE toto (
         column1 TEXT NOT NULL UNIQUE,
         column2 TEXT default NULL
     )'
);

$db->table('toto')->insert(['column1' => 'option1', 'column2' => 'value1']);
```

Add/Replace some values:

```php
$db->hashtable('toto')
   ->columnKey('column1')
   ->columnValue('column2')
   ->put(['option1' => 'new value', 'option2' => 'value2']));
```

Get all values:

```php
$result = $db->hashtable('toto')->columnKey('column1')->columnValue('column2')->get();
print_r($result);

Array
(
    [option2] => value2
    [option1] => new value
)
```

or

```php
$result = $db->hashtable('toto')->getAll('column1', 'column2');
```

Get a specific value:

```php
$db->hashtable('toto')
   ->columnKey('column1')
   ->columnValue('column2')
   ->put(['option3' => 'value3']);

$result = $db->hashtable('toto')
             ->columnKey('column1')
             ->columnValue('column2')
             ->get('option1', 'option3');

print_r($result);

Array
(
    [option1] => new value
    [option3] => value3
)
```

### Schema migrations

#### Define a migration

- Migrations are defined in simple functions inside a namespace named "Schema".
- An instance of PDO is passed to first argument of the function.
- Function names has the version number at the end.

Example:

```php
namespace Schema;

function version_1($pdo)
{
    $pdo->exec('
        CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            name TEXT UNIQUE,
            email TEXT UNIQUE,
            password TEXT
        )
    ');
}


function version_2($pdo)
{
    $pdo->exec('
        CREATE TABLE tags (
            id INTEGER PRIMARY KEY,
            name TEXT UNIQUE
        )
    ');
}
```

#### Run schema update automatically

- The method "check()" executes all migrations until to reach the correct version number.
- If we are already on the last version nothing will happen.
- The schema version for the driver Sqlite is stored inside a variable (PRAGMA user_version)
- You can use that with a dependency injection controller.

Example:

```php
$last_schema_version = 5;

$db = new PicoDb\Database(array(
    'driver' => 'sqlite',
    'filename' => '/tmp/mydb.sqlite'
));

if ($db->schema()->check($last_schema_version)) {

    // Do something...
}
else {

    die('Unable to migrate database schema.');
}
```

### Use a singleton to handle database instances

Setup a new instance:

```php
PicoDb\Database::bootstrap('myinstance', function() {

    $db = new PicoDb\Database(array(
        'driver' => 'sqlite',
        'filename' => DB_FILENAME
    ));

    if ($db->schema()->check(DB_VERSION)) {
        return $db;
    }
    else {
        die('Unable to migrate database schema.');
    }
});
```

Get this instance anywhere in your code:

```php
PicoDb\Database::get('myinstance')->table(...)
```
