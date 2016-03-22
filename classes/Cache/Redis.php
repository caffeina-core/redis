<?php

/**
 * Cache\Redis
 *
 * Core\Cache Redis Driver.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @version 1.0
 * @copyright Caffeina srl - 2014 - http://caffeina.co
 */


namespace Cache;

class Redis implements Adapter {

  protected $redis = null;
  protected $options = [
    'scheme'      => 'tcp',
    'host'        => '127.0.0.1',
    'port'        => 6379,
    'timeout'     => 1,
    'reconnect'   => 100,
    'prefix'      => '',
    'serialize'   => true,
    'database'    => 0,
    'exceptions'  => true,
  ];

  public static function valid(){
    return true;
  }

  public function instance(){
    return $redis;
  }

  public function __construct($opt=[]){
    /**
     * Predis Docs:
     * https://github.com/nrk/predis
     */
    $this->options = array_merge($opt,$this->options);
    $this->redis = new \Predis\Client($this->options['scheme'].'://'.$this->options['host'].':'.$this->options['port'].'/',[
      'prefix'              => 'core:'.$this->options['prefix'],
      'exceptions'          => $this->options['exceptions'],
      'connection_timeout'  => $this->options['timeout'],
      'database'            => $this->options['database'],
    ]);
    $this->redis->select($this->options['database']);
  }

  public function get($key){
    return unserialize($this->redis->get($key));
  }

  public function set($key,$value,$expire=0){
    $expire ? $this->redis->setex($key,$expire,serialize($value)) : $this->redis->set($key,serialize($value));
  }

  public function delete($key){
  	$this->redis->delete($key);
  }

  public function exists($key){
    return $this->redis->exists($key);
  }

  public function flush(){
    $keys = $this->redis->keys('*');
    call_user_func_array([$this->redis,'del'],$keys);
  }

  public function inc($key,$value=1){
  	return $this->redis->incrby($key,$value);
  }

  public function dec($key,$value=1){
    return $this->redis->decrby($key,$value);
  }
}
