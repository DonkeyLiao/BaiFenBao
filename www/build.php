<?php
// +----------------------------------------------------------------------
// | Describe: 应用入口文件
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2014-02-07
// +----------------------------------------------------------------------

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

header('Content-Type: text/html; charset=utf-8'."\n");
require "./header.inc.php";

define('BUILD_LITE_FILE',true); //生成Lite文件
//define("APP_DEBUG", true);//调试模式开关
require PROJECT_ROOT_PATH.'ThinkPHP/ThinkPHP.php';