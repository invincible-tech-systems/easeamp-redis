<?php

declare(strict_types=1);

namespace InvincibleTechSystems\EaseAmpRedis;

use \InvincibleTechSystems\EaseAmpRedis\Exceptions\EaseAmpRedisException;

/*
* Name: EaseAmpRedis
*
* Author: Veera Kumar Pasumarthi
*
* Company: Invincible Tech Systems
*
* Version: 1.0.1
*
* Description: A very simple and safe PHP library that provides methods to access redis cache in php applications. This wraps up the AmPHP related Redis Package to 
* interact with Redis in-memory cache in an asynchronous & non-blocking way.
*
* License: MIT
*
* @copyright 2020 Invincible Tech Systems
*/

use \Amp\Redis\Config;
use \Amp\Redis\Redis;
use \Amp\Redis\RemoteExecutor;
use \Amp\Iterator;
use \Amp\Delayed;
use \Amp\Loop;
use \Amp\Redis\Subscriber;
use \Amp\Redis\RedisException;
use \Amp\Redis\Subscription;
use \Amp\Socket\Socket;


class EaseAmpRedis
{
	private $result;

    private $redisHost;

    private $redisClient;

    private $subscriber;
	
	private $subscriberResult;


    public function __construct(string $redisHost)
    {   
        $this->redisHost = $redisHost;

        $remoteExecutor = new RemoteExecutor(Config::fromUri($this->redisHost));
               
        $this->redisClient = new Redis($remoteExecutor);

        $this->subscriber = new Subscriber(Config::fromUri($this->redisHost));
    }

	
    /*    Method for checking key exist or not if exist return true otherwise return false   */  

	
	public function exists(string $keyNamespacePrefix, string $keyNameSuffix)
	{
	    $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key){

	   	    try{

                $this->result = yield $this->redisClient->has($key);

            }catch(EaseAmpRedisException $e){

                echo "\n EaseAmpRedisException - ", $e->getMessage(), (int)$e->getCode();

            }

    	});

        return $this->result;
    	     	
    }

	
	/*  Method for checking type of the key if key exist return type of the key (string, list, set, zset, hash) otherwise return none   */    

	
	public function type(string $keyNamespacePrefix, string $keyNameSuffix)
	{
    	$key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key){

            try{

            	$this->result = yield $this->redisClient->getType($key);

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
    	 	
    	});

        return $this->result;
    	     	
    }


    /*  Method for set expire time for key if expire time set success returns true otherwise it returns false  */    
    

    public function expire(string $keyNamespacePrefix, string $keyNameSuffix, int $seconds)
    {
		$key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
        \Amp\Loop::run(function() use ($key,$seconds){

            try{
            
                $this->result = yield $this->redisClient->expireIn($key,$seconds);

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }

        });

        return $this->result;
                
    }


    /*  Method for Incrementing the key by number . If number is given as second argument the key is incremented by given number otherwise the key is incremented by one. If the key does not exist, it is set to 0 before performing the operation.   */    

	
	public function incrBy(string $keyNamespacePrefix, string $keyNameSuffix, int $num = 1)
	{
	    $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key,$num){

	    	try{

                $this->result = yield $this->redisClient->increment($key,$num);

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
    	 	
    	});

    	return $this->result;
    	     	
    }


    /*  Method for Decrementing the key by number . If number is given as second argument the key is decremented by given number otherwise the key is decremented by one. If the key does not exist, it is set to 0 before performing the operation.   */    

	
	public function decrBy(string $keyNamespacePrefix, string $keyNameSuffix, int $num = 1)
	{
        $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key,$num){

        	try{

                $this->result = yield $this->redisClient->decrement($key,$num);

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
    	 	
    	});

    	return $this->result;
    	     	
    }


    /*  Method for Publishing the message to the channel and It returns the number of clients that received the message   */    
    

	public function publish(string $channel , string $message)
	{
    	\Amp\Loop::run(function() use ($channel,$message){

    		try{

                $this->result = yield $this->redisClient->publish($channel,$message);

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }

        });

    	return $this->result;

    }


    
    /*  Method for Subscribe the channel   */    
    

   /*  public function subscribe(string $channel)
    {
        \Amp\Loop::run(function() use ($channel){

            try{

                $subscription = yield $this->subscriber->subscribe($channel);
                 
                $this->result = true;

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
            
        });

        return $this->result;
                
    } */
	
	public function subscribe(string $channel)
    {
        \Amp\Loop::run(function() use ($channel){

            try{

                $subscription = yield $this->subscriber->subscribe($channel);
                 
                while (yield $subscription->advance()) {
				
					$this->subscriberResult = $subscription->getCurrent();
					
				}

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
            
        });
       
	   return $this->subscriberResult;
    }


    /*  Method for Subscribe the pattern for subscribe all the channels matching the pattern   */    
    

    /* public function pSubscribe(string $pattern)
    {
    
        \Amp\Loop::run(function() use ($pattern){

            try{

                $subscriptionPattern = yield $this->subscriber->subscribeToPattern($pattern);

                $this->result = true;

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }

        });

        return $this->result;
                
    } */
	
	public function pSubscribe(string $pattern)
    {
    
        \Amp\Loop::run(function() use ($pattern){

            try{

                $subscriptionPattern = yield $this->subscriber->subscribeToPattern($pattern);

                while (yield $subscriptionPattern->advance()) {
				
					$this->subscriberResult = $subscriptionPattern->getCurrent();
					
				}

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }

        });

        return $this->subscriberResult;
                
    }


/*  Method for Unsubscribe the channel   */    
    

    public function unSubscribe(string $channel)
    {
        \Amp\Loop::run(function() use ($channel){

            try{

                $subscription = yield $this->subscriber->subscribe($channel);

                $subscription->cancel();
                 
                $this->result = true;

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
            
        });

        return $this->result;
                
    }


    /*  Method for Unsubscribe the pattern for unsubscribe all the channels matching the pattern   */    
    

    public function pUnSubscribe(string $pattern)
    {
    
        \Amp\Loop::run(function() use ($pattern){

            try{

                $subscriptionPattern = yield $this->subscriber->subscribeToPattern($pattern);

                $subscriptionPattern->cancel();

                $this->result = true;

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }

        });

        return $this->result;
                
    }


    /*  Method to set single and multiple keys and set their values as strings passing in data as array   */    
    

	public function stringSet(array $data)
    {
        \Amp\Loop::run(function() use ($data){

            try{

                $this->result = yield $this->redisClient->setMultiple($data);

                $this->result = true;

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
            
        });
	     
        return $this->result;
    	     	
    }


    /*  Method to push values to list from head or tail of the list in the form of array and return the length of the list after the push operations.  */    
    

	public function listPush(string $pushMethod, string $keyNamespacePrefix, string $keyNameSuffix, array $value)
    {
        $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key,$value,$pushMethod){

            try{

	            if($pushMethod == "H"){

                    $list = $this->redisClient->getList($key);

                    if(count($value) > 1){
     	 
     	                for($i = 0 ; $i<count($value) ; $i++){
     		  
                            $this->result =  yield $list->pushHead($value[$i]);

     	                }
            
                    } else {
     	    
                        $this->result = yield $list->pushHead($value[0]);

                    } 
 
                } else if($pushMethod == "T"){

                    $list = $this->redisClient->getList($key);

                    if(count($value) > 1){
     	 
     	                for($i = 0 ; $i < count($value) ; $i++){
     		  
                              $this->result =  yield $list->pushTail($value[$i]);

     	                }
                
                    } else {
     	    
                        $this->result = yield $list->pushTail($value[0]);

                    } 
               
                } 
	    
            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }			       

        });   

    	return $this->result;
    	     	
    }


    /*  Method to pop values from list from head or tail of the list in the form of array and return the deleted element of the list after the pop operations.  */  
    

	public function listPop(string $popMethod , string $keyNamespacePrefix, string $keyNameSuffix)
    {
	    $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key,$popMethod){

            try{

	            if($popMethod == "H"){

                    $list = $this->redisClient->getList($key);

                    $this->result = yield $list->popHead();

                } else if($popMethod == "T"){

                    $list = $this->redisClient->getList($key);

                    $this->result = yield $list->popTail();

                } 
	     	
            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }                  
		       
        });

	    return $this->result;
    	     	
    }


    /* Sets the specified fields to their respective values in the hash stored at key. This command overwrites any specified fields already existing in the hash. If key does not exist, a new key holding a hash is created.   */    
    

	public function mapSet(string $keyNamespacePrefix, string $keyNameSuffix, array $value)
    {
	    $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($key,$value){

            try{

	            $map = $this->redisClient->getMap($key);

	            $this->result = yield $map->setValues($value);

                $this->result = true;

	     	}catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }		       

        });

	    return $this->result;
    	     	
    }


    /*  Method for getting value of the key 
        
        if key exist test type of key
                    
        according to type returns the value of the respective key

        if type of key is string returns value of the key
                                          
        if type of key is list returns the list of values as array
                                          
        if type of key is hash returns the fields and their respective values as array  */    

	
	public function get(string $keyNamespacePrefix, string $keyNameSuffix)
    {
        $key = $this->getNamespacedKey($keyNamespacePrefix, $keyNameSuffix);
		
		\Amp\Loop::run(function() use ($keyNamespacePrefix,$keyNameSuffix,$key){

            try{
	
                $exist = $this->exists($keyNamespacePrefix,$keyNameSuffix);
	
	            if($exist == true){

		            $type = $this->type($keyNamespacePrefix,$keyNameSuffix);

		            if($type == "string"){
			
                        \Amp\Loop::run(function() use ($key){
            
                            $this->result = yield $this->redisClient->get($key);
            
                        });
		
		            } else if($type == "list"){

                        \Amp\Loop::run(function() use ($key){		     

		                    $list = $this->redisClient->getList($key);

			                $this->result = yield $list->getRange();

			            });

		             } else if($type == "hash"){

			            \Amp\Loop::run(function() use ($key){

			                $map = $this->redisClient->getMap($key);

			                $this->result = yield $map->getAll();

			            });
         
                     } else{

         	            $this->result = $key." does not return any type";
                     
                     }

	             } else {

	                $this->result = $key." does not exist";	
	            
                 }

            }catch(EaseAmpRedisException $e){
                       
                yield new Delayed(1000);
            }
	
    	});

        return $this->result;
    	     	
    }
	
	public function getNamespacedKey(string $keyNamespacePrefix, string $keyNameSuffix)
    {
	    
		return  $keyNamespacePrefix . ":" . $keyNameSuffix;
	         	
    }

}

