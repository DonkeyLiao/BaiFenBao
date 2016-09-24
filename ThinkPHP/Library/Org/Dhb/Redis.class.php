<?php
// +----------------------------------------------------------------------
// | Describe: Redis数据库配置
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2016-03-15
// +----------------------------------------------------------------------

namespace Org\Dhb;
defined('THINK_PATH') or exit();

/**
 * Redis数据驱动 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 * 示例：$objRedis = new \Org\Dhb\Redis(C('REDIS_LOGS_DB'));
 */
class Redis{
	 /**
	 * 架构函数
     * @param array $options 数据参数
     * @access public
     */
    public function __construct($options=array()) {
        if ( !extension_loaded('redis') ) {
            E(L('_NOT_SUPPORT_').':redis');
        }
        if(empty($options)){
            $options = C('REDIS_LOGS_DB');
        }
        $this->options = array (
                'host'          => $options['REDIS_HOST'],
                'port'          => $options['REDIS_PORT'],
                'timeout'     => $options['REDIS_TIMEOUT'],
                'auth'	         => $options['REDIS_AUTH'],
                'expire'       => $options['REDIS_LIFETIME'],
                'persistent'  => $options['REDIS_PERSISTENT'],
                'prefix'        => '',
        );
        
        $this->options['prefix']  =  '';         
        $func = $this->options['persistent'] ? 'pconnect' : 'connect';
        $this->handler  = new \Redis;
        $this->handler->$func($this->options['host'], $this->options['port'], $this->options['timeout']);

        //增加代码，设置redis安全性，增加认证密码
        if(isset($this->options['auth']) && $this->options['auth']){
            $this->handler->auth($this->options['auth']);
        }
        $db_index = REDIS_DB_INDEX;
        if($db_index){
            $this->handler->select($db_index);
        }
    }

    /**
     * 读取数据
     * @access public
     * @param string $name 数据变量名
     * @return mixed
     */
    public function get($name) {

        $value = $this->handler->get($this->options['prefix'].$name);
        $jsonData  = json_decode( $value, true );
        return ($jsonData === NULL) ? $value : $jsonData;	//检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    /**
     * 写入数据
     * @access public
     * @param string $name 数据变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null) {

        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        $name   =   $this->options['prefix'].$name;
        //对数组/对象数据进行数据处理，保证数据完整性
        $value  =  (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if(is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        }else{
            $result = $this->handler->set($name, $value);
        }

        return $result;
    }

    /**
     * 删除数据
     * @access public
     * @param string $name 数据变量名
     * @return boolean
     */
    public function rm($name) {
        return $this->handler->delete($this->options['prefix'].$name);
    }

    /**
     * 清除数据
     * @access public
     * @return boolean
     */
    public function clear() {
        return $this->handler->flushDB();
    }
    
    
    /**
     * 写入数据 List 类型
     * @access public
     * @param string $name 数据变量名
     * @param mixed $value  存储数据
     * @return boolean
     */
    public function push($name, $value) {
    
        $name   =   $this->options['prefix'].$name;
        //对数组/对象数据进行数据处理，保证数据完整性
        $value  =  (is_object($value) || is_array($value)) ? json_encode($value) : $value;

        $result = $this->handler->lPush($name, $value);
    
        return $result;
    }

}
