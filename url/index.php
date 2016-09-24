<?php 
		require('../www/header.inc.php');
    	$currentUri = $_SERVER['REQUEST_URI'];
    	$arr = explode('/', $currentUri);
    	$tel = $mobileUrl = '';    	
    	$tel = $arr[count($arr) - 1];
		$mobileUrl = MOBILE_URL.'html/common/sms_active.html?'.$tel;
		Header("Location:$mobileUrl"); 
?>