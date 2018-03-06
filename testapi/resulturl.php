<?
header("Content-type:text/html;charset=utf-8");
include_once ("Config.php");

$status = $_REQUEST['status'];
$code_img_url = $_REQUEST['code_img_url'];
$msg = urldecode($_REQUEST['msg']);
$paymethod = $_REQUEST['paymethod'];
if( strstr($paymethod,"wx") !== false ) {
	$str1 = "wechat.png";
	$str3 = "请使用微信扫描<br>二维码完成支付";
} else if( strstr($paymethod,"alipay") !== false ) {
	$str1 = "alipay.png";
	$str3 = "请使用支付宝扫描<br>二维码完成支付";
} else if( strstr($paymethod,"qq") !== false ) {
	$str1 = "qqqianbao.png";
	$str3 = "请使用QQ手机版扫描<br>二维码完成支付";
} else {
	$str1 = "picerror.png";
	$str3 = "请返回重新支付";
}
$str2 = "<font color=#FFFFFF size=4><b>扫码显示获取失败，请返回重试</b></font>";

if ($_REQUEST['code_img_url']) {
		//如果返回code_img_url不为空，则正常显示二维码
		$nonce_str = $_REQUEST['nonce_str'];
		$usertoken = $_REQUEST['usertoken'];
		$postkey = $_REQUEST['postkey'];
		$NewPostKey=md5($usertoken."CNY".$nonce_str);

		//验签
		if($postkey == $NewPostKey) {
			if($status === "0") {
				file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] ".$paymethod."扫码显示获取成功: code_img_url=".$code_img_url." msg=".$msg." postkey=".$postkey." 验签成功 \r\n\r\n",FILE_APPEND);
				//如果是微信WAP支付成功后跳转过来，就显示pay_info里的URL
				if($paymethod == "wftwxwap") {
					header("Location:".base64_decode($code_img_url));
					exit;
				} else if($paymethod == "alipaywap") {
					echo "<p style=text-align:center;font-size:30px;color:#000;>支付完成，请返回用户中心查看</p>";
					exit;
				}
				$str2 = "<img src=".$code_img_url.">";
			} else {
				file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] ".$paymethod."扫码显示获取失败: status=".$status." msg=".$msg." paymethod=".$paymethod."\r\n\r\n",FILE_APPEND);
				$str2 = "<font color=#FFFFFF size=3><b>扫码显示获取失败，请返回重试</b></font>";
			}
		} else {
			file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] ".$paymethod."扫码显示获取失败: postkey=".$postkey." NewPostKey=".$NewPostKey." 验签失败 \r\n\r\n",FILE_APPEND);
			$str2 = "<font color=#FFFFFF size=3><b>扫码密匙验证失败</b></font>";
		}
//} else {
	//file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] ".$paymethod."扫码显示获取失败: status=".$status." msg=".$msg." paymethod=".$paymethod." \r\n\r\n",FILE_APPEND);
	//$str2 = "<font color=#FFFFFF size=4><b>扫码显示获取失败，请返回重试</b></font>";
}
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>收银台</title>
    <script src="/Public/js/jquery-1.7.2.js?v=1481609204"></script>
</head>
<style>
*{margin:0;padding:0;}
body{background:url(bg.jpg) repeat;}
#main{background-color:#fff;padding:1px;width:500px;margin:100px auto;text-align:center;border-radius:3px;box-shadow:5px 5px 30px #333;}
#content{padding:30px;}
#title{color:#333;font-size:14px;background-color:#e8e8e8;border-bottom:1px solid #ccc;line-height:60px;}
#title span{color:#fb180a;font-size:16px;font-weight:bold;}
#QRmsg{color:#149696;background-color:#e8e8e8;border-top:1px solid #ccc;line-height:28px;padding:20px 0;font-size:16px;}
.qr_default{background:url(icon_pay.png) no-repeat 150px -63px;}
.qr_succ, .pay_succ{background:url(icon_pay.png) no-repeat 150px -3px;}
.pay_error{background:url(icon_pay.png) no-repeat 150px -120px;}
#msgContent p{text-align:left;padding-left:220px;}
#msgContent p a{color:#149696;font-weight:bold;}
</style>
<body>
     <div id="main">
        <div id="title"><img src="<?php echo $str1;?>" style="width:178px;height:41px;"/></div>
        <div id="content">
             <div> <?php echo $str2;?> </div>
        </div>
        <div id="QRmsg"><div id="msgContent" class="qr_default"><p><?php echo $str3;?></p></div></div>
    </div>
</body>

<script>
var out_sn = '<?php echo $_REQUEST['out_trade_no'];?>';
 var f= setInterval("myInterval()",4000);  //1000为1秒钟  即4秒轮询一次后台看看支付成功了没
       var i =5;
       function myInterval()
       {

           <?php if($_SERVER['SERVER_NAME']=='yundai.itcitylife.com') {//测试网址
               $rul="http://yundai.itcitylife.com/Payapi_WftAlipay_MerChantUrl.html";
           }else{
               $rul="http://pay08.hzit.com/Payapi_WftAlipay_MerChantUrl.html";
           }?>

			 $.ajax({
			    url:'http://pay08.hzit.com/Payapi_WftAlipay_MerChantUrl.html',
			    type:'POST',   //不行就试试GET
			    async:true,    //是否异步
			    data:{
			        out_trade_no:out_sn,
					pp:22
			     },
			    timeout:8000,       //超时时间
			    dataType:'html',    //返回的数据格式：json/xml/html/script/jsonp/text
			    beforeSend:function(xhr){
			        console.log(xhr)
			        console.log('发送前')
			    },
			    success:function(res,textStatus,jqXHR){
						  if(res==1)
						  {
							  clearInterval(f);  //清除定时器 跳转到支付结果页面
							  location.href='success.php?out_trade_no=<?php echo $_REQUEST['out_trade_no'];?>&status=SUCCESS';
							  return false;
						  }else{
							   console.log('输出'+res)
						  }
			    },
			    error:function(xhr,textStatus){
			        console.log('错误')
			        console.log(xhr)
			        console.log(textStatus)
			    },
			    complete:function(){
			        console.log('结束')
			    }
		});
			 
       }
</script>
</html>