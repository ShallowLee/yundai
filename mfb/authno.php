<?php
/**
 * 授权码通知信息处理
 *
 */
include 'config.php';
include 'pay.php';
header ( "content-type:text/html;charset=utf-8" );

$pay=new Pay();

$return_data = $_POST;
if($pay->verify_sign(KEY,$return_data, $return_data['dstbdatasign'])){
	/**
	* 此处根据通知返回的dstbdata参数中的returncode的值判断是否成功获取授权码，然后进行相应的操作。
	*/
	echo "校验成功";
}else{
	echo "校验失败，此信息非法，请勿做任何操作";
	//echo "99";
}