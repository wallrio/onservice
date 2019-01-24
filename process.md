

# Process
service for create process parallel

## Methods


### fork

	$server->process->fork(ARRAY_PARAMETERS);

##### Example basic:
```php
	$server->process->fork(array(
		'run'=>function($parameters,$memory,$server,$pid,$pidParent){
			// $parameters = array('name'=>'fulano')

			// your code

		},
		'parameters'=>array('name'=>'fulano')
	));
```


##### Example with process Parent:
```php
	$server->process->fork(array(
		'run'=>function($parameters,$memory,$server,$pid,$pidParent){
			// $parameters = array('name'=>'fulano')

			// your code

		},
		'parameters'=>array('name'=>'fulano'),
		'parent'=>function($parameters,$memory,$server,$pid,$pidChild){ // optional
			// parent proccess
		}
	));
```

###### run:
must contain the code to be executed in the process
	
- $parameters =	information passed by the index parameters
- $memory = compartilhamento de informações entre processos
- $server = reference to server class

###### $memory

- save data
	- $memory->save(string,optional index);
	- $server->process->save(string,optional index);

- load data
	- $memory->save(optional index);
	- $server->process->load(optional index);

###### parameters:
option to pass values to the process


### memory

#### Changing the buffer size
by default the buffer size is 16000000 bytes ~ 16 Mb, if you need to increase this size, use the method below by passing the integer value in bytes to the buffer.

- Example for 50 Mb:

	$server->process->memory->setBuffer(50000000);

#### Changing the permission
define the permissions on the memory segment, which are expressed in the same way as the UNIX file system permissions that is given as example 0666.

	$server->process->memory->setPermission(0666);