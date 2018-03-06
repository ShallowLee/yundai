<?
header("Content-type:text/html;charset=utf-8");

$status = $_REQUEST['status'];
$out_trade_no = $_REQUEST['out_trade_no'];
if($status == "SUCCESS") {
	file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] 支付成功 out_trade_no=".$out_trade_no. " status=".$status. "\r\n\r\n",FILE_APPEND);
	$str1 = "支付成功！";
	$str2 = "请关闭本页面后返回用户中心查看！";
} else {
	file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] 支付失败 out_trade_no=".$out_trade_no. " status=".$status. "\r\n\r\n",FILE_APPEND);
	$str1 = "支付失败！";
	$str2 = "请关闭本页面后返回重试！";
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<title>支付完成</title>
<style type="text/css">
#successtab{
	width:500px;
	height:auto;
	border:1px solid #063; 
	}
#successtab tr td{
	width:500px;
	height:30px;
	text-align:center;
	vertical-align:middle;
	}	
</style>
</head>

<body>
<br>
<div class="cz_div" style="height:auto;">
<div style="width:500px; margin:0px auto; margin-top:50px; height:auto;">
	<table border="0" id="successtab" cellpadding="0" cellspacing="0">
		<tr>
			<td style="color:#F60; font-size:50px;"><?php echo $str1;?></td>
		</tr>
		<tr>
			<td style="text-align:center;height:30px;">&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:center;height:30px;">订单号：<span style="color:#39F"><?php echo $out_trade_no;?></span></td>
		</tr>
		<tr>
			<td style="text-align:center;color:#FF0000;height:30px;"><?php echo $str2;?></td>
		</tr>
		<tr>
			<td style="text-align:center;height:30px;">&nbsp;</td>
		</tr>
		<tr>
			<td style="text-align:center;height:30px;"><input type="button" name="close" value="关闭" onclick="window.opener=null;window.open('','_self');window.close();" /></td>
		</tr>
	</table>
</div>
</div>
</body>
</html>
