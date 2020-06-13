
# Settings
To create parameters and settings, which will be used in the application


## Instance

	new Settings(PATH, MODE)

- PATH (optional)
    - settings directory path
    - URL of the configuration file (JSON | YML)
    - if omitted, the configuration directory will be (./settings/)

- MODE (optional)
	- specifies the file type (yml|json)
	- useful to use as PATH a URL without extension

##### Example:
```php
use onservice\services\Settings as Settings;

// create the server
$settings = new Settings();

print_r($settings->repository);

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

- content of users.yml

```yml
brasil:
  name: Fulano da silva
```

- Accessing the above configuration

```php
$settings = new Settings();

echo $settings->repository->company->technology->name;

echo $settings->repository->directory->users->brasil->name;

```

### Setting remote - example

```php
$settings = new Settings('http://domain.com/setting.yml') ;

echo $settings->repository->company->name;

echo $settings->repository->user->name;

```


### Setting remote - example

```php
$settings = new Settings('http://domain.com/configuration-database','yml');

echo $settings->repository->database->name;

```
