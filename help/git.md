# Git
Workflow Service for version control with GIT

## Methods


### get
downloads the files from the repository to the local working directory

	$server->git->get(ARRAY_PARAMETERS);

##### Example basic:
```php
	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git',
		'workspace'=> getcwd()
	));
```

##### Example to directory private

```php
	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git'
		'workspace'=> getcwd(),
		'username'=>'username',
		'password'=>'password'
	));
```

##### Example to download from another branch

```php
	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git',
		'branch'=>'development'
	));
```

##### Exemplo para baixar de um diretório especifico na branch

```php
	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git',
		'branch'=>'development',
		'directory'=>'src'
	));
```

##### Parametros

- url (string)
	- define the git repository

- branch (string)
	- define the branch of the repository that will be downloaded

- workspace (string)
	- defines the directory where the files of the remote repository will be downloaded

- directory (string)
	- download only a certain directory from a branch

- username
	- Specifies the private repository owner's username

- password
	- Specifies the private repository owner password

- clearworkspace (boolean)
	- removes the directory marked in the workspace before downloading the repository
	- Caution: this parameter can delete important files in your working directory, so be careful when using it