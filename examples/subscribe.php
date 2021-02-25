<?php

require '../vendor/autoload.php';

use \InvincibleTechSystems\EaseAmpRedis\EaseAmpRedis;
use \InvincibleTechSystems\EaseAmpRedis\CustomAmphpDnsConfigLoader;


$customAmphpDnsConfigValues = ["208.67.222.222:53", "208.67.220.220:53","8.8.8.8:53","[2001:4860:4860::8888]:53"];

$CustomAmphpDnsConfigLoader = new CustomAmphpDnsConfigLoader($customAmphpDnsConfigValues, 5000, 3);

\Amp\Dns\resolver(new \Amp\Dns\Rfc1035StubResolver(null, $CustomAmphpDnsConfigLoader));


$method = new EaseAmpRedis('tcp://localhost:6379');


/*  Method for Subscribe  the channel   */    

$response =$method->subscribe('hello');

var_dump($response);

 echo "<br>";

/*  Method for Subscribe the pattern for subscribe all the channels matching the pattern   */ 

$response =$method->pSubscribe('h?llo');

var_dump($response);

echo "<br>";

