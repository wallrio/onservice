# Git
Workflow Service for version control with GIT

## Available to

- GitHub
- GitLab


## Basic use
downloads the files from the repository to the local working directory

	$server->git->get(ARRAY_PARAMETERS);



##### Example basic:
```php

	use onservice\services\Git as Git;

	$server = new CreateServer( new Git() );

	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git',
		'workspace'=> getcwd()
	));
```

##### Example to directory private

```php

	use onservice\services\Git as Git;

	$server = new CreateServer( new Git() );

	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git'
		'workspace'=> getcwd(),
		'username'=>'username',
		'password'=>'password'
	));
```

##### Example to download from another branch

```php

	use onservice\services\Git as Git;

	$server = new CreateServer( new Git() );

	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git',
		'branch'=>'development'
	));
```

##### Exemplo para baixar de um diretório especifico na branch

```php

	use onservice\services\Git as Git;

	$server = new CreateServer( new Git() );
	
	$result = $server->git->get(array(
		'url'=>'https://github.com/user/repository.git',
		'branch'=>'development',
		'directory'=>'src'
	));
```


### GitLab

##### Example to directory private

```php
	use onservice\services\Git as Git;
	$server = new CreateServer( new Git() );
	$result = $server->git->get(array(
		'url'=>'https://gitlab.com/user/repository.git'
		'branch'=>'master',
		'workspace'=> getcwd(),
		'projectid'=>21345159,
		'token'=>'a4rEQF9NfzCmvQqxvh9'
	));
```

>  to get the 'projectid', access the project repository and copy the number that is in 'Project ID'
> to get the token, acess https://gitlab.com/profile/personal_access_tokens, and generate a new token in 'Personal Access Tokens'
	
	
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

