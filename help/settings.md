
# Settings
To create parameters and settings, which will be used in the application


## Instance

	new Settings(PATH)

- PATH (optional)
    - settings directory path
    - URL of the configuration file (JSON | YML)
    - if omitted, the configuration directory will be (./settings/)

##### Example:
```php
use onservice\CreateServer as CreateServer;
use onservice\services\Settings as Settings;

// create the server
$server = new CreateServer( new Settings() );

$name = $server->settings->company->name;

echo $name;
```

### Directory - example
create the configuration files in the directory defined in the constructor of the "Settings" class

- new Settings()

```
/---YOUR PROJECT
	|
	|---settings
		|
		|--company.json
		|
		|--directory
			|
			|--users.yml

```

- content of company.json

```json
{
	"technology":{
		"name":"New Company"
	}
}
```

- content of users.json

```json
{
	"brasil":{
		"name":"Fulano da silva"
	}
}
```

- Accessing the above configuration

```php
$server = new CreateServer( new Settings() );

echo $server->settings->company->technology->name;

echo $server->settings->directory->users->brasil->name;

```

### Setting remote - example

```php
$server = new CreateServer( new Settings('http://domain.com/setting.yml') );

echo $server->settings->company->name;

echo $server->settings->user->name;

```
