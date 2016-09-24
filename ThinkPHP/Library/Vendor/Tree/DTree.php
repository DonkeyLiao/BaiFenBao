<?php

/**
 * 获取数据树状数组
 * Class DTree
 */

class DTree {

    static $data = array();
    static $id = 'id';
    static $pid = 'pid';
    static $name = 'name';
    static $icon = array(' │',' ├',' └');
    static $result = array();


    /**
     * @param array $list 需要组织的二维数组
     * @param string $id id字段
     * @param string $pid 父id字段
     * @param string $name 显示名称
     * @return array
     * @throws Exception
     */
    static function getArray($list = array(),$id='id',$pid='pid',$name='name') {
        if(!function_exists('array_column')) {
            throw new Exception("需要array_column函数支持!");
        }
        $list = array_column($list ?: array() , null,$id);
        self::$result = array();
        self::$data = $list;
        self::$id = $id;
        self::$pid = $pid;
        self::$name = $name;
        return self::parse() ?: array();
    }

    static function parse($cid=0,$sid=0,$adds=''){
        $number=1;
        $child = self::getChild($cid);
        if(is_array($child)) {
            $total = count($child);
            foreach($child as $id=>$a) {
                $j=$k='';
                if($number == $total) {
                    $j .= self::$icon[2];
                } else {
                    $j .= self::$icon[1];
                    $k = $adds ? self::$icon[0] : '';
                }
                $spacer = $adds ? $adds.$j : '';
                @extract($a);
                $a[self::$name] = $spacer.' '.$a[self::$name];
                self::$result[$a[self::$id]] = $a;
                $fd = $adds.$k.'&nbsp;';
                self::parse($id, $sid, $fd);
                $number++;
            }
        }
        return self::$result;
    }

    /**
     * 获取子数据
     * @param $cid
     * @return array|bool
     */
    static function getChild($cid){
        $result = array();
        if(is_array(self::$data)) {
            foreach(self::$data as $id => $a) {
                if($a[self::$pid] == $cid) {
                    $result[$id] = $a;
                }
            }
        }
        return $result ? $result : false;
    }

}