FileStream
==========
Temporarily saves data to file on disk, useful for inter-process communication.

	$filestream->save( ANY_TYPE_VALUE, ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
save of content

	$filestream->load( ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
show content of register
	
	$filestream->get( ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
show content and clean the register

	$filestream->clean( ANY_TYPE_VALUE_FOR_ID, ANY_TYPE_VALUE_FOR_SEGMENT);
clean the register of content

	$filestream->destroy( );
clean all register


##### Example basic
```php

	use onservice\services\process\FileStream as FileStream;

	$filestream = new FileStream;

	$filestream->save('VALUE');
	
	echo $filestream->load();

```


##### Example with id
```php

	use onservice\services\process\FileStream as FileStream;

	$filestream = new FileStream;

	$filestream->save('VALUE', 1 );
	
	echo $filestream->load(1);

```

#### get type of stream

	$filestream->getType();
