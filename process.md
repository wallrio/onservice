

# Process
service for create process parallel

## Methods


### fork

	$server->process->fork(ARRAY_PARAMETERS);

##### Example:
```php
	$server->process->fork(array(
		'run'=>function(&$parameters,$memory,$server){
			// $parameters = array('name'=>'fulano')

			// your code

		},
		'parameters'=>array('name'=>'fulano')
	));
```
###### run:
must contain the code to be executed in the process
	
- $parameters =	information passed by the index parameters
- $memory = compartilhamento de informações entre processos
- $server = reference to server class

###### $memory

- save data
$memory->save(string,optional index);
$server->process->save(string,optional index);

- load data
$memory->save(optional index);
$server->process->load(optional index);


###### parameters:
option to pass values to the process