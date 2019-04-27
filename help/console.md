

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
	- dir: (string) define the directory of commands.

	- core: (boolean) enables/disables console display via '-c' option.
	- title = (string) define the title of console.
	- legend = (string) define the legende of console.

	- titleForecolor = (color) define the forecolor of title.
	- titleBackcolor = (color) define the backcolor of title.
	- titleBold = (boolean) define the font bold of title.

	- commandForecolor = (color) define the forecolor of command.
	- commandBackcolor = (color) define the backcolor of command.
	- commandBold = (boolean) define the font bold of command.

	- commandTitleForecolor = (color) define the forecolor of command title.
	- commandTitleBackcolor = (color) define the backcolor of command title.
	- commandTitleBold = (boolean) define the font bold of command title.

	- descriptionForecolor = (color) define the forecolor of description.
	- descriptionBackcolor = (color) define the backcolor of description.
	- descriptionBold = (boolean) define the font bold of description.

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

2. Create the directory specified in the previous step.

3. Create a class for the command to be made available.

### Example
For the 'walk' command, create the 'Walk.php' class.
```
src
 |
 |--console
 		|
 		|--Walk.php
```

- Note: it is possible to create sub level for the command.

### Example
For the 'walk' command exemplified above, create in the 'walk.php' class the method below:

```php
namespace console;

class Walk {
	/** @description: description of command **/
	public function index(){		
		return 'index...';
	}
}
```

### Example sub commands

```
src
 |
 |--console
 		|
 		|--help
 			|
 			|--Walk.php
```


> for the above directory, the command will be 'help/walk'

4. In the created class, create one the methods of the specific commands

### Example
For the 'help/walk' command exemplified above, create in the 'help/walk.php' class the method below:

```php
namespace console\help;

class Walk {
	/** @description: description of command **/
	public function index(){		
		return 'index...';
	}
}
```


### Example of additional commands
For the 'help/walk: run' command, create the following method in the 'help/walk.php' class:

```php
namespace console\help;

class Walk {
	/** @description: description of command **/
	public function run(){		
		return 'running...';
	}
}
```


## Change the description of the command group
Enter the 'description' property in the command class.


### Example

```php
namespace console\help;

class Walk {

	public $description = "Description group of commands";

	...
}
```

## Sort commands
Enter a property with the name 'order' with the position priority value in the command class.

### Example

```php
namespace console\help;

class Walk {

	public $order = 1;

	...
}
```

### Sort Sub Commands
Enter the '@order' attribute in the method annotation.

```php
namespace console\help;

class Walk {

	public $order = 1;

	/** 
		@order: 0
		@description: description of command 
	**/
	public function run(){		
		return 'running...';
	}
}
```

### Custom name
Enter the '@name' attribute in the method annotation.

```php
namespace console\help;

class Walk {

	public $order = 1;

	/** 
		@name: walk-run
		@description: description of command 
	**/
	public function run(){		
		return 'running...';
	}
}
```

## Note
always use the namespace to avoid class conflict