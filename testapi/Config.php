<?php
error_reporting(0); 
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set(PRC);
//平台商户ID，需要更换成自己的商户ID
$UserId='10297';
//接口密钥，登录API平台，商户管理-->安全设置-->密钥设置，获取自己的密匙
$SalfStr='d98c8187ca79332bc504902ca47cd10f';
//平台网关地址

if($_SERVER['SERVER_NAME']=='yundai.itcitylife.com') {//测试网址
    $gateWary="http://yundai.itcitylife.com/Payapi_Index_Pay.html";
}else{
    $gateWary="http://pay08.hzit.com/Payapi_Index_Pay.html";
}



//异步通知地址

$notify_url = "http://".$_SERVER['HTTP_HOST']."/testapi/Notify_Url.php";
//显示二维码和付款结果的转向地址
$result_url = "http://".$_SERVER['HTTP_HOST']."/testapi/resulturl.php";
?>