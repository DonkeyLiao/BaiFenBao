<?php
namespace Org\Dhb;
/**
 * RedisOperate.php
 *
 * 单例模式设计Redis操作类
 *
 */
class RedisOperate{
    
    // 实例
    static private $_instance = null;

    private function __construct(){
    }
     
    //创建__clone方法防止对象被复制克隆
    public function __clone(){
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

    /**
     * Singleton instance（获取自己的实例）
     */
    static public function getInstance(){
        if(self::$_instance === null) {
            $redis = new \Redis();

            $host = C('REDIS_HOST');
            $port = C('REDIS_PORT');
            $auth = C('REDIS_AUTH');

            $redis->connect($host, $port);
            $redis->auth($auth);
			$db_index = REDIS_DB_INDEX;
	        if($db_index){
	            $redis->select($db_index);
	        }
            self::$_instance = $redis;
        }
        
        return self::$_instance;
    }
}