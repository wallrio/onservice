Memory
======
Saves data in memory, useful for interprocess communication.


	$memory->save( ANY_TYPE_VALUE, ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
save of content

	$memory->load( ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
show content of register
	
	$memory->get( ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
show content and clean the register

	$memory->clean( ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
clean the register of content

	$memory->destroy( );
clean all register


##### Example basic
```php

	use onservice\services\process\MemoryProcess as MemoryProcess;

	$memory = new MemoryProcess;

	$memory->save('VALUE');
	
	echo $memory->load();

```


##### Example with id
```php

	use onservice\services\process\MemoryProcess as MemoryProcess;

	$memory = new MemoryProcess;

	$memory->save('VALUE', 1 );
	
	echo $memory->load(1);

```

#### get type of stream

	$memory->getType();

#### set Identifcation global 

	$memory->setIdentifier();

#### Changing the buffer size
by default the buffer size is 16000000 bytes ~ 16 Mb, if you need to increase this size, use the method below by passing the integer value in bytes to the buffer.

- Example for 50 Mb:

	$server->process->memory->setBuffer(50000000);

#### Changing the permission
define the permissions on the memory segment, which are expressed in the same way as the UNIX file system permissions that is given as example 0666.

	$server->process->memory->setPermission(0666);