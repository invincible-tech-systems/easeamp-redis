# EaseAmpRedis
> A very simple and safe PHP library that provides methods to access Redis cache & Redis Pubsub (i.e., Redis is used as a message queue) in php applications. This wraps up the AmPHP related Redis Package to interact with Redis in-memory cache in an asynchronous & non-blocking way.

## Advantages
- Query Redis Cache in asynchronous & non-blocking way
- Handle Channel Subscribe, UnSubscribe actions for single channel as well as a channel pattern, along with channel publish operation, when interacting with Redis Pubsub.

## Getting started
With Composer, run

```sh
composer require invincible-tech-systems/easeampredis:^1.0.3
```

Note that the `vendor` folder and the `vendor/autoload.php` script are generated by Composer; they are not part of EaseAmpRedis.

To include the library,

```php
<?php
require 'vendor/autoload.php';

use InvincibleTechSystems\EaseAmpRedis\EaseAmpRedis;
```

As Amphp/dns is among the dependencies of this library, to prevent recursive DNS Server resolution errors that may occur due reasons like open_basedir restrictions/ no access to /etc/resolv.conf file on the linux server etc..., do include the following lines in your code,

```php

use \InvincibleTechSystems\EaseAmpRedis\CustomAmphpDnsConfigLoader;

$customAmphpDnsConfigValues = ["208.67.222.222:53", "208.67.220.220:53","8.8.8.8:53","[2001:4860:4860::8888]:53"];

$CustomAmphpDnsConfigLoader = new CustomAmphpDnsConfigLoader($customAmphpDnsConfigValues, 5000, 3);

\Amp\Dns\resolver(new \Amp\Dns\Rfc1035StubResolver(null, $CustomAmphpDnsConfigLoader));

```

Note: Do skip including the above, if incase similar custom DNS Config Loader is loaded from any of the other Amphp/dns dependent libraries like EaseAmyMysql (https://github.com/invincible-tech-systems/easeamp-mysql) or EaseAmpMysqlRedis (https://github.com/invincible-tech-systems/easeamp-mysql-redis) or EaseAmpMysqlHalite (https://github.com/invincible-tech-systems/easeampmysql-halite) in the application.


To Connect to Redis Server, without Password

```php

$redisHost = "tcp://10.124.0.3:6379";
$redisConnection = new EaseAmpRedis($redisHost);

```

To Connect to Redis Server, with Password, wherein, the password that is going to be used in the below is the one that is defined using requirepass directive in the redis.conf file.

```php

$redisHost = "tcp://localhost:6379?password=mypassword";
$redisConnection = new EaseAmpRedis($redisHost);

```

Note: In both with password & without password scenario based examples, port number 6379 is assumed to be used as that is the default port, as defined by Redis Software. The port number has to be changed, if Redis is hosted on a different port number.

Defining the Namespace Prefix

```php

$namespacePrefix = "My-First-App";

```

Note: Do define the namespace, the way required, to uniquely identify the specific application and to segregate keys appropriately.


To Check if a Key Exists in Redis Cache


```php


$existsCheckResult = $redisConnection->exists($namespacePrefix, 'foo');

```

The exists method expects a redis key namespace prefix along with key suffix as two parameters. 'foo' is the key suffix, as per this example. In this context, if the redis key is available, then the result will be returned as boolean(true), anything otherwise, the result will be boolean(false).



To Check the Type of the Key in Redis Cache

```php

$typeCheckResult = $redisConnection->type($namespacePrefix, 'foo');

```

The type method expects a redis key namespace prefix along with key suffix as two parameters. 'foo' is the key suffix, as per this example. In this context, the result will the data type of the value of specific Redis Key. If the result is a valid one, it can be in the lines of string, list, set, zset, hash, anything otherwise, it will be 'None'.



To Set the Expiry Time to a Key in Redis Cache

```php

$result = $redisConnection->expire($namespacePrefix, 'foo',3000);

```

The expire method expects three parameters as input, that constitutes a redis key namespace prefix, redis key suffix and the number of seconds for the key to expire (defined as milliseconds). 'foo' is the key suffix and 3000 is the number of milliseconds definition, as per this example. In this context, the result of the operation will be boolean(true) if successful and anything otherwise, the result will be boolean(false).


To Increment the Value of the Key in the Redis cache

```php

$result = $redisConnection->incrBy($namespacePrefix, 'foo', 3);

```

The incrBy method can be used, that accepts three parameters as input like a redis key namespace prefix, redis key suffix and the number to do the increment operation. If in case the key do not exist, it is set to 0 before performing the operation.


To Decrement the Value of the Key in the Redis cache

```php

$result = $redisConnection->decrBy($namespacePrefix, 'foo', 3);

```

The decrBy method can be used, that accepts three parameters as input like a redis key namespace prefix, redis key suffix and the number to do the decrement operation. If in case the key do not exist, it is set to 0 before performing the operation.


To Set Single or Multiple keys and Set their Values as Strings Passing in Data as Associative Array

```php

$result = $redisConnection->stringSet(['f33'=>'jai','h33'=>'ram']);

```

The stringSet method sets the keys('f33','h33') values with specified strings('jai','ram'). The result will be true if operation is success otherwise the result will be false.


To Push the Values to the list of the Specified Redis Key, from head and in the form of array, in Redis cache

```php

$result = $redisConnection->listPush('H', $namespacePrefix, 'list100', ['a','b']);

```

Note: The listPush method returns the length of the list after the push operation.


To Push the Values to the list of the Specified Redis key, from Tail and in the form of array, in Redis cache

```php

$result = $redisConnection->listPush('T', $namespacePrefix, 'list100', ['c','d']);

```

Note: The listPush method returns the length of the list after the push operation.


To Remove the Values from the list of the Specified Redis key, from Head, in Redis cache

```php

$result = $redisConnection->listPop('H', $namespacePrefix, 'list100');

```

Note: The listPop method returns the deleted element of the list after the pop operation.


To Remove the Values from the list of the Specified Redis key, from Tail, in Redis cache

```php

$result = $redisConnection->listPop('T', $namespacePrefix, 'list100');

```

Note: The listPop method returns the deleted element of the list after the pop operation.


To Set the Specified Fields to their respective values in the Hash stored at Key, in Redis cache

```php

$result = $redisConnection->mapSet($namespacePrefix, 'map100', ['yoo'=>'1','moo'=>'2']);

```

This command overwrites any specified fields already existing in the hash. If the key does not exist, a new key holding a hash is created. The result will be true if operation is success, anything else, the result will be false.


To Get the Value of the Redis key, from Redis cache

```php

$result = $redisConnection->get($namespacePrefix, 'f33');

```

according to type of data of specific Redis key, get method returns the value using appropriate redis command internally

        if type of key is string, get method returns the value of the key as a string
                                          
        if type of key is list, get method returns the value of the key as a numeric index array
                                          
        if type of key is hash, get method returns the value of the key as an associative array


To Subscribe to a Channel, on Redis Pubsub


```php

$result = $redisConnection->subscribe('hello');

```

A Channel can be subscribed to in the application using subscribe method. Since this activity is a blocking operation, it is advised to extend the EaseAmpRedis Class and then write code related to respective business logic inside the subscribe method in the extended class.


To pSubscribe to multiple channels, based on the Specified Pattern, on Redis Pubsub

```php

$result = $redisConnection->pSubscribe('h?llo');

```

One or more Channels can be subscribed to in the application using pSubscribe method, based on specified pattern('h?llo'). Examples include: channel names like 'hello','htllo','hxllo' etc. Since this activity is a blocking operation, it is advised to extend the EaseAmpRedis Class and then write code related to respective business logic inside the pSubscribe method in the extended class.


To Publish a Message to a Channel, on Redis Pubsub

```php

$result = $redisConnection->publish('koo','Hello');

```

The publish method publishes the message('Hello') to the channel('koo'). If the message is successfully published to the specified channel, then all those users (i.e., web socket clients) who had subscribed to that channel will receive the message.


To Unsubscribe a Specific Channel, on Redis Pubsub

```php

$result = $redisConnection->unSubscribe('koo');

```

The unSubscribe method unsubscribes the user from the specified channel('koo'). If the result is true, the user will not receive any messages that are published to that channel thereafter.


To Unsubscribe from multiple channels, based on the Specified Pattern, on Redis Pubsub

```php

$result = $redisConnection->pUnSubscribe('h?llo');

```

The pUnSubscribe method unsubscribes the user from all channels, that are based on specified pattern('h?llo'). Examples include: channel names like 'hello','htllo','hxllo' etc. If the result is true, the user will not receive any messages that are published to all the channels matching that specified pattern.



## Contributors
Krishnaveni (krishnaveni.nimmala@invincibletechsystems.com)

## License
This software is distributed under the [MIT](https://opensource.org/licenses/MIT) license. Please read [LICENSE](https://github.com/easeappphp/PDOLight/blob/main/LICENSE) for information on the software availability and distribution.
