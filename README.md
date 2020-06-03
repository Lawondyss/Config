# Config
Base class for creating own class of configuration. Better than a associative array :-)

## Install
Over [Composer](https://getcomposer.org/)
```bash
composer require lawondyss/config
```

## Usage
```php
class DbConfig extend Lawondyss\Config
{
  public $driver;
  public $host;
  public $database;
  public $username;
  public $password;
  public $charset;
}

$dbConfig = DbConfig::fromArray([
  'driver' => 'mysqli',
  'host' => 'localhost',
  'database' => 'example',
  'username' => 'root',
  'password' => 'root',
  'charset' => 'utf8'
]);
```

Simple call individual option.
```php
$charset = $dbConfig->charset;
$dbConfig->charset = 'latin1';
```

Is implemented array access.
```php
$pass = $dbConfig['password']; 
$dbConfig['password'] = '*****';
```

If you want default values, then defined in class.
```php
class DbConfig extend Lawondyss\Config
{
  public $driver = 'mysqli';
  public $host = 'localhost';
  public $database;
  public $username;
  public $password;
  public $charset = 'utf8';
}

$defaultConfig = new DbConfig;
```

Is possible "merge" with a customized options.
```php
$lipsumDbConfig = $defaultConfig->withOptions([
  'database' => 'lipsum',
  'username' => 'lorem',
  'password' => 'ipsum',
]);
```

If another code use options as associative array:
```php
$dibi = new Dibi\Connection($lipsumDbConfig->toArray());
```

## License

MIT
