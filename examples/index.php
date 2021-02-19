<?php

require '../vendor/autoload.php';

use \InvincibleTechSystems\EaseAmpRedis\EaseAmpRedis;
use \InvincibleTechSystems\EaseAmpRedis\CustomAmphpDnsConfigLoader;

$customAmphpDnsConfigValues = ["208.67.222.222:53", "208.67.220.220:53","8.8.8.8:53","[2001:4860:4860::8888]:53"];

$CustomAmphpDnsConfigLoader = new CustomAmphpDnsConfigLoader($customAmphpDnsConfigValues, 5000, 3);

\Amp\Dns\resolver(new \Amp\Dns\Rfc1035StubResolver(null, $CustomAmphpDnsConfigLoader));



$method = new EaseAmpRedis('tcp://localhost:6379');

$namespacePrefix = "My-First-App";

/*    Method for checking key exist or not if exist return true otherwise return false   */  

$response =$method->exists($namespacePrefix, 'foo');

var_dump($response); //true

echo "<br>";


/*  Method for checking type of the key if key exist return type of the key (string, list, set, zset, hash) otherwise return none   */

$response =$method->type($namespacePrefix, 'foo');

var_dump($response); //string

 echo "<br>";


 /*    Method for checking key expire or not if exist return true otherwise return false   */  

$response =$method->expire($namespacePrefix, 'foo',3000);

var_dump($response); //true

echo "<br>";


 /*  Method for Incrementing the key by number . If number is given as second argument the key is incremented by given number otherwise the key is incremented by one. If the key does not exist, it is set to 0 before performing the operation.   */ 

 $response =$method->incrBy($namespacePrefix, 'goo');

var_dump($response); //1
echo "<br>";
$response =$method->incrBy($namespacePrefix, 'goo',3);

var_dump($response); //4
echo "<br>";


/*  Method for Decrementing the key by number . If number is given as second argument the key is decremented by given number otherwise the key is decremented by one. If the key does not exist, it is set to 0 before performing the operation.   */

 $response =$method->decrBy($namespacePrefix, 'goo');

var_dump($response); //3
echo "<br>";
$response =$method->decrBy($namespacePrefix, 'goo',3);

var_dump($response); //0
echo "<br>";

//exit;
/*  Method for Publishing the message to the channel and It returns the number of clients that received the message   */ 

$response =$method->publish('koo','Hello');

var_dump($response); //0
echo "<br>";
 

/*  Method for Unsubscribe  the channel   */    

$response =$method->unSubscribe('koo');

var_dump($response); //true
 echo "<br>";

 

/*  Method for Unsubscribe the pattern for unsubscribe all the channels matching the pattern   */ 

$response =$method->pUnSubscribe('h?llo');

var_dump($response); //true
 echo "<br>";


/*  Method to set single and multiple keys and set their values as strings passing in data as array   */ 

$response =$method->stringSet(['f33'=>'bgtvvggg','h33'=>'dgdgdgg']);

var_dump($response); //true
echo "<br>";


/*  Method to push values to list from head or tail of the list in the form of array and return the length of the list after the push operations.  */   
    
$response =$method->listPush('H', $namespacePrefix, 'list1001', ['a']);

var_dump($response); //1
echo "<br>";
$response =$method->listPush('H', $namespacePrefix, 'list1001', ['b','c']);

var_dump($response); //3
echo "<br>";
$response =$method->listPush('T', $namespacePrefix, 'list1001', ['d']);

var_dump($response); //4
echo "<br>";
 

/*  Method to pop values from list from head or tail of the list in the form of array and return the deleted element of the list after the pop operations.  */ 

$response =$method->listPop('H', $namespacePrefix, 'list1001');

var_dump($response); //c
echo "<br>";
$response =$method->listPop('T', $namespacePrefix, 'list1001');

var_dump($response); //d
echo "<br>";


/* Sets the specified fields to their respective values in the hash stored at key. This command overwrites any specified fields already existing in the hash. If key does not exist, a new key holding a hash is created.   */    

$response =$method->mapSet($namespacePrefix, 'map1001', ['yoo'=>'1','moo'=>'2']);

var_dump($response); //true

echo "<br>";

/*  Method for getting value of the key 
        
        if key exist test type of key
                    
        according to type returns the value of the respective key

        if type of key is string returns value of the key
                                          
        if type of key is list returns the list of values as array
                                          
        if type of key is hash returns the fields and their respective values as array  */  

$response =$method->get($namespacePrefix, 'f33');

var_dump($response); //bgtvvggg
echo "<br>";
$response =$method->get($namespacePrefix, 'list1001');

var_dump($response); //[b,a]
echo "<br>";
$response =$method->get($namespacePrefix, 'map1001');

var_dump($response); //['yoo'=>'1','moo'=>'2']
echo "<br>";