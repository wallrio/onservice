# Database OJM
Implementation of Object JSON Mapper, to be used with the JSON Driver database.

Abstracts communication with database via class.

## Instance 

```php

use onservice\services\database\mapper\OJM as OJM;

$ojm = new OJM;
```

## Setting 

- Example

```php
$ojm = new OJM;

$parameters = array(
	'dir'=>'./database-directory', // optional - default = temporary dir	
	'basename'=>'basename'
);

$ojm->setup($parameters);
```


## Find registers

> $users = $ojm->find(TABLE,WHERE);

- Example

```php
$users = $ojm->find('users',array('username'=>'fulano'));
print_r($users);
```

- Example with operator OR '||'

```php
$users = $ojm->find('users',array('username'=>'fulano', '||.username'=>'ciclano'));
print_r($users);
```

- Example with soundex '~'

```php
$users = $ojm->find('users',array('name'=>'~Fulanu'));
print_r($users);
```


- Example with part of the string '*'

```php
$users = $ojm->find('users',array('name'=>'*Ful'));
print_r($users);
```



## Find and Update

- Example

```php
$users = $ojm->find(TABLE,WHERE);
$users[0]->email = 'newmail@domain.com';
$users[0]->save();
```

### Mode save global

- Example
```php
// $users = $ojm->find('device');
// $users[0]->email = 'newmail@domain.com';

$ojm->save($users);
```

### Create a new register on table

- Example

```php
$users = $ojm->create(TABLE);
$users->name = 'Fulano da Silva';
$users->username = 'fulano';
$users->created = time();
$users->save();

// $ojm->save($users); // method optional to save
```


### Remove a register on table

- Example

```php
$users = $ojm->find(TABLE,WHERE);
$ojm->remove($users); 

$users[0]->remove();// method optional to save
```


