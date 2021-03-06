<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<title>钱包网上支付平台</title>
</head>
<?php
include 'config.php';
include 'pay.php';
?>

<body>
	<form action="request.php" method="POST">
		<table>
			<tr>
				<td>电商订单号</td>
				<td><input type="text" id="dsorderid" name="dsorderid" value="<?php echo rand();?>" /></td>
				<td>订单号，保证对应电商一笔订单</td>
			</tr>
			<tr>
				<td>商户名称</td>
				<td><input type="text" id="merchname" name="merchname" value="杭州云代科技有限公司" /></td>
				<td>电商名称Encode(“utf-16BE”),进行十六进制编码转换后再传参数</td>
			</tr>
			<tr>
				<td>购买时间</td>
				<td><input type="text" id="buytime" name="buytime" value="<?php echo date('Y年m月d日',time()); ?>" /></td>
				<td>yyyy年mm月dd日Encode(“utf-16BE”)进行十六进制编码转换后再传参数</td>
			</tr>
			<tr>
				<td>钱包ID</td>
				<td><input type="text" id="mediumno" name="mediumno" value="0100850355885900" /></td>
				<td>电商钱包ID</td>
			</tr>
			<tr>
				<td>币种</td>
				<td><input type="text" id="currency" name="currency" value="CNY" /></td>
				<td>币种</td>
			</tr>
			<tr>
				<td>交易总金额</td>
				<td><input type="text" id="amount" name="amount" value="0.02" /></td>
				<td></td>
			</tr>
			<tr>
				<td>通知地址</td>
				<td><input type="text" id="dsyburl" name="dsyburl" value="http://pay08.hzit.com/mfb/verify.php" /></td>
				<td>电商异步通知地址，该地址用于接收支付结果或商户授权结果，具体参数见下表5.2通知参数</td>
			</tr>
			<tr>
				<td>页面跳转地址</td>
				<td><input type="text" id="dstburl" name="dstburl" value="http://pay08.hzit.com/mfb/" /></td>
				<td>电商同步通知，用于支付成功之后立即发起的页面跳转</td>
			</tr>
			<tr>
				<td>授权码返回地址</td>
				<td><input type="text" id="authnourl" name="authnourl" value="http://pay08.hzit.com/mfb/" /></td>
				<td>电商异步通知地址，该地址用于返回给电商用户的登录授权码</td>
			</tr>
			<tr>
				<td>商品名称</td>
				<td><input type="text" id="product" name="product" value="测试商品11" /></td>
				<td>产品名称Encode(“utf-16BE”) 进行十六进制编码转换后再传参数</td>
			</tr>
			<tr>
				<td>商品描述</td>
				<td><input type="text" id="productdesc" name="productdesc" value="正在进行测试的商品" /></td>
				<td>Encode(“utf-16BE”)进行十六进制编码转换后再传参数</td>
			</tr>
			<tr>
				<td>电商平台的用户id</td>
				<td><input type="text" id="dsuserno" name="dsuserno" value="50001" /></td>
				<td>所接入的电商平台自己的用户id</td>
			</tr>
			 
			<tr>
				<td><input type="submit"></td>
			</tr>
		</table>
	</form>
</body>