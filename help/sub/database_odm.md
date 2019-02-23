# Database ODM
Implementation of Object Document Mapper, to be used with the Mongo database.

Abstracts communication with database via class.

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\database\mapper\ODM as ODM;

$server = new CreateServer(new ODM);
```

## Setting 

- Example

```php
$server = new CreateServer(new ODM);

$parameters = array(
	'basename'=>'basename'
);

$server->odm->setup($parameters);
```


## Find registers

> $users = $server->odm->find(TABLE,WHERE);

- Example

```php
$users = $server->odm->find('users',array('username'=>'fulano'));
print_r($users);
```

- Example with operator OR '||'

```php
$users = $server->odm->find('users',array('username'=>'fulano', '||.username'=>'ciclano'));
print_r($users);
```



## Find and Update

- Example

```php
$users = $server->odm->find(TABLE,WHERE);
$users[0]->email = 'newmail@domain.com';
$users[0]->save();
```

### Mode save global

- Example
```php
// $users = $server->odm->find('device');
// $users[0]->email = 'newmail@domain.com';

$server->odm->save($users);
```

### Create a new register on table

- Example

```php
$users = $server->odm->create(TABLE);
$users->name = 'Fulano da Silva';
$users->username = 'fulano';
$users->created = time();
$users->save();

// $server->odm->save($users); // method optional to save
```


### Remove a register on table

- Example

```php
$users = $server->odm->find(TABLE,WHERE);
$server->odm->remove($users); 

$users[0]->remove();// method optional to save
```



