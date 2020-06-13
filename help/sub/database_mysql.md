

# Database Using Mysql Driver
abstracts the communication with database using Mysql driver

## Instance 

```php

use onservice\services\Database as Database;
use onservice\services\database\Mysql as Mysql;

$database = new Database( new Mysql(HOST,USERNAME,PASSWORD,BASE) ) ;
```

- The parameter *BASE* is optional

## Setting 
the method *config* is optional if was settings on instance

```php
$database = new Database( new Mysql() );

$database->config(array(
	'host'=>'localhost',
	'basename'=>'basename',
	'username'=>'username',
	'password'=>'password',
));
```





## Methods


#### Create base:
```php
$database->createBase(BASE_NAME);
```

##### Example 

```php
$database = new Database( new Mysql() );

$database->config(array(
	'host'=>'localhost',
	'username'=>'username',
	'password'=>'password',
));

$resultCreateBase = $database->createBase('BASENAME');					
$resultCreateBase = $database->base('BASENAME');

```



#### Create table:
```php
$database->createTable(TABLE_NAME,FIELDS);
```

Example:
```php
$database->createTable('users',array(
'id'=>'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
'name'=> 'VARCHAR(30) NOT NULL',
'email'=> 'VARCHAR(150)',
'created'=> 'VARCHAR(20)'
));

```

#### Select register:
```php
$result = $database->select(TABLE_NAME,WHERE);
```

- Example:

```php
$server->database->select('users','username = "fulano"');
```


#### INSERT register:
```php
$database->insert(TABLE_NAME,FIELD);
```

Example:

```php
$database->insert('users',array(
	'name'		=>	'Fulano da Silva',
	'email'		=>	'fulano@email.com',
	'created'	=>	time(),
));
```

#### Delete register:
```php
$database->delete(TABLE_NAME,WHERE);
```
Example:

```php
$database->delete('users','id = "abc01"');
```

#### UPDATE register:
```php
$database->update(TABLE_NAME,WHERE);
```

Example:
```php
$database->update('users',array(
	'name'		=>	'Fulano da Silva',
	'email'		=>	'fulano@email.com',
	'created'	=>	time(),
),'name = "Wallace Rio"');
```

