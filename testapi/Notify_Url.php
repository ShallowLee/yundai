<?
header("Content-type:text/html;charset=utf-8");
include_once ("Config.php");
$OrderId = $_REQUEST["P_OrderId"];       //商户订单号
$FaceValue = $_REQUEST["P_FaceValue"];   //订单金额
$Subject = $_REQUEST["P_Subject"];       //商品名称
$SuccTime = $_REQUEST["P_SuccTime"];     //成功时间
$PostKey = $_REQUEST["P_PostKey"];       //传过来的加密验证字符串
$Bankname = $_REQUEST["P_Bankname"];     //支付方式名称

//组装用来验证加密的字符串
$NewPostKey = md5($UserId . $Subject . $OrderId . $FaceValue . $SuccTime . "CNY" . $SalfStr);

if ($PostKey == $NewPostKey) {
	//验签成功，在此处做商户系统自身所需的处理
    file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] 异步通知成功 OrderId=".$OrderId. " FaceValue=".$FaceValue. " Subject=".$Subject. " SuccTime=".$SuccTime. " PostKey=".$PostKey. " Bankname=".$Bankname. "\r\n\r\n",FILE_APPEND);
} else {
    file_put_contents('neworder.txt',"[".date("Y-m-d H:i:s")."] 异步通知失败 OrderId=".$OrderId. " PostKey=".$PostKey. " NewPostKey=".$NewPostKey. "\r\n\r\n",FILE_APPEND);
}
?>