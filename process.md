

# Process
service for create process parallel

## Methods


### fork

	$server->process->fork(ARRAY_PARAMETERS);

##### Example:
```php
	$server->process->fork(array(
		'run'=>function(&$parameters){
			// $parameters = array('name'=>'fulano')

			// your code

		},
		'parameters'=>array('name'=>'fulano')
	));
```
###### run:
must contain the code to be executed in the process
	
###### parameters:
option to pass values to the process