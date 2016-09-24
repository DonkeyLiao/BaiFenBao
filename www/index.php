<?php
// +----------------------------------------------------------------------
// | Describe: 系统入口文件
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2014-02-07
// +----------------------------------------------------------------------

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

header('Content-Type: text/html; charset=utf-8'."\n");
require "./header.inc.php";
//require RUNTIME_PATH.'dhb.php'; //直接引用编译文件

//ini_set('session.save_handler', 'redis');
//ini_set('session.save_path', 'tcp://192.168.0.106:6380');
require PROJECT_ROOT_PATH.'ThinkPHP/ThinkPHP.php';