
# LongPolling
service for client-server communication

## Using
for use follow the steps below

1. create the server file.
2. create the client file.


# 1 step (Server)
Create the file server.php

```php
require "../../vendor/autoload.php";

use onservice\CreateServer as CreateServer;
use onservice\services\LongPolling as LongPolling;

// using persistence in file
use onservice\services\longpolling\FilePersistence as FilePersistence;
$filePersistence = new FilePersistence(__DIR__.'/users'); // directory to save data (writeable)

// create the server
$server = new CreateServer( new LongPolling($filePersistence) );

// config the connection
$server->longpolling->config( array(    
    'startinfo' => 'Welcome...', // message to send when client connect
    'updatetime'=> 5    // time in seconds to update the connection on the client
));

// set action on receiver data of client
$server->longpolling->received(function($from,$data,$service){
    // $from        =   (string)                 id of  sender
    // $data        =   (string|object|number)   message of sender
    // $service     =   (object)                 reference to service longpolling
    
    // send to all users
    foreach ($service->users() as $key => $value) {
        $id = $value->id;
        $service->recordMessage('server',$id,$data);
    }

});

// initialize the server longpolling
$server->longpolling->start();

```

## OR using MySQL

```php

use onservice\services\longpolling\MysqlPersistence as MysqlPersistence;

$MysqlPersistence = new MysqlPersistence(array(
    'host'=>'HOST',
    'basename' => 'BASENAME',
    'username' => 'USERNAME',
    'password' => 'PASSWORD'
));

$server = new CreateServer( new LongPolling($MysqlPersistence) );

```

- A database will automatically be created in your database with the name located in BASENAME

# 2 step (Client)
Create the file client.html

```html

    <!DOCTYPE html>
    <html>
    <head>
        <title></title>
        <script type="text/javascript" src="onservice.js"></script>

        <script type="text/javascript">
            var connection = new OnService.longpolling();
            
            connection.config({
            url:'.../longpolling_server.php',
            id:'ID_OF_CLIENT',  // string to identificate the client
    
            // executes when the client connects to the server
            connect:function(response){
                console.log('connect:',this.id,response);               
            },
            
            // executes when the client disconnects from the server
            disconnect:function(response){
                console.log('disconnect:',this.id,response);               
            },

            // runs from time to time, signaling that the connection is active
            keep:function(response){
                console.log('keep:',this.id,response);              
            },
            
            // executes when there is an error
            error:function(response){           
                console.log('error:',this.id,response);
            },

            // executes when a message is sent successfully
            sended:function(response){
                console.log('sended:',this.id,response);
            },
                     
            // executes when you receive new messages
            received:function(response){
                console.log('received:',this.id,response);              
            },
        });
        </script>

    </head>
    <body>
    
    </body>
    </html>

```

### Methods 

#### Connect on Server

```javascript
con1.connect();
```

#### Disconnect from the Server

```javascript
con1.disconnect();
```
#### Configurate the connection

```javascript
con1.config(OBJECT_JSON);
```
##### OBJECT_JSON
É um objeto JSON content parametros para definição e tratamento da conexão.

|Parameter|Type |Description|
|--|--|--|
|url|string  |Address of server  
|id| string/number |Identification of client
|options| JSON/String | Custom Parameters to be a Set to Server    
|connect(response)|function| returns connection information started
|disconnect(response)|function|returns connection information closed
|received(response)|function|returns messages received
|sended(response)|function|returns informations of messages sended
|keep(response)|function|returns informations of connection active
|error(response)|function|returns informations of error

#### Send message to another client


```javascript

    con1.send('ID_OF_ANOTHER_CLIENT',MESSAGE);
    // MESSAGE  =   (String|JSON|Number)

```

#### Send message to server


```javascript

    con1.send(null,MESSAGE);
    // null = identifies the server
    // MESSAGE  =   (String|JSON|Number)

```