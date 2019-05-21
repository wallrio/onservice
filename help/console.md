

# Console
Service to create a custom command-line interface (CLI)

##### Example:
```php
use onservice\services\Console as Console;

// create the server
$server = new CreateServer( new Console() );
```


## Note
The code created with the "console" service must be executed exclusively in the terminal


## Methods

### instance

```php
$server = new CreateServer( new Console('ARRAY_PARAMETERS') );
```

- ARRAY_PARAMETERS: (optional)

| Attribute  			| Type   	| Description 											|
| --------------------- | --------- | ----------------------------------------------------- |
| dir  		 			| string 	| define the directory of commands  					|
| commands   			| array 	| define the commands.  								|
| core   				| boolean 	| enables/disables console display via '-c' option.  	|
| title  				| string  	| define the title of console. 							|
| legend 				| tring   	| define the legende of console. 						|
| titleForecolor 		| color 	| define the forecolor of title 						|
| titleBackcolor 		| color 	| define the backcolor of title 						|
| titleBold 			| boolean 	| define the font bold of title 						|
| commandForecolor 		| color 	| define the forecolor of command 						|
| commandBackcolor 		| color 	| define the backcolor of command 						|
| commandBold 			| boolean 	| define the font bold of command 						|
| commandTitleForecolor | color 	| define the forecolor of command title 				|
| commandTitleBackcolor | color 	| define the backcolor of command title 				|
| commandTitleBold 		| boolean 	| define the font bold of command title 				|
| descriptionForecolor 	| color 	| define the forecolor of description 					|
| descriptionBackcolor 	| color 	| define the backcolor of description 					|
| descriptionBold 		| boolean 	| define the font bold of description 					|


- NOTE: if 'dir' not specified, the path will be 'src/console'

### COLORS

- red
- white
- green
- blue
- yellow
- purple
- cian
- black
- magenta
- gray

## Running Console
Open your terminal and execute:

```bash
php your-index.php 
```


## Create commands
 To create CLI commands, follow the steps below:

1. First define the directory that will house the commands, this can be done by the constructor of class 'new Console'.

### Example

```php
require "vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Console as Console;

$server = new CreateServer(new Console(array(
	"title"=>"My Console",
	"legend"=>"v1.0",
	"dir"=>"app/consoleCommands"
)));

```

### Example complete with array

```php
require "vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Console as Console;

$server = new CreateServer(new Console(array(
	"title"=>"My Console",
	"legend"=>"v1.0",
	"dir"=>"app/consoleCommands",
	"commands" => array(
		'help'=>array(
			'order'=>2,
			'description'=>'help...',
			'function'=>function($parameters){
				return 'help ok';
			},
			array('order'=>3,'name'=>'version','description'=>'version...','function'=>function(){return 'help/version...';}),
			array('order'=>2,'name'=>'about','description'=>'about...','function'=>function(){return 'help/about...';}),
		),
	)
)));

```


### Example complete with class

1. Create your file index.php

```php
require "vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\Console as Console;

$server = new CreateServer(new Console(array(
	"title"=>"My Console",
	"legend"=>"v1.0",
	"dir"=>"app/console"
)));

```

2. Create the directory app/console

	mkdir -p app/console

3. in the app / console directory create the Help.php class

```php
namespace console;

class Help {
	
	public $order = 0;
	public $title = 'Help';
	public $description = 'help...';

	public function index($parameters){		
		return 'index...';
	}
}
```

> $parameters = displays in an array the parameters after the command

4. the structure for the Help class should be as below:

```
src
 |
 |--console
 		|
 		|--Help.php
```

- Note: it is possible to create sub level for the command.

5. create the directory app/console/help:

	mkdir -p app/console/help

6. create class 'app/console/help/about.php' who will respond to the command help/about:

```php
namespace console\help;

class About {
	
	public $order = 0;	
	public $title = 'About';
	public $description = 'about...';

	public function index($parameters){		
		return 'index...';
	}
}
```

4. the total structure should be as below:

```
src
 |
 |--console
 		|
 		|--Help.php
 		|
 		|--help
 			|--about.php
```



## Note
always use the namespace to avoid class conflict


## Input/Output on CLI

- [Input](sub/console_input.md)
- [Output](sub/console_output.md)

