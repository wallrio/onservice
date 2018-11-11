

# Database Using Mysql Driver
abstracts the communication with database using Mysql driver

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Database as Database;
use onservice\services\Database\Mysql as Mysql;

$server = new CreateServer( new Database( new Mysql(HOST,USERNAME,PASSWORD,BASE) ) );
```

- The parameter *BASE* is optional

## Setting 
the method *config* is optional if was settings on instance

```php
$server = new CreateServer( new Database( new Mysql() ) );

$server->database->config(array(
	'host'=>'localhost',
	'basename'=>'basename',
	'username'=>'username',
	'password'=>'password',
));
```





## Methods


#### Create base:
```php
$server->database->createBase(BASE_NAME);
```

##### Example 

```php
$server = new CreateServer( new Database( new Mysql() ) );

$server->database->config(array(
	'host'=>'localhost',
	'username'=>'username',
	'password'=>'password',
));

$resultCreateBase = $server->database->createBase('BASENAME');					
$resultCreateBase = $server->database->base('BASENAME');

```



#### Create table:
```php
$server->database->createTable(TABLE_NAME,FIELDS);
```

Example:
```php
$server->database->createTable('users',array(
'id'=>'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
'name'=> 'VARCHAR(30) NOT NULL',
'email'=> 'VARCHAR(150)',
'created'=> 'VARCHAR(20)'
));

```

#### Select register:
```php
$result = $server->database->select(TABLE_NAME,WHERE);
```

#### INSERT register:
```php
$server->database->insert(TABLE_NAME,FIELD);
```

Example:

```php
$server->database->insert('users',array(
	'name'		=>	'Fulano da Silva',
	'email'		=>	'fulano@email.com',
	'created'	=>	time(),
));
```

#### Delete register:
```php
$server->database->delete(TABLE_NAME,WHERE);
```
Example:

```php
$server->database->delete('users','id = "abc01"');
```

#### UPDATE register:
```php
$server->database->update(TABLE_NAME,WHERE);
```

Example:
```php
$server->database->update('users',array(
	'name'		=>	'Fulano da Silva',
	'email'		=>	'fulano@email.com',
	'created'	=>	time(),
),'name = "Wallace Rio"');
```

