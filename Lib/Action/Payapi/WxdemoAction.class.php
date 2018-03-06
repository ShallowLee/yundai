<?php
     class WxdemoAction extends PayAction{
         public function Post($typego){
            $this->PayName = "wxdemo";
            $this->TradeDate = date("Y-m-d H:i:s");
            $this->Paymoneyfen = 1;
			 $typego =  $_POST['typego'];
			if($typego=='pc')
			{
				 $this->bankname = "微信扫码";
			}
			
            $this->check();
            $this->Orderadd();

            $Sjapi = M("Sjapi");
            $this->_MerchantID = $Sjapi->where("apiname='".$this->PayName."'")->getField("shid"); //商户ID
            $this->_Md5Key = $Sjapi->where("apiname='".$this->PayName."'")->getField("key"); //密钥   
            $zhanghu = $Sjapi->where("apiname='".$this->PayName."'")->getField("zhanghu");
            $this->sjt_OrderMoney=floatval($this->OrderMoney)*floatval($this->Paymoneyfen);//订单金额

            $string = "out_trade_no=".$this->TransID."&total_fee=".$this->sjt_OrderMoney."&key=".$this->_Md5Key."&partner=".$this->_MerchantID;
            $url ="http://".C("WEB_APIURL")."/api/wxpay/example/native.php?".$string;
            echo "<script>location.href='".$url."'</script>"; exit;
			//echo $url;
         }

		 //同步跳转接口 发送订单支付信息到商户
		 public function ReturnUrl(){
			$str = json_encode($_REQUEST);
			file_put_contents('Lib/Action/Payapi/wxdemo.txt',$str ."\r\n",FILE_APPEND);

			//接受消息
			if($_REQUEST['sp_billno']) {
						//商户订单号
						$out_trade_no = $_REQUEST['sp_billno'];
						//支付流水号
						$trade_no     = $_REQUEST['transaction_id'];
						//交易状态
						$trade_status = $_REQUEST['trade_status'];
						if($trade_status == "SUCCESS") {
							//file_put_contents('Lib/Action/Payapi/chenggong.txt', $str ."\r\n",FILE_APPEND);
							$this->TongdaoManage($out_trade_no,0);
						} else {
							echo "error: trade_status=".$trade_status;
						}
			 } else {
						//验证失败
						echo "ReturnUrl error: sp_billno=".$_REQUEST['sp_billno'];
			 }
		 }
		  
		 //订单状态查询接口  从native.php的ajax传递过来
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
     }
?>