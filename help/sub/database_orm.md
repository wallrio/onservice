# Database ORM
Implementation of Object Relational Mapper.

Abstracts communication with database via class.

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Database\mapper\ORM as ORM;

$server = new CreateServer(new ORM);
```

## Setting 

- Example

```php
$server = new CreateServer(new ORM);

$parameters = array(
	'driver'=>'driver',
	'host'=>'host',
	'username'=>'username',
	'password'=>'password',
	'basename'=>'basename'
);

$server->orm->setup($parameters);
```


## Find registers

- Example

```php
$users = $server->orm->find(TABLE,WHERE_SQL);
print_r($users);
```


## Find and Update

- Example

```php
$users = $server->orm->find(TABLE,WHERE_SQL);
$users[0]->email = 'newmail@domain.com';
$users[0]->save();
```

### Mode save global

- Example
```php
// $users = $server->orm->find('device');
// $users[0]->email = 'newmail@domain.com';

$server->orm->save($users);
```

### Create a new register on table

- Example

```php
$users = $server->orm->create(TABLE);
$users->id = 2;
$users->name = 'Fulano da Silva';
$users->username = 'fulano';
$users->created = time();
$users->save();

// $server->orm->save($users); // method optional to save
```

### Scheme
Changes the structure of the database as defined by the 'scheme' method

- Example

```php

$scheme = array(
	'device'=>array( // table
		'id'=> 'type:int, size:11, null:false, primary:true, increment:true', //field
		'type'=> 'type:varchar, size:30' //field
	),
	'users'=>array( // table
		'id'=> 'type:int, size:11, null:false, primary:true, increment:true', //field
		'name'=> 'type:varchar, size:250', //field
		'username'=> 'type:varchar, size:30', //field
		'created'=> 'type:int, size:15' //field
	)
);

$server->orm->scheme($scheme);

```

> The 'scheme' only creates or adds tables and fields, it is not possible to remove information in the database by the 'scheme', to remove it will require manual action.