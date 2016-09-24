<?php
// +----------------------------------------------------------------------
// | Describe: 系统入口配置文件
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2015-02-07
// +----------------------------------------------------------------------
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
date_default_timezone_set('PRC');
define("SITE_ROOT_PATH", str_replace("\\","/",dirname(__FILE__)));	//系统主目录
define('OPEN_SSL', false); // 是否开启ssl访问，开启使用https，没开启使用http
$protocol = OPEN_SSL ? 'https://' : 'http://';
define("WEB_ROOT_URL", rtrim(trim($protocol.$_SERVER['HTTP_HOST'], '/'),'.'));
define('PROJECT_ROOT_PATH', dirname(SITE_ROOT_PATH).'/');			//项目目录
define('APP_PATH', PROJECT_ROOT_PATH.'Baifenbao/');				    // 应用目录
define('RUNTIME_PATH', PROJECT_ROOT_PATH.'Runtime/' );				//缓存目录
define('MOBILE_URL', 'http://mobile.newdhb.com/' );   //移动端URL
define('SMS_URL', 'http://u.newdhb.com/' );   //短信模板URL
define("PUBLIC_URL", 'http://public.newdhb.com/');   //JS、CSS URL
define("RESOURCE_URL", 'http://res.newdhb.com/');   //资源URL
define("RESOURCE_ROOT_PATH", PROJECT_ROOT_PATH.'www/Resource/');   //本地资源
define('AGENT_INVITE_URL', WEB_ROOT_URL.'/');//代理商账户推荐URL前缀
define('VERSION_NUMBER', '1.0.0');	 //版本号
define('SET_VERSION', '20160624'); // 更新缓存用
define('DEVELOPMENT_MODE', 'development');		//开发者提供的调试  开发模式 = development  线上模式 = online
define('MINIFY_ON', false);// 是否开启minify
define('VER_TYPE', 'online');  // online 在线版 standalone 独立版
define("APP_DEBUG", true);//调试模式开关