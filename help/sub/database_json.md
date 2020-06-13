

# Database Using JSON Driver
abstracts the communication with database using JSON driver

## Instance 


```php


use onservice\services\Database as Database;
use onservice\services\database\JSON as JSON;

$database = new Database( new JSON() );
```

## Setting 

```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => DIRECTORY_TO_SAVE_THE_DATA,	// optional - default = temporary dir
	'basename' => 'BASENAME'
));
```

- The config method is optional, if it is not used, it is necessary to use the "base" method to select the work base.

- If the "dir" parameter is not defined, then the working directory will be the system temporary directory, example: in linux system the diretory is "/tmp"


### Example

```php
$database = new Database( new JSON() );
$base = $database->base('BASENAME');
```


### Configuration directly on instance

```php
$database = new Database( new JSON(DIRECTORY_TO_SAVE_THE_DATA, BASENAME) );
```


## Methods


#### Create a base:

```php
$base = $database->createBase('BASE_NAME');
```

##### Example 

```php
$database = new Database( new JSON() );

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->createBase('BASENAME');					


```

#### Collection:
Collection is where the documents will be housed, it is similar to tables in relational database.

##### Example 

```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

```

##### Create a Collection:

###### Example 

```php
$database = new Database( new JSON() );

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
$collection = $base->createCollection('COLLECTION_NAME');

```

##### Delete a Collection:

###### Example 

```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
$base->deleteCollection('COLLECTION_NAME');

```
* A collection will only be deleted if there are no documents registered in the collection 


#### Document:
Documents is where the data will be stored, it is similar to records of a table in a relational database.

##### Example - selecting a document 
> $result  =  $collection->document->select( FIELDS, OPTIONS );

```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

$result = $collection->document->select(array(
	'username' => 'wallrio'
));

print_r($result);
```

If the select method parameter is not set, the query returns all documents in the collection

- The parameters OPTIONS is optional.

- Special consultations
It is possible to filter a query using special characters.
Enter one of the characters below at the beginning of the query string to define how the query will look.

| Key | Value |
|--|--|
| ~ | search for string synonymous (soundex) |
| * | search for string contained in query |

- Example with ~
```php
$result = $collection->document->select(array(
	'username' => '~Wallacy'
));
```
The example above Find "Wallace"

- Example with *
```php
$result = $collection->document->select(array(
	'username' => '*alla'
));
```

The example above Find "Wallace"

- Using Operators

| Key | Value |
|--|--|
| || | operator OR |
|    | operator AND |

- Example with empty (AND)

```php
$result = $collection->document->select(array(
	'username' => 'Wallacy',
	'password' => '123'
));
```

the above example looks for username 'Wallacy' AND password '123'

- Example with || (OR)


```php
$result = $collection->document->select(array(
	'username' => 'Wallacy',
	'||.username' => 'Wallace'
));

```

the above example looks for username 'Wallacy' OR username 'Wallace'


##### Example - selecting a document with options

```php
$result = $collection->document->select(array(
	'username' => 'Wallace'
),array(
	'open'=>false,
	'nocontent'=>true,
));

```

- OPTIONS:
	- nocontent: (boolean) does not display the contents of the registry.
	- open: (boolean) does not open the record in the search. (using TRUE, the response will return all records)

##### Example - creating a document 
> $result  =  $collection->document->create( FIELDS );

```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
$collection = $base->collection('COLLECTION_NAME');

$result = $collection->document->create(array(
	'username' => 'wallrio',
	'name' => 'Wallace Rio'
));

```

##### Example - updating a document 

> $result  =  $collection->document->update( ID, FIELDS );
```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
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


- update a specific level, separate the levels with a slash (/)
```php
$result = $collection->document->update(
	$result,
	array(	
		"name":"Fulano",
		"contact/email/work" => "email@work.com"
	)
);
```

> result of the above command

```json

{	
	"name":"Fulano",
	"contact":{
		"email":{
			"work" : "email@work.com"
		}
	}
}


```



##### Example - deleting a document 
> $result  =  $collection->document->delete( ID );
> 
```php
$database = new Database( new JSON() ) ;

$database->config(array(
	'dir' => __DIR__.DIRECTORY_SEPARATOR.'database',
));

$base = $database->base('BASENAME');					
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
$database = new Database( new JSON() ) ;

$result = $database
		->base('BASENAME')
		->collection('COLLECTION_NAME')
		->document->select();

print_r($result);
```


## Recomendations

### htaccess
It is explicitly recommended to use the code below to block the viewing of your JSON directories and documents.

By default the JSON Driver already creates a .htaccess file in the base directory, but as a recommendation check the existence of this file, if it does not exist, create it with the code below.

Create a .htaccess file in the main directory of your JSON database

```
# Block directory list view
Options -Indexes

# Block the visualization of document JSON
<Files "*_jdoc.json">
Order Allow,Deny
Deny from all
</Files>
```