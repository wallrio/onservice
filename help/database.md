


# Database
abstracts the communication with database

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Database as Database;
use onservice\services\Database\Mysql as Mysql;
use onservice\services\Database\JSON as JSON;
use onservice\services\Database\Mongo as Mongo;

$server = new CreateServer( new Database( DRIVER ) );
```

> DRIVER is the method of persistence

# Drivers available

- [Mysql](help/database_mysql.md)
- [JSON](help/database_json.md)
- [Mongo](help/database_mongo.md)
