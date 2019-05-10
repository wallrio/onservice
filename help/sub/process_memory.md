### memory

#### Changing the buffer size
by default the buffer size is 16000000 bytes ~ 16 Mb, if you need to increase this size, use the method below by passing the integer value in bytes to the buffer.

- Example for 50 Mb:

	$server->process->memory->setBuffer(50000000);

#### Changing the permission
define the permissions on the memory segment, which are expressed in the same way as the UNIX file system permissions that is given as example 0666.

	$server->process->memory->setPermission(0666);