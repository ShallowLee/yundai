<?php
     class PayAction extends Action{
         
         protected $UserID;  //商户ID号
         protected $TransID;  //商户订单号
         protected $TradeDate;  //订单生成时间
         protected $OrderMoney;  //订单金额
         protected $ProductName;  //商品名称
         protected $Amount;  //购买数量
         protected $Username;  //支付用户名
         protected $Email;  //电子邮件
         protected $Mobile;  //手机
         protected $AdditionalInfo;  //备注
         protected $Zt;  //支付状态
         protected $Sjt_Merchant_url; //异步通知地址
         protected $Sjt_Return_url;   //同步跳转地址
         protected $typepay;     //类型
         protected $CardNO;      //充值卡号
         protected $CardPWD;     //充值卡密码
         protected $Sjt_PayID;   //支付渠道
         protected $Sjt_Return = 1;   //返回状态 1为成功，0为失败 
         protected $Sjt_Error = "01";   //错误编号
         protected $PayName;      //接口名称
         protected $_MerchantID;  //接口商户ID
         protected $_Md5Key;      //接口密钥
         protected $_Merchant_url;  //接口前台通知地址
         protected $_Return_url;    //接口后台通知地址
         protected $Paymoneyfen;    //接口金额是分还是元
         protected $sjt_OrderMoney;  //接口提交金额
         protected $bankname;      //银行名称
         
         //验证数据
		 protected function check(){
//           echo "<pre>";
//           print_r($_GET);
             header("Content-Type:text/html; charset=utf-8");
             
             //异步通知地址
			 $this->Sjt_Merchant_url = $this->_request("Sjt_Merchant_url");
             if($this->Sjt_Merchant_url == NULL || $this->Sjt_Merchant_url == ""){
                 $this->Sjt_Merchant_url = $this->_request("pr_NeedResponse");
             }
             
             //同步跳转地址
			 $this->Sjt_Return_url = $this->_request("p8_Url");
             if($this->Sjt_Return_url == NULL || $this->Sjt_Return_url == ""){
                 $this->Sjt_Return = 0;
                 $this->Sjt_Error = 1;  //商户前台通知地址不能为空
                 //$this->RunError();
                 die('商户跳转地址不能为空');
             }
             
             //默认情况下两个地址的参数
             if($this->_request("fhlx") == "huaqi"){
                 $this->Sjt_Return_url = $this->_request("p8_Url");
                 $this->Sjt_Merchant_url = $this->_request("pr_NeedResponse");
             }
             
             //获取盛捷通商户号
			 $Sjt_MerchantID = $this->_request("Sjt_MerchantID");   
             if($Sjt_MerchantID == NULL || $Sjt_MerchantID == ""){
                 $Sjt_MerchantID = $this->_request("p1_MerId");
             }
			 //判断盛捷通商户号是否存在
             if($Sjt_MerchantID == NULL || $Sjt_MerchantID == ""){  
                 $this->Sjt_Return = 0;
                 $this->Sjt_Error = 2;  //商户号不能为空
                 //$this->RunError();
                 die('商户号不能为空');
             }else{
                 $this->UserID = intval($Sjt_MerchantID)-10000;  //获取到的商户号减掉10000就是实际数据库ID
                 $User = M("User");
                 if(!$User->where("id=".$this->UserID)->count()){
                     $this->Sjt_Return = 0;
                     $this->Sjt_Error = 3;  //商户号不存在
                     //$this->RunError();
                     die('商户号不存在');
                 }
             }
             
             //获取商户提交的支付渠道ID
             $this->Sjt_PayID = $this->_request("Sjt_PayID");   
             if($this->Sjt_PayID == NUll || $this->Sjt_PayID == ""){
				//如果没有渠道ID就获取IndexAction里CompatibleApi兼容API方法里的pd_FrpId的值 默认为招商银行zsyh
				$this->Sjt_PayID = $this->_request("pd_FrpId");
             }
             $User = M("User");
             $tongdao = $User->where("id=".$this->UserID)->getField("tongdao");
             $is_mobile=is_mobile_request();
             if($is_mobile==0){
				$zfid=$this->_request("PayBank");
				$td = explode("|",$tongdao);
				if(!in_array($zfid,$td)){
					die('支付渠道未授权');
				}
			 }else{
				if($this->_request("PayBank")==21){
					$zfid=12;
				}else{
					$zfid=$this->_request("PayBank");
				} 
				$td = explode("|",$tongdao);
				if(!in_array($zfid,$td)){
					die('支付渠道未授权');
				}  
			 }
             
             //判断商户提交的支付渠道字段是否存在
			 if($this->Sjt_PayID == NUll || $this->Sjt_PayID == ""){
				$this->Sjt_Return = 0;
				$this->Sjt_Error = 4;
				//$this->RunError();
                die('支付渠道不能为空');
             }else{
                $paytype = $this->_request("Sjt_Paytype");
                if($paytype == "" || $paytype == NULL){
                      $this->Sjt_Return = 0;
                      $this->Sjt_Error = 11;
                      //$this->RunError();
                      die('支付类型错误 ');
                }
                 
                 if($paytype == "b"){
                     //$Bankpay = M("Bankpay");
                     //$this->Sjt_PayID = $Bankpay->where("Sjt=".$this->Sjt_PayID)->getField($this->PayName);    
                     //if(!$this->Sjt_PayID  ){
                     //   $this->Sjt_Return = 0;
                     //   $this->Sjt_Error = 5;
                        //$this->RunError();
                     //  die('支付渠道不存在1');
                     //} 
                 }else{
                     if($paytype == "g"){
                         
                        $Gamepay = M("Gamepay");
                        $this->Sjt_PayID = $Gamepay->where("id=".$this->Sjt_PayID)->getField($this->PayName);
                        if(!$this->Sjt_PayID){
                            $this->Sjt_Return = 0;
                            $this->Sjt_Error = 11;   // 支付类型错误
                            //$this->RunError();
                            die(' 支付类型错误');
                        }  
                         
                     }else{
                         
                            $this->Sjt_Return = 0;
                            $this->Sjt_Error = 5;   // 支付渠道不存在
                            //$this->RunError();
                            die('支付渠道不存在2');
                     }
                 }
              }
              
              //获取盛捷通商户提交的订单金额
			  $this->OrderMoney = $this->_request("Sjt_OrderMoney");
              if($this->OrderMoney == NUll || $this->OrderMoney == ""){
                  $this->OrderMoney = $this->_request("p3_Amt");
              }
              
              //判断盛捷通商户提交的订单金额字段是否存在
			  if($this->OrderMoney == NUll || $this->OrderMoney == ""){
                    $this->Sjt_Return = 0;
                    $this->Sjt_Error = 6;   //订单金额不能为空
                    $this->RunError();
              }else{
                  if(!is_numeric($this->OrderMoney)){
                       $this->Sjt_Return = 0;
                       $this->Sjt_Error = 7;   //订单金额为不是数字
                       $this->RunError();
                  }
              }

              $Userapiinformation = D("Userapiinformation");
              $WebsiteUrl = $Userapiinformation->where("UserID=".$this->UserID)->getField("WebsiteUrl");   //获取用户设置的网址
              $yuming = $_SERVER["HTTP_REFERER"];       //获取提交的网址的域名
              $Sjt_Key = $Userapiinformation->where("UserID=".$this->UserID)->getField("Key");   //获取用户的密钥
        
			  $p0_Cmd = $_REQUEST["p0_Cmd"];
			  $p1_MerId = $_REQUEST["p1_MerId"];
			  $p2_Order = $_REQUEST["p2_Order"];
			  $p3_Amt = $_REQUEST["p3_Amt"];
			  $p4_Cur = $_REQUEST["p4_Cur"];
			  $p5_Pid = $_REQUEST["p5_Pid"];
			  $p6_Pcat = $_REQUEST["p6_Pcat"];
			  $p7_Pdesc = $_REQUEST["p7_Pdesc"];
			  $p8_Url = $_REQUEST["p8_Url"];
			  $p9_SAF = $_REQUEST["p9_SAF"];
			  $pa_MP = $_REQUEST["pa_MP"];
			  $pd_FrpId = $_REQUEST["pd_FrpId"];
			  $pr_NeedResponse = $_REQUEST["pr_NeedResponse"];
			  $hmacstr = $p0_Cmd.$p1_MerId.$p2_Order.$p3_Amt.$p4_Cur.$p5_Pid.$p6_Pcat.$p7_Pdesc.$p8_Url.$p9_SAF.$pa_MP.$pd_FrpId.$pr_NeedResponse.$Sjt_Key;
			  $hmac = MD5($hmacstr);
        
            //商户提交的密钥是否正确
			if($hmac != strtolower($this->_request("hmac"))){
                  if(strstr($yuming,C("WEB_URL_CZ")) == false){
                       $this->Sjt_Return = 0;
                       $this->Sjt_Error = "9";   //密钥错误 
                       $this->RunError();
                  }
			}
               
            //仅用于点卡充值交易
			$paytype = $this->_request("Sjt_Paytype");
			if($paytype == "g"){
				$this->CardNO = $this->_request("Sjt_CardNumber");  //卡号
                $this->CardPWD = $this->_request("Sjt_CardPassword");  //密码
             
				if($this->CardNO == Null || $this->CardNO == "" || $this->CardPWD == NULL || $this->CardPWD == ""){
					$this->Sjt_Return = 0;
					$this->Sjt_Error = 12;   //卡号或密码错误 
					$this->ReturnUrl();
				}
			}
         }
         
         //添加订单
         protected function Orderadd() {
			 //支付通道名称一律转成小写，否则在Sjapi表里找不到对应的记录
			 $payName = strtolower($this->PayName);

			 $p2_Order = $this->_request("p2_Order");
			 $Order = M("Order");
             if($p2_Order != NULL && $p2_Order != "") {
				$OrderTransID = $Order->where("TransID = '".$p2_Order."'")->count();
                if($OrderTransID > 0){
					$this->Sjt_Return = 0;
                    $this->Sjt_Error = 13;   //订单号已存在 
                    $this->ReturnUrl();
                }else{
                    $this->TransID = $p2_Order; 
                }
			 }else{
                $id_id = $Order->order("id desc")->limit(1)->getField("id");
                 
                if($this->PayName == "Yinlian"){
					$this->TransID = date('YmdHis') . strval(mt_rand(100, 999));  //银联流水号
                }else{
                    $this->TransID = $this->Sjt_MerchantID.date("Ymd").(1000000000+$id_id);  //其他流水号 
                }
				$Sjapi = M("Sjapi");
				$this->_MerchantID = $Sjapi->where("apiname='".$payName."'")->getField("shid"); //商户ID
				$this->TransID = $this->_MerchantID.$this->TransID;
             }

            $this->ProductName=urldecode($this->_request("p5_Pid"));        //商品名称
            $this->AdditionalInfo=urldecode($this->_request("p7_Pdesc"));   //商品描述，附加信息
            //this->Username = iconv('GB2312', 'UTF-8', $this->_request("Sjt_UserName"));
			$this->Username = urldecode($this->_request("Sjt_UserName"));
			//die("ProductName=".$this->ProductName." AdditionalInfo=".$this->AdditionalInfo." Username=".$this->Username);

            $Order = D("Order");
            $data["UserID"] = $this->UserID;       //商户编号
            $data["TransID"] = $this->TransID;     //订单号
            $data["TradeDate"] =$this->TradeDate;  //订单时间
              
            $Paycost = M("Paycost");
            $fv = $Paycost->where("UserID=".$this->UserID)->getField("wy");
            if($fv == 0){
				$fv = $Paycost->where("UserID=0")->getField("wy");
            }
            //$tongdao = $User->where("id=".$this->UserID)->getField("tongdao");
            $is_mobile=is_mobile_request();
			if($is_mobile==0){
				$zfid=$this->_request("PayBank");
			}else{
				if($this->_request("PayBank")==21){
					$zfid=12;
				}else if($this->_request("PayBank")==2){
					//如果是移动端打开的支付宝，则需调用alipaywap的id=1
					$zfid=1;
				}else{
					$zfid=$this->_request("PayBank");
				} 
			}

            //获取费率字段fl计算出支付到账的实际金额
			$sjapi = M("sjapi");
            $jinfl = $sjapi->where("id=".$zfid)->getField("fl");
            $jiaoyijine = $this->OrderMoney * $jinfl;            //商户实际到账金额 = 用户支付金额乘以商户费率
			if($jiaoyijine > $this->OrderMoney){
				exit("交易金额出错：实际到账金额不能大于支付金额");
			}
              
              $data["OrderMoney"] = $jiaoyijine;                 //商户实际到账金额
              $data["trademoney"] = $this->OrderMoney;           //用户支付金额
              $data["sxfmoney"] = $this->OrderMoney - $jiaoyijine;   //商户手续费 = 用户支付金额 - 商户实际到账金额
              $data["ProductName"] = $this->ProductName;         //商品名称
              $data["Username"] = $this->Username;               //某些业务用的附加信息字段
              $data["AdditionalInfo"] = $this->AdditionalInfo;   //商品描述，附加信息
              $data["Sjt_Merchant_url"] = $this->Sjt_Merchant_url;   //商户通知地址
              $data["Sjt_Return_url"] = $this->Sjt_Return_url;       //用户跳转地址
              
              //$tjurl = $_SERVER["HTTP_REFERER"];
			  $tjurl = $this->_request("tjurl");
              if($tjurl == NULL || $tjurl == ""){
                  $data["tjurl"] = "http://".C("WEB_URL");
              }
                
			  switch($payName){
                case "alipay":
                  $is_mobile=is_mobile_request();
                  if($is_mobile==0){
					$data["tongdao"] = "支付宝扫码";
					$data['payname'] = "alipay";
			      }else{
					$data["tongdao"] = "支付宝WAP";   //如果是移动端打开的支付宝，则需调用alipaywap
					$data['payname'] = "alipaywap";
				  }
                  break;
                case "alipaywap":
                  $data["tongdao"] = "支付宝WAP";
				  $data['payname'] = "alipaywap";
                  break;
                case "qqbao":
                  $data["tongdao"] = "QQ钱包";
				  $data['payname'] = "qqbao";
                  break;
				case "wxdemo":
                  $data["tongdao"] = "微信扫码";
                  $data['payname'] = "wxdemo";
				  break;	
                case "huanxunips":
                  $data["tongdao"] = "环迅IPS";
                  break;
				case "yibao":
                  $data["tongdao"] = "易宝";
                  break;
                case "lianlian":
                  $data["tongdao"] = "连连支付";
                  break;
                case "wftalipay":
                  $data["tongdao"] = "威富通支付宝扫码";
                  $data['payname'] = "wftalipay";
				  break;
                case "wftwxsm":
                  $data["tongdao"] = "威富通微信扫码";
                  $data['payname'] = "wftwxsm";
				  break;
                case "wftalipaywap":
                  $data["tongdao"] = "支付宝WAP2";
                  $data['payname'] = "wftalipaywap";
				  break;
                case "wftwxwap":
                  $data["tongdao"] = "微信WAP2";
				  $data['payname'] = "wftwxwap";
                  break;
				case "wxwap":
                  $data["tongdao"] = "微信原生WAP";
				  $data['payname'] = "wxwap";
                  break;
                case "tengfutong":
                  $data["tongdao"] = "腾付通";
                  $data["TransID"] = substr($this->TransID,6,20);        //订单号
                  $this->TransID = substr($this->TransID,6,20);          //订单号
                  break;
			}
              
            $paytype = $this->_request("Sjt_Paytype");
            if($paytype == "b"){
				$bankid =  $this->_request("pd_FrpId");
				$Bankpay = M("Bankpay");
				$BankName = $Bankpay->where("Sjt='".$bankid."'")->getField("BankName");
                $data["bankname"] = $BankName;
            }
             
			//判断是否是以下我们的主流支付通道
			if($payName=='alipay' || $payName=='alipaywap' || $payName=='wxwap' || $payName=='qqbao' || $payName=='wxdemo' || $payName=='wftalipay' || $payName=='wftwxsm' || $payName=='wftalipaywap' || $payName=='wftwxwap'){
				$data['bankname'] = $data['tongdao'];
            }

             if(strstr($yuming,C("WEB_URL")) == true){
                 $data["typepay"] = 1;
             }
              
			 //特地业务预留，无用
             if($_REQUEST["p6_Pcat"] == "703AC229E8E18062F3B474654E9D476C"){
                 $data["typepay"] = 3;
             }

             if($paytype == "g"){
                  $data["CardNO"] = $this->CardNO;
                  $data["CardPWD"] = $this->CardPWD;
                  if($_REQUEST["p6_Pcat"] == "703AC229E8E18062F3B474654E9D476C"){
                        $data["typepay"] = 4;
                  }else{
                       $data["typepay"] = 2;
                  }
                  
                  $payid =  $this->_request("pd_FrpId");
                  $Gamepay = M("Gamepay");
                  $GameName  = $Gamepay->where("id=".$payid)->getField("GameName");
                  $data["payname"] = $GameName;
               }
              
            $data["fhlx"] = $this->_request("fhlx");
			//var_dump($data);  
			//exit;
              
            if (!$Order->add($data)){
                exit("保存新订单失败！请返回订单发起页重新支付");
            }

			$this->usertoken = $this->_request("PostKey");
         }
         
        //返回错误
		protected function RunError(){    
             die('error');
             if($this->Sjt_Merchant_url == "" || $this->Sjt_Merchant_url == null){
                echo $this->Sjt_Error;
             }else{
                echo "<form id=\"Form1\" name=\"Form1\" method=\"post\" action=\"".$this->Sjt_Merchant_url."\">";
                echo "<input type=\"hidden\" name=\"Sjt_Return\" value=\"".$this->Sjt_Return."\">";
                echo "<input type=\"hidden\" name=\"Sjt_Error\" value=\"".$this->Sjt_Error."\">";
                echo "<input type='hidden' name='Sjt_BType' value='1'>";
                echo "</from>";
                echo "<script type=\"text/javascript\">";
                echo "document.Form1.submit();";
                echo "</script>";
             }
             exit;
		}
        
        protected function Echots(){
            echo "<script type='text/javascript'>";
            echo "var i = 0;";
            echo "dsjid = window.setInterval('djs()',1000);";
            echo "function djs(){";
            echo "    if(i <= 0){";
            echo "        window.clearInterval(dsjid);";
            echo "        document.Form1.submit();";
            echo "    }else{";
            echo "        i = i - 1;";
            echo "    }";
            echo "}";
            echo "</script>";
            exit;
        }
        
        protected function dkname($zwm){
            $ywm = "";
            switch($zwm){
                case "天宏一卡通":
                $ywm = "thykt";
                break;
                
                case "完美一卡通":
                $ywm = "wmykt";
                break;
                
                case "网易一卡通":
                $ywm = "wyykt";
                break;
                
                case "联通充值卡":
                $ywm = "ltczk";
                break;
                
                case "久游一卡通":
                $ywm = "jyykt";
                break;
                
                case "QQ币充值卡":
                $ywm = "qqczk";
                break;
                
                case "搜狐一卡通":
                $ywm = "shykt";
                break;
                
                case "征途游戏卡":
                $ywm = "ztyxk";
                break;
                
                case "骏网一卡通":
                $ywm = "jwykt";
                break;
                
                case "盛大一卡通":
                $ywm = "sdykt";
                break;
                
                case "全国神州行":
                $ywm = "qgszx";
                break;
                
                case "天下一卡通":
                $ywm = "txykt";
                break;
                
                case "电信充值":
                $ywm = "dxczk";
                break;
                
                case "纵游一卡通":
                $ywm = "zyykt";
                break;
            }
            return $ywm;
        }

        public function SelectOK(){
           
           $Sjt_TransID = $this->_request("Sjt_TransID");
           
           if($Sjt_TransID == NULL || $Sjt_TransID == ""){
               exit("no1");
           }
           
           $Sign = $this->_request("Sign");
           $Order = D("Order");
           $UserID = $Order->where("TransID = '".$Sjt_TransID."'")->getField("UserID");
           $Userapiinformation = D("Userapiinformation");
           $Sjt_Key = $Userapiinformation->where("UserID=".$UserID)->getField("Key");
           $Signs = md5($Sjt_TransID.$Sjt_Key);
           if($Sing == $Sings){
			   $Sjt_Zt = $Order->where("TransID = '".$Sjt_TransID."'")->getField("Zt");
               if($Sjt_Zt == 1){
                   echo "ok";
               }else{
                   exit("no2");
               }
           }else{
               exit("no3");
           }
        }
       
        //中文转码
		private function TransCode($Code){     
           return iconv("GBK", "UTF-8", $Code);
        }
      
        //返回成功后的处理
		public function TongdaoManage($TransID,$type=1){
		      	$Order = D("Order");
		      	$UserID = $Order->where("TransID = '".$TransID."'")->getField("UserID");
		      	//返回跳转地址
		      	$Sjt_Return_url = $Order->where("TransID = '".$TransID."'")->getField("Sjt_Return_url");
		      	//异步通知地址
		      	$Sjt_Merchant_url = $Order->where("TransID = '".$TransID."'")->getField("Sjt_Merchant_url");
		      	//商户ID
		      	$Sjt_MerchantID = $Order->where("TransID = '".$TransID."'")->getField("UserID");
		      	//在商户平台里用户的用户名（在商城里则是包含品名和订单号的一串字符）
		      	$Sjt_Username = $Order->where("TransID = '".$TransID."'")->getField("Username");
		      	//商品名称
		      	$ProductName = $Order->where("TransID = '".$TransID."'")->getField("ProductName");
		      	//商户实际到账金额
		      	$OrderMoney = $Order->where("TransID = '".$TransID."'")->getField("OrderMoney");
		      	//用户付款金额
		      	$trademoney = $Order->where("TransID = '".$TransID."'")->getField("trademoney");
		      	//实际交易金额
		      	$tranAmt = $trademoney;
		      	//支付类型
		      	$typepay = $Order->where("TransID = '".$TransID."'")->getField("typepay");
		      	//付款方式名称
		      	$bankname = $Order->where("TransID = '".$TransID."'")->getField("bankname");
				$payname = $Order->where("TransID = '".$TransID."'")->getField("payname");
				//订单状态 0未处理 1已处理
		      	$Sjt_Zt = $Order->where("TransID = '".$TransID."'")->getField("Zt");
		      	
				//如果订单没有处理，进行处理操作
		      	if($Sjt_Zt == 0){
					//把订单的状态修改为已处理
		      		 $Order->where("TransID='".$TransID."'")->setField("Zt",1);
					 //修改账户金额
		      		 $Money = D("Money");
		      		 $Y_Money = $Money->where("UserID=".$UserID)->getField("Money");
		      		// $Tongdao_Money = $Money->where("UserID=".$UserID)->getField($TongdaoName);
		      		 $data["Money"] = $OrderMoney + $Y_Money;
		      		// $data[$TongdaoName] = $Tongdao_Money + $OrderMoney;
		      		 $Money->where("UserID=".$UserID)->save($data); 
		      		 //新增资金变动记录
		      		 $Moneybd = M("Moneybd");
		      		 $data["UserID"] = $UserID;
		      		 $data["money"] = $OrderMoney;
		      		 $data["ymoney"] =  $Y_Money;
		      		 $data["gmoney"] = $Y_Money + $OrderMoney;
		      		 $datatime_datetime = date("Y-m-d H:i:s");
		      		 $data["datetime"] = $datatime_datetime;
		      		 //$data["tongdao"] = $TongdaoName;
		      		 $data["TransID"] = $TransID;
		      		 $data["lx"] = 1;
		      		 $result = $Moneybd->add($data);
		      		 $this->bianliticheng($UserID, $tranAmt,$TransID);    //遍历提成
		      	}

		      	$Userapiinformation = D("Userapiinformation");
		      	$keystr = $Userapiinformation->where("UserID=".$UserID)->getField("Key");
		      	$_Result = 1;
		      	$_resultDesc = "";
		      	$_SuccTime = date("YmdHis");
		      	
				$Sjt_Md5Sign = md5($Sjt_MerchantID.$ProductName.$TransID.$tranAmt.$_SuccTime."CNY".$keystr);

					//file_put_contents("Lib/Action/Payapi/jij.txt",$Sjt_Merchant_url."-$TransID",FILE_APPEND);
					//$url="$Sjt_Merchant_url"."?&Sjt_TransID=$TransID";
		            //file_put_contents("Lib/Action/Payapi/jij.txt",$url."-$TransID",FILE_APPEND);
			        //file_get_contents($url);		
		      		//exit;
					 
				$Ordertz = M("Ordertz");
				$ordertzlist = $Ordertz->where("Sjt_TransID = '".$_TransID."'")->select();
				if(!$ordertzlist){
					   $data["Sjt_MerchantID"] = $Sjt_MerchantID;
					   $data["Sjt_TransID"] = $_TransID;
					   $data["Sjt_Return"] = $_Result;  //返回状态 1为正常，0为失败 
					   $data["Sjt_factMoney"] = $tranAmt;
					   $data["Sjt_UserName"] = $ProductName;
					   $data["Sjt_SuccTime"] = $_SuccTime;
					   $data["Sjt_Error"] = $_resultDesc;          //错误编号
					   $data["Sjt_Sign"] = $Sjt_Md5Sign;
					   $data["Sjt_urlname"] = $Sjt_Return_url;
					   $data["success "] = 2;
					   $Ordertz->add($data);
				}

				if ($type == "0") {
					file_put_contents('Lib/Action/Payapi/PayLog.txt',"[".date("Y-m-d H:i:s")."] TongdaoManage同步跳转: TransID=".$TransID." bankname=".$bankname." payname=".$payname." UserId=".$Sjt_MerchantID." tranAmt=".$tranAmt." Return_url=".$Sjt_Return_url." \r\n\r\n",FILE_APPEND);
					//跳转到商户指定的URL
					echo "<form id=\"Form1\" name=\"Form1\" method=\"post\" action=\"".$Sjt_Return_url."\">";
		      		echo "<input type=\"hidden\" name=\"P_UserId\" value=\"".$Sjt_MerchantID."\">";
		      		echo "<input type=\"hidden\" name=\"P_Subject\" value=\"".$ProductName."\">";
		      		echo "<input type=\"hidden\" name=\"P_OrderId\" value=\"".$TransID."\">";
		      		echo "<input type=\"hidden\" name=\"P_FaceValue\" value=\"".$tranAmt."\">";
		      		echo "<input type=\"hidden\" name=\"P_SuccTime\" value=\"".$_SuccTime."\">";
		      		echo "<input type=\"hidden\" name=\"P_PostKey\" value=\"".$Sjt_Md5Sign."\">";
					echo "<input type=\"hidden\" name=\"P_Bankname\" value=\"".$bankname."\">";
					echo "<input type=\"hidden\" name=\"paymethod\" value=\"".$payname."\">";
					echo "<input type=\"hidden\" name=\"out_trade_no\" value=\"".$TransID."\">";
		      		echo "</from>";
		      		echo "<script type=\"text/javascript\">";
		      		echo "document.Form1.submit();";
		      		echo "</script>";
		      	} else if ($type == "2") {
					file_put_contents('Lib/Action/Payapi/PayLog.txt',"[".date("Y-m-d H:i:s")."] TongdaoManage异步通知: TransID=".$TransID." bankname=".$bankname." UserId=".$Sjt_MerchantID." tranAmt=".$tranAmt." Notifyurl=".$Sjt_Merchant_url." \r\n\r\n",FILE_APPEND);
					//发送异步通知URL
					echo "<form id=\"Form1\" name=\"Form1\" method=\"post\" action=\"".$Sjt_Merchant_url."\">";
		      		echo "<input type=\"hidden\" name=\"P_UserId\" value=\"".$Sjt_MerchantID."\">";
		      		echo "<input type=\"hidden\" name=\"P_Subject\" value=\"".$ProductName."\">";
		      		echo "<input type=\"hidden\" name=\"P_OrderId\" value=\"".$TransID."\">";
		      		echo "<input type=\"hidden\" name=\"P_FaceValue\" value=\"".$tranAmt."\">";
		      		echo "<input type=\"hidden\" name=\"P_SuccTime\" value=\"".$_SuccTime."\">";
		      		echo "<input type=\"hidden\" name=\"P_PostKey\" value=\"".$Sjt_Md5Sign."\">";
					echo "<input type=\"hidden\" name=\"P_Bankname\" value=\"".$bankname."\">";
		      		echo "</from>";
		      		echo "<script type=\"text/javascript\">";
		      		echo "document.Form1.submit();";
		      		echo "</script>";
		      	}
		}
      
        //遍历提成
		protected function bianliticheng($UserID,$tranAmt=0,$TransID,$num=1){
			$User = M("User");
			$sjUserID = $User->where("id=".$UserID)->getField("SjUserID");
			if($sjUserID){
					      		$Paycost = M("Paycost"); 
					      		$sjfl = $Paycost->where("UserID=".$sjUserID)->getField("wy");
					      		if($sjfl == 0){
					      			$sjfl = $Paycost->where("UserID=0")->getField("wy");
					      		}
					      		$fl = $Paycost->where("UserID=".$UserID)->getField("wy");
					      		if($fl == 0){
					      			$fl = $Paycost->where("UserID=0")->getField("wy");
					      		}
					      		$tcfl = (1-$fl)-(1-$sjfl);
					      		if($tcfl <= 0 || $tcfl >= 1){
					      			$tcfl = 0;
					      		}
					      		if($tcfl > 0){
							      			$tcmoney = $tcfl*$tranAmt;
							  
							      			$Money = D("Money");
							      			$sjY_Money = $Money->where("UserID=".$sjUserID)->getField("Money");
							      			//$sjtongdaoY_Money = $Money->where("UserID=".$sjUserID)->getField($tongdaoname);
							      
							      			$data["Money"] = $tcmoney + $sjY_Money;
							      			//$data[$tongdaoname] = $sjtongdaoY_Money + $tcmoney;
							      			$Money->where("UserID=".$sjUserID)->save($data); //更新上级账户金额
							      			$Moneybd = M("Moneybd");
							      			$data["UserID"] = $sjUserID;
							      			$data["money"] = $tcmoney;
							      			$data["ymoney"] =  $sjY_Money;
							      			$data["gmoney"] = $tcmoney + $sjY_Money;
							      			$data["datetime"] = date("Y-m-d H:i:s");
							      			//$data["tongdao"] = $tongdaoname;
							      			$data["TransID"] = $TransID;
							      			$data["tcjb"] = $num;
							      			$data["lx"] = 7;
							      			$Moneybd->add($data);
					      		}
					      		$num = $num + 1;
					      		$this->bianliticheng($sjUserID, $tranAmt,$TransID,$num);
			}
			return "";
		}

}
?>
