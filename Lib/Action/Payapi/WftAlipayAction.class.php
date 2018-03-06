<?php
     class WftAlipayAction extends PayAction{
         public function Post($typego){
            $this->PayName = "wftalipay";
            $this->TradeDate = date("Y-m-d H:i:s");
            $this->Paymoneyfen = 100;
			$typego =  $_POST['typego'];
			if($typego=='pc')
			{
				 $this->bankname = "威富通支付宝扫码";
			}
			
            $this->check();
            $this->Orderadd();

            $Sjapi = M("Sjapi");
            $this->_MerchantID = $Sjapi->where("apiname='".$this->PayName."'")->getField("shid"); //商户ID
            $this->_Md5Key = $Sjapi->where("apiname='".$this->PayName."'")->getField("key"); //密钥
            $this->sjt_OrderMoney=floatval($this->OrderMoney)*floatval($this->Paymoneyfen);//订单金额

            $user_ip = "";
            if(isset($_SERVER['HTTP_CLIENT_IP'])) {
				$user_ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $user_ip = $_SERVER['REMOTE_ADDR'];
            }

			$Subject = $this->ProductName; //商户传过来的商品名称 

            $string = "out_trade_no=".$this->TransID."&total_fee=".$this->sjt_OrderMoney."&key=".$this->_Md5Key."&partner=".$this->_MerchantID."&body=".$Subject."&mch_create_ip=".$user_ip."&returnurl=".$this->Sjt_Return_url."&usertoken=".$this->usertoken;
            $url ="http://".C("WEB_APIURL")."/wftalipay/request.php?method=submitOrderInfo&".$string;
            echo "<script>location.href='".$url."'</script>";
			exit;
         }

		 //同步跳转接口 发送订单支付信息到商户
		 public function ReturnUrl(){
			$str = json_encode($_REQUEST);
			file_put_contents('Lib/Action/Payapi/wftalipay.txt',$str ."\r\n\r\n",FILE_APPEND);

			//接受消息
			if($_REQUEST['out_trade_no']) {
						//商户订单号
						$out_trade_no = $_REQUEST['out_trade_no'];
						//支付流水号
						$out_transaction_id = $_REQUEST['out_transaction_id'];

						if($_REQUEST['pay_result'] === "0" and $_REQUEST['result_code'] === "0") {
							//file_put_contents('Lib/Action/Payapi/chenggong.txt', $str ."\r\n",FILE_APPEND);
							$this->TongdaoManage($out_trade_no,0);
						} else {
							echo "支付失败: pay_result=".$_REQUEST['pay_result']."  pay_info=".$_REQUEST['pay_info']." result_code=".$_REQUEST['result_code'];
						}
			 } else {
						//未接收到消息
						var_dump($_REQUEST);
			 }
		 }

		 //扫码显示接口（无用）
		 public function PayUrl(){
			//接受消息
			$status = $_REQUEST['status'];
			$code_img_url = $_REQUEST['code_img_url'];
			$msg = urldecode($_REQUEST['msg']);
			$noncestr = $_REQUEST['noncestr'];
			$PostKey = $_REQUEST['sign'];

			$Sjapi = M("Sjapi");
			$MerchantID = $Sjapi->where("apiname='wftalipay'")->getField("shid");
			$NewPostKey=md5($MerchantID."CNY".$noncestr);
			//验签
			if($PostKey == $NewPostKey) {
					if($status === "0") {
						file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 扫码显示获取成功: code_img_url=".$code_img_url." msg=".$msg." noncestr=".$noncestr." 验签成功 \r\n\r\n",FILE_APPEND);
						echo "<img src=".$code_img_url."><p>请使用支付宝钱包扫描<br>二维码以完成支付</p>";
					} else {
						file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 扫码显示获取失败: status=".$status." msg=".$msg." noncestr=".$noncestr."\r\n\r\n",FILE_APPEND);
						echo "扫码显示获取失败，请返回重试";
					}
			 } else {
					file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 扫码显示: userid=".$MerchantID." noncestr=".$noncestr." NewPostKey=".$NewPostKey." sign=".$PostKey." 验签失败 \r\n\r\n",FILE_APPEND);
					echo "密匙验证失败";
			 }
		 }

		 //异步通知接口
		 public function NotifyUrl(){
			//接受消息
			$out_trade_no = $_POST['out_trade_no'];
			$out_transaction_id = $_POST['out_transaction_id'];
			$total_fee = $_POST['total_fee'];
			$result_code = $_POST['result_code'];
			$pay_result = $_POST['pay_result'];
			$PostKey = $_POST['PostKey'];
			//生成加密验签字符串
			$preEncodeStr=$out_trade_no.$total_fee.$out_transaction_id."CNY";
			$NewPostKey=md5($preEncodeStr);

			file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 异步通知初始化: out_trade_no=".$out_trade_no." out_transaction_id=".$out_transaction_id." total_fee=".$total_fee." PostKey=".$PostKey." NewPostKey=".$NewPostKey."\r\n\r\n",FILE_APPEND);

			//验签
			if($PostKey == $NewPostKey) {
					if($pay_result === "0" and $result_code === "0") {
						file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 验签和支付成功 \r\n\r\n",FILE_APPEND);
						$this->TongdaoManage($out_trade_no,1);
					} else {
						file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 支付失败: pay_result=".$pay_result." result_code=".$result_code."\r\n\r\n",FILE_APPEND);
						//echo "支付失败: pay_result=".$pay_result."  out_trade_no=".$out_trade_no." result_code=".$result_code;
					}
			 } else {
					file_put_contents('Lib/Action/Payapi/wftalipay.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 验签失败 \r\n\r\n",FILE_APPEND);
					//echo $out_trade_no."密匙验证失败";
			 }
		 }
		 
		 //订单状态查询接口  从native.php的ajax传递过来
		 public function MerChantUrl() {
			header("Access-Control-Allow-Origin: *");
			$TransID = $_REQUEST['out_trade_no'];
			$Order = M("Order");
			$Sjt_Zt = $Order->where("TransID = '".$TransID."'")->getField("Zt");
			if($Sjt_Zt==1) {
				echo 1;  
			} else {
				echo 2; 
			}
		 }
     }
?>