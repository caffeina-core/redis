<?php

/**
 * Cache\Redis
 *
 * Core\Cache Redis Driver.
 *
 * @package core
 * @author stefano.azzolini@caffeina.com
 * @author gabriele.diener@caffeina.com
 * @version 1.0.1
 * @copyright Caffeina srl - 2014-2016 - http://caffeina.com
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
    $this->redis   = new \Predis\Client($this->options['scheme'].'://'.$this->options['host'].':'.$this->options['port'].'/', [
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
    return $expire >= 0 ? $this->redis->setEx($key,$expire,serialize($value)) : $this->redis->set($key,serialize($value));
  }

  public function delete($key){
  	$this->redis->delete($key);
  }

  public function exists($key){
    return $this->redis->exists($key);
  }

  public function flush(){
    return $this->redis->flushdb();
  }

  public function inc($key,$value=1){
  	return $this->redis->incrBy($key,$value);
  }

  public function dec($key,$value=1){
    return $this->redis->decrBy($key,$value);
  }
}
