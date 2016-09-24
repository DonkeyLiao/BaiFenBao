<?php
// +----------------------------------------------------------------------
// | Describe: API入口文件
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2015-10-26
// +----------------------------------------------------------------------

// 检测PHP环境

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

header('Content-Type: application/json; charset=utf-8'."\n"); 
header("Access-Control-Allow-Origin:*"); //跨域访问
require "./header.inc.php";

// 绑定访问DhbApi模块
define('BIND_MODULE','BfbApi');
if(!empty($_POST['c'])) $_GET['controller'] = trim($_POST['c']);
if(!empty($_POST['a'])) $_GET['action'] = trim($_POST['a']);

// define("APP_DEBUG", true);//调试模式开关
require PROJECT_ROOT_PATH.'ThinkPHP/ThinkPHP.php';