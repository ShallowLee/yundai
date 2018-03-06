<?php
header("Content-type:text/html;charset=utf-8");
include_once("Config.php");

$agent = $_SERVER['HTTP_USER_AGENT'];//判断终端性质
    if(true == preg_match("/.+Windows.+/", $agent)){  
        $is_mobile = "2";
    }elseif(true == preg_match("/.+Macintosh.+/", $agent)){  
        $is_mobile = "2"; 
    }elseif(true == preg_match("/.+iPad.+/", $agent)){  
        $is_mobile = "2";  
    }elseif(true == preg_match("/.+iPhone.+/", $agent)){  
        $is_mobile = "3"; 
    }elseif(true == preg_match("/.+Android.+/", $agent)){  
        $is_mobile = "3"; 
    }else{  
        $is_mobile = "2"; 
    }

if ($_POST["amount"]) {
	$P_CardId="";                      //无用，不需更改
	$P_CardPass="";                    //无用，不需更改 
	$P_FaceValue=$_POST["amount"];     //付款金额，单位元
	$P_ChannelId="10000";              //渠道编号，不需更改 
	$P_Subject=urlencode($_POST["subject"]);      //商品标题，可自定义
	$P_Quantity=1;                     //购买数量，不需更改
	$P_Description=urlencode($_POST["description"]);    //商品描述，可自定义，比如用作用户名
	$P_Notic="";                       //无用，不需更改
	$P_Result_url=$result_url;         //Config里面的同步跳转地址
	$P_Notify_url=$notify_url;         //Config里面的异步通知地址
	$P_OrderId=getOrderId();           //生成订单号，可以自行更改，必须保持在商户系统内的唯一性
	$is_mobile=$_POST["is_mobile"];    //2为PC端扫码，3为移动WAP

	$preEncodeStr=$UserId."|".$P_OrderId."|".$P_CardId."|".$P_CardPass."|".$P_FaceValue."|".$P_ChannelId."|".$SalfStr;
	$P_PostKey=md5($preEncodeStr);

	$params="P_UserId=".$UserId;
	$params.="&P_OrderId=".$P_OrderId;
	$params.="&P_CardId=".$P_CardId;
	$params.="&P_CardPass=".$P_CardPass;
	$params.="&P_FaceValue=".$P_FaceValue;
	$params.="&P_ChannelId=".$P_ChannelId;
	$params.="&P_Subject=".$P_Subject;
	$params.="&P_Quantity=".$P_Quantity;
	$params.="&P_Description=".$P_Description;
	$params.="&P_Notic=".$P_Notic;
	$params.="&P_Result_url=".$P_Result_url;
	$params.="&P_Notify_url=".$P_Notify_url;
	$params.="&P_PostKey=".$P_PostKey;
	$params.="&is_mobile=".$is_mobile;

	//提交到API
	//header("location:$gateWary?$params");

	$ch=curl_init(); 
	curl_setopt($ch,CURLOPT_URL,$gateWary);
	//1=设置头文件的信息作为数据流输出
	curl_setopt($ch,CURLOPT_HEADER,0); 
	//1=设置获取的信息以文件流的形式返回，而不是直接输出
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,0); 
	//设置是post还是get方法
	curl_setopt($ch,CURLOPT_POST,1); 
	//传递变量
	curl_setopt($ch,CURLOPT_POSTFIELDS,$params); 
	$result = curl_exec($ch);
	curl_close($ch);
}

//生成订单号
function getOrderId()
{
	return rand(100000,999999)."".date("YmdHis");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>支付演示</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta name="apple-mobile-web-app-capable" content="yes"> 
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
</head>
<body>
<br/><br/>
<div style="text-align:center"><span style="font-size:24px">&nbsp;支付演示</span>
<br/><br/>
&nbsp;*这里可以自定义商户的提示信息*</div>
<br/><br/>
<center>
<form name="p" action="index.php" method="post">
<input type="hidden" name="is_mobile" value="<?php echo $is_mobile; ?>">
付款用途：<input type="text" name="subject" size="12" value="网络服务">&nbsp;*选填<br><br>
付款用户：<input type="text" name="description" size="12" value="user123">&nbsp;*选填<br><br>
付款金额：<input type="text" name="amount" value="0.03" size="15">&nbsp;元<br><br>
<input type="submit" value="点击支付">
</form>
</center>
</body>
</html>