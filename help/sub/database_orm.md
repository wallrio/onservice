# Database ORM
Implementation of Object Relational Mapper.

Abstracts communication with database via class.

## Instance 

```php

use onservice\services\database\mapper\ORM as ORM;

$orm = new ORM;
```

## Setting 

- Example

```php
$orm = new ORM;

$parameters = array(
	'driver'=>'driver',
	'host'=>'host',
	'username'=>'username',
	'password'=>'password',
	'basename'=>'basename'
);

$orm->setup($parameters);
```


## Find registers

> $users = $orm->find(TABLE,WHERE);

- Example

```php
$users = $orm->find('users',array('username'=>'fulano'));
print_r($users);
```

- Example with operator OR '||'

```php
$users = $orm->find('users',array('username'=>'fulano', '||.username'=>'ciclano'));
print_r($users);
```

- Example with soundex '~'

```php
$users = $orm->find('users',array('name'=>'~Fulanu'));
print_r($users);
```



## Find and Update

- Example

```php
$users = $orm->find(TABLE,WHERE);
$users[0]->email = 'newmail@domain.com';
$users[0]->save();
```

### Mode save global

- Example
```php
// $users = $orm->find('device');
// $users[0]->email = 'newmail@domain.com';

$orm->save($users);
```

### Create a new register on table

- Example

```php
$users = $orm->create(TABLE);
$users->name = 'Fulano da Silva';
$users->username = 'fulano';
$users->created = time();
$users->save();

// $orm->save($users); // method optional to save
```


### Remove a register on table

- Example

```php
$users = $orm->find(TABLE,WHERE);
$orm->remove($users); 

$users[0]->remove();// method optional to save
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
		'created'=> 'type:int, size:15' //field,
		'desciption'=> 'type:longtext' //field
	)
);

$orm->scheme($scheme);

```

> The 'scheme' only creates or adds tables and fields, it is not possible to remove infODMation in the database by the 'scheme', to remove it will require manual action.