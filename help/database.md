# Database
abstracts the communication with database

## Instance 

```php

use onservice\CreateServer as CreateServer;
use onservice\services\Database as Database;
use onservice\services\database\Mysql as Mysql;
use onservice\services\database\JSON as JSON;
use onservice\services\database\Mongo as Mongo;

$server = new CreateServer( new Database( DRIVER ) );
```

> DRIVER is the method of persistence

# Drivers available

- [Mysql](sub/database_mysql.md)
- [JSON](sub/database_json.md)
- [Mongo](sub/database_mongo.md)
- [ORM](sub/database_orm.md)
