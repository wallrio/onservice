# Database Using Mongo Driver
abstracts the communication with database using Mongo driver (database noSQL)

## Instance 


```php
use onservice\CreateServer as CreateServer;
use onservice\services\Database as Database;
use onservice\services\Database\Mongo as Mongo;

$server = new CreateServer( new Database( new Mongo() ) );
```

### Example

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->base('BASENAME');
```

### Configuration directly on instance

```php
$server = new CreateServer( new Database( new Mongo('BASENAME') ) );
```



## Methods


#### Create a base:

```php
$base = $server->database->createBase('BASE_NAME');
```

##### Example 

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->createBase('BASENAME');					
```


#### Collection:
Collection is where the documents will be housed, it is similar to tables in relational database.

##### Example 

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');
```


##### Create a Collection:

###### Example 

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->base('BASENAME');					
$collection = $base->createCollection('COLLECTION_NAME');
```



##### Delete a Collection:

###### Example 

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->base('BASENAME');					
$base->deleteCollection('COLLECTION_NAME');

```


#### Document:
Documents is where the data will be stored, it is similar to records of a table in a relational database.

##### Example - selecting a document 
> $result  =  $collection->document->select( WHERE );

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

$result = $collection->document->select(array(
	'username' => 'wallrio'
));

print_r($result);
```



##### Example - creating a document 
> $result  =  $collection->document->create( FIELDS );

```php
$server = new CreateServer( new Database( new Mongo() ) );
$base = $server->database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

$result = $collection->document->create(array(
	'username' => 'wallrio',
	'name' => 'Wallace Rio'
));

```


##### Example - updating a document 

> $result  =  $collection->document->update( ID, FIELDS );
```php
$server = new CreateServer( new Database( new Mongo() ) );

$base = $server->database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

$result = $collection->document->update(
	'54d3d67ce7bc9c30d69e874efc21fafc',
	array(
		'name' => 'Wallace'
	)
);

```


- Multiples id's
```php
$result = $collection->document->update(
	['54d3d67ce7bc9c30d69e874efc21fafc','aee4da48c9f47f7038fd852fea715bdb'],
	array(
		'name' => 'Wallace'
	)
);
```


- id's from select method
```php

$result = $collection->document->select(array(
	'username' => 'alla'
));

$result = $collection->document->update(
	$result,
	array(
		'name' => 'Wallace'
	)
);
```


##### Example - deleting a document 
> $result  =  $collection->document->delete( ID );
> 
```php
$server = new CreateServer( new Database( new Mongo() ) );

$server->database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $server->database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

$result = $collection->document->delete('aee4da48c9f47f7038fd852fea715bdb');
```

- Multiples id's
```php

$result = $collection->document->delete(['54d3d67ce7bc9c30d69e874efc21fafc','aee4da48c9f47f7038fd852fea715bdb']);

```

- id's from select method
```php

$resultSelect = $collection->document->select(array(
	'username' => 'alla'
));

$result = $collection->document->delete($resultSelect);
```



#### Cascading methods - Example
```php
$server = new CreateServer( new Database( new Mongo() ) );

$result = $server->database
		->base('BASENAME')
		->collection('COLLECTION_NAME')
		->document->select();

print_r($result);
```
