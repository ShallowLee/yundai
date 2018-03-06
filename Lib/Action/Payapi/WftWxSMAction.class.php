<?php
    class WftWxSMAction extends PayAction{
        
        public function Post(){

            $this->PayName = "WftWxSM";
            $this->TradeDate = date("YmdHis");
            $this->Paymoneyfen = 100;
            $this->check();
            $this->Orderadd();
                    
            $tjurl = "https://pay.swiftpass.cn/pay/gateway";
            $this->_Merchant_url= "http://".C("WEB_URL")."/Payapi_WftWxSM_MerChantUrl.html";    //通知地址
            $this->_Return_url= "http://".C("WEB_URL")."/Payapi_WftWxSM_ReturnUrl.html";        //返回地址
                    
            $Sjapi = M("Sjapi");
            $this->_MerchantID = $Sjapi->where("apiname='wftwxsm'")->getField("shid");   //通道商ID
            $this->_Md5Key = $Sjapi->where("apiname='wftwxsm'")->getField("key");        //通道商密钥   
            $zhanghu = $Sjapi->where("apiname='wftwxsm'")->getField("zhanghu");          //通道商账户
            $this->sjt_OrderMoney=floatval($this->OrderMoney)*floatval($this->Paymoneyfen);   //订单金额

			//die($this->sjt_OrderMoney);
                
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
			//die("string=".$string);
            $url ="http://".C("WEB_APIURL")."/wftwxsm/request.php?method=submitOrderInfo&".$string;
            echo "<script>location.href='".$url."'</script>";
			exit;
        }
      
		 //同步跳转接口 发送订单支付信息到商户
		 public function ReturnUrl(){
			$str = json_encode($_REQUEST);
			file_put_contents('Lib/Action/Payapi/wftwxsm.txt', "[".date("Y-m-d H:i:s")."] 威富通微信扫码 ReturnUrl: ".$str ."\r\n\r\n",FILE_APPEND);

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
			$MerchantID = $Sjapi->where("apiname='wftwxsm'")->getField("shid");
			$NewPostKey=md5($MerchantID."CNY".$noncestr);
			//验签
			if($PostKey == $NewPostKey) {
					if($status === "0") {
						file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 扫码显示获取成功: code_img_url=".$code_img_url." msg=".$msg." noncestr=".$noncestr." 验签成功 \r\n\r\n",FILE_APPEND);
						echo "<img src=".$code_img_url."><p>请使用微信扫描<br>二维码以完成支付</p>";
					} else {
						file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 扫码显示获取失败: status=".$status." msg=".$msg." noncestr=".$noncestr."\r\n\r\n",FILE_APPEND);
						echo "扫码显示获取失败，请返回重试";
					}
			 } else {
					file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 扫码显示: userid=".$MerchantID." noncestr=".$noncestr." NewPostKey=".$NewPostKey." sign=".$PostKey." 验签失败 \r\n\r\n",FILE_APPEND);
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

			file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 异步通知初始化: out_trade_no=".$out_trade_no." out_transaction_id=".$out_transaction_id." total_fee=".$total_fee." PostKey=".$PostKey." NewPostKey=".$NewPostKey."\r\n\r\n",FILE_APPEND);

			//验签
			if($PostKey == $NewPostKey) {
					if($pay_result === "0" and $result_code === "0") {
						file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 验签和支付成功 \r\n\r\n",FILE_APPEND);
						$this->TongdaoManage($out_trade_no,1);
					} else {
						file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 支付失败: pay_result=".$pay_result." result_code=".$result_code."\r\n\r\n",FILE_APPEND);
						//echo "支付失败: pay_result=".$pay_result."  out_trade_no=".$out_trade_no." result_code=".$result_code;
					}
			 } else {
					file_put_contents('Lib/Action/Payapi/wftwxsm.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$out_trade_no." 验签失败 \r\n\r\n",FILE_APPEND);
					//echo $out_trade_no."密匙验证失败";
			 }
		 }

		 //订单状态查询接口
		 public function MerChantUrl() {
			$TransID = $_REQUEST['out_trade_no'];
			$Order = M("Order");
			$Sjt_Zt = $Order->where("TransID = '".$TransID."'")->getField("Zt");
			if($Sjt_Zt==1) {
				echo 1;  
			} else {
				echo 2; 
			}
		 }

	 function randpw($len=8,$format='ALL'){
		$is_abc = $is_numer = 0;
		$password = $tmp ='';
		switch($format){
			case 'ALL':
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				break;
			case 'CHAR':
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				break;
			case 'NUMBER':
				$chars='0123456789';
				break;
			default :
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				break;
		}
		mt_srand((double)microtime()*1000000*getmypid());

		while(strlen($password)<$len){

			$tmp =substr($chars,(mt_rand()%strlen($chars)),1);
			if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
				$is_numer = 1;
			}
			if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
				$is_abc = 1;
			}
			$password.= $tmp;
		}

		if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
			$password = randpw($len,$format);
		}
		return $password;
	}

	public function checkstatus(){
		$orderid = $this->_request("orderid");

		$Order = M("Order");
		$find = $Order->where("TransID = '".$orderid."'")->find();
		if(!$find){
			$this->error('0','',true);
	//         $json = array(
	//             "status" => "error",
	//         );
				
		}else{
			if($find["Zt"] <> 0){
				$url=U('WftWxSM/hrefReturn');
				$this->success($url,'',true);
	//             $json = array(
	//                 "status" => "ok",
	//                 "url"    => $this->hqurl($find["id"])
	//             );
				
			}else{
				$this->error('0','',true);
	//            $json = array(
	//                 "status" => "error",
	//             ); 
				
			}
		}
		$this->error('0','',true);
	   // exit(json_encode($json));
	}

	public function hrefReturn(){
		$orderid=$this->_request('orderid');
		$this->TongdaoManage($orderid,0);
	}

	private function hqurl($id){
		$Order = M("Order");
		$tbhdurl = $Order->where("id=".$id)->getField("tbhdurl");
		//if(!$tbhdurl){
			//$this->hqurl($id);
		//}else{
			return $tbhdurl;
		//}
	}

}
?>
