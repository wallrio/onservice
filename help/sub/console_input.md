# Console - Input

## Question
creates a question in the terminal for the user to respond.
The return of this command will be the user's response.

	question(string $text_of_question,string $default,bollean $required,array $response,array $parameters);

## Example 

```php

use onservice\services\console\Input as Input;

$inputResponse = Input::question(
	'Deseja continuar?',
	'yes',
	true,
	array(
		'yes'=>function($parameters){			
			return true;
		},
		'no'=>function($parameters){
			return false;
		},
		'cancel'=>function($parameters){
			$name = $parameters['name'];
			echo "\n";
			echo Output::write(' Program closed, author '.$name,array('bold'=>false,'forecolor'=>'white')); 
			echo "\n\n";
			die();
		}
	),
	array('name'=>'Wallace Rio')
);
```



## Questions
creates a list of queries in the terminal for the user to respond.
The return of this command will be an array with the answers.	

	questions(array $questions,array $parameters)


```php

use onservice\services\console\Input as Input;

$inputResponse = Input::questions(array(
	array(
		'id'=>'name',
		'question'=>'Whats your name',				
		'required'=>true				
	),array(
		'id'=>'age',
		'question'=>'How old are you?',	
		'default'=>33,			
	),array(
		'id'=>'genre',
		'question'=>'What gender are you?',
		'default'=>'male',	
		'options'=>array(
			'male'=>function($parameters){ 
				return 'male';
			},
			'female'=>function($parameters){
				return 'female';
			}
		)
	),array(
		'id'=>'year',
		'question'=>'Mostrar o ano atual?',	
		'default'=>'yes',
		'options'=>array(
			'yes'=>function($parameters){ 
				$year = $parameters['year'];
				return $year;
			},
			'no'=>function($parameters){
				return false;
			}
		)
	)
),array('year'=>Date('Y')));

print_r($inputResponse);
		
```