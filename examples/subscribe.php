<?php

require '../vendor/autoload.php';

use InvincibleTechSystems\EaseAmpRedis\EaseAmpRedis;

$method = new EaseAmpRedis('tcp://localhost:6379');


/*  Method for Subscribe  the channel   */    

$response =$method->subscribe('koo');

var_dump($response); //true

 echo "<br>";

/*  Method for Subscribe the pattern for subscribe all the channels matching the pattern   */ 

$response =$method->pSubscribe('h?llo');

var_dump($response); //true

echo "<br>";

