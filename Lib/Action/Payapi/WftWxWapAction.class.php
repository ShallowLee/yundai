<?php
class WftWxWapAction extends PayAction{
        
	public function Post(){
            $this->PayName = "WftWxWap";
            $this->TradeDate = date("YmdHis");
            $this->Paymoneyfen = 100;

            $this->check();
            $this->Orderadd();

            $this->sjt_OrderMoney=floatval($this->OrderMoney)*floatval($this->Paymoneyfen);        //订单金额
            $tjurl = "https://pay.swiftpass.cn/pay/gateway";
            $this->_Merchant_url= "http://".C("WEB_URL")."/Payapi_WftWxWap_NotifyUrl.html";      //商户通知地址
            $this->_Return_url= "http://".C("WEB_URL")."/Payapi_WftWxWap_ReturnUrl.html";          //用户跳转地址
                
            $user_ip = "";
            if(isset($_SERVER['HTTP_CLIENT_IP'])) {
				$user_ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $user_ip = $_SERVER['REMOTE_ADDR'];
            }

			$Subject = $this->ProductName; //商户传过来的商品名称 

            header("Location:http://".C("WEB_URL")."/wftwxwap/request.php?method=submitOrderInfo&out_trade_no=".$this->TransID."&total_fee=".$this->sjt_OrderMoney."&body=".$Subject."&mch_create_ip=".$user_ip."&returnurl=".$this->Sjt_Return_url."&usertoken=".$this->usertoken);
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

			file_put_contents('Lib/Action/Payapi/wftwxwap.txt',"[".date("Y-m-d H:i:s")."] 异步通知初始化: out_trade_no=".$out_trade_no." out_transaction_id=".$out_transaction_id." total_fee=".$total_fee." PostKey=".$PostKey." NewPostKey=".$NewPostKey."\r\n\r\n",FILE_APPEND);

			//验签
			if($PostKey == $NewPostKey) {
					if($pay_result === "0" and $result_code === "0") {
						file_put_contents('Lib/Action/Payapi/wftwxwap.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 验签和支付成功 \r\n\r\n",FILE_APPEND);
						$this->TongdaoManage($out_trade_no,2);
					} else {
						file_put_contents('Lib/Action/Payapi/wftwxwap.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 支付失败: pay_result=".$pay_result." result_code=".$result_code."\r\n\r\n",FILE_APPEND);
						//echo "支付失败: pay_result=".$pay_result."  out_trade_no=".$out_trade_no." result_code=".$result_code;
					}
			 } else {
					file_put_contents('Lib/Action/Payapi/wftwxwap.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 验签失败 \r\n\r\n",FILE_APPEND);
					//echo $out_trade_no."密匙验证失败";
			 }
	}

    //同步跳转
	public function ReturnUrl(){
		$str = json_encode($_REQUEST);
		file_put_contents('Lib/Action/Payapi/wftwxwap.txt', "[".date("Y-m-d H:i:s")."] 微信WAP2 ReturnUrl: ".$str ."\r\n\r\n",FILE_APPEND);

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

	public function MerChantUrl(){
		$request=file_get_contents('php://input');
           parse_str($request,$request_form);
		   $out_trade_no=$request_form['mhtOrderNo'];
		   $this->TongdaoManage($out_trade_no,0);
		   file_put_contents('Lib/Action/Payapi/wftwxwap.txt', "MerChantUrl: ".$request_form .$xml."\r\n",FILE_APPEND);
           require_once 'services/Services.php';
			if (Services::verifySignature($request_form)){
				$tradeStatus=$request_form['tradeStatus'];
				echo "success=Y";
				if($tradeStatus!=""&&$tradeStatus=="A001"){      
					$this->TongdaoManage($out_trade_no,0);
					exit;
				}
            }
			echo "验签失败";	       	
	}

}
?>