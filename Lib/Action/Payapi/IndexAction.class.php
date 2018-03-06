<?php
class IndexAction extends Action{
    public function index(){
        $this->display();
    }
   
    public function Pay(){
       header("Content-Type:text/html; charset=utf-8");

       //获取支付方式
	   $Sjt_Paytype = $this->_request("Sjt_Paytype");
	   
	   //如果没有支付方式就调用兼容API
       if($Sjt_Paytype == NULL || $Sjt_Paytype == ""){
           //$this->testApi();
		   $this->CompatibleApi();
       }else{
		   //如果是在线付款方式
           if($Sjt_Paytype == "b"){
			   //商户号
               $UserID = intval($this->_request("p1_MerId")) - 10000;
               $Usersafetyinformation = M("Usersafetyinformation");
               $PayBank=intval($this->_request("PayBank"));

               //网银名称为空值则调用数据库里的PayBank值
			   if(!$PayBank || $PayBank==''){
                  $PayBank = $Usersafetyinformation->where("UserID = ".$UserID)->getField("PayBank");
               }
			   //默认网银通道alipay
               if($PayBank == 0 || $PayBank == NULL){
                   $System = M("System");
                   $DefaultBank = $System->where("UserID=0")->getField("DefaultBank"); 
               }else{
                   $DefaultBank = $PayBank;
               }
               
               //PayBank=10001就是银联，否则就去sjapi表里获取相应的通道名称
			   if(intval($PayBank) == 10001){
                   $payname = "Yinlian";
               }else{
                   $Sjapi = M("Sjapi");
                   $payname = $Sjapi->where("id=".$DefaultBank)->getField("payname");
               }
			   //die($payname);
               
               if( $_POST['PayBank']==21){
				   //QQ钱包
                 	$payname  ='QQbao'; 
					//pc或者mobile
                 	R("Payapi/".$payname."/Post",$this->_request("typego"));
               }
			   
			   if( $_POST['PayBank']==20){
				    //微信手机原生支付
                    $payname  ='Wxwap'; 
					R("Payapi/".$payname."/Post",$this->_request("typego"));
               }
			   
			   if( $_POST['PayBank']==22){
				    //微信PC扫码支付
                    $payname  ='Wxdemo';
					R("Payapi/".$payname."/Post",$this->_request("typego"));
               }

			   if( $_POST['PayBank']==1){
				    //id=1的支付宝WAP，但因为我们还在用旧版支付宝手机通道，所以需用Alipay扫码的class来完成WAP调用
                    $payname  ='Alipay';
					R("Payapi/Alipay/Post");
               }

			   //远程调用模块 默认为id=1的支付宝WAP
			   //die("Payapi/".$payname."/Post".$PayBank); 
               R("Payapi/".$payname."/Post");
               
           }else{

               //如果是点卡充值
               if($Sjt_Paytype == "g"){
                   $gameid = $this->_request("pd_FrpId");
                   $Gamepay = M("Gamepay");
                   $payname = $Gamepay->where("sjt='".$gameid."'")->getField("default");
                  // exit("--".$gameid."--");
                   switch($payname){
                       case "baofu":
                       R("Payapi/BaoFu/Post");
                       break;
                       case "qiling":
                       R("Payapi/Qiling/Post");
                       break;
                       case "yibao":
                       R("Payapi/YibaoGame/Post");
                       break;
                       default:
                       echo $payname;
                   }
               }else{
                    exit("<script language='javascript'>alert('请不要非法提交！[".$Sjt_Paytype."]'); location.href='http://".C("WEB_URL")."';</script>");
               }
           }
       }
    }
    
    public function success(){
        $this->assign("msgTitle","");
        $this->assign("message","充值成功！");
        $this->assign("waitSecond",3);
        $this->assign("jumpUrl","User_Index.html");
        $this->display();
    }

    //兼容API
	private function CompatibleApi(){
           $ArrayQiLing = array("P_UserId","P_OrderId","P_FaceValue");
           if($this->gjzpd($ArrayQiLing)){
              $p0_Cmd = "Buy";
              $p1_MerId = $this->_request("P_UserId");   //商户编号  
              $p2_Order = $this->_request("P_OrderId");  //交易编号
              $p3_Amt = $this->_request("P_FaceValue");  //交易金额
              $p4_Cur = "CNY";
              $p5_Pid = $this->_request("P_Subject");   //商户传过来的商品名称
			  //die($p5_Pid);
              $p6_Pcat = "10086";
              $p7_Pdesc = $this->_request("P_Description");   //商户传过来的商品描述
              $p8_Url = $this->_request("P_Result_url");   //商户传过来的跳转地址
              $p9_SAF = "0";
              $pa_MP = "0";
              $pd_FrpId = "zsyh";    //默认为招商银行，暂时无用，在PayAction里要用到
              $pr_NeedResponse = $this->_request("P_Notify_url");   //商户传过来的通知地址
              $Sjt_Paytype = "b";
			  $PostKey = $this->_request("P_PostKey");
			  $is_mobile = $this->_request("is_mobile");

              $Sjt_UserName = $this->_request("P_Subject")."|".$this->_request("P_CardId")."|".$this->_request("P_Description")."|".$this->_request("P_Notic");
			  $Sjt_CardNumber = "";
              $Sjt_CardPassword = "";
              $Sjt_ProudctID = "";
              
              $Userapiinformation = M("Userapiinformation");
              $key = $Userapiinformation->where("UserID=".(intval($p1_MerId)-10000))->getField("key");
              
              $tjurl = "http://".C("WEB_URL")."/Payapi_Index_Pay.html";
              $hmacstr = $p0_Cmd.$p1_MerId.$p2_Order.$p3_Amt.$p4_Cur.$p5_Pid.$p6_Pcat.$p7_Pdesc.$p8_Url.$p9_SAF.$pa_MP.$pd_FrpId.$pr_NeedResponse.$key;
              $hmac = md5($hmacstr);
              $tjurla = $_SERVER["HTTP_REFERER"];

            $this->assign('tjurl',$tjurl);
            $this->assign('p1_MerId',$p1_MerId);
            $this->assign('p2_Order',$p2_Order);
            $this->assign('p3_Amt',$p3_Amt);
            $this->assign('p5_Pid',$p5_Pid);
            $this->assign('p6_Pcat',$p6_Pcat);
            $this->assign('p7_Pdesc',$p7_Pdesc);
            $this->assign('p8_Url',$p8_Url);
            $this->assign('pa_MP',$pa_MP);
            $this->assign('pd_FrpId',$pd_FrpId);
            $this->assign('pr_NeedResponse',$pr_NeedResponse);
            $this->assign('Sjt_UserName',$Sjt_UserName);
			$this->assign('hmac',$hmac);
            $this->assign('PostKey',$PostKey);
            $this->assign('tjurla',$tjurla);
			 
			if($is_mobile == "3"){
				 $this->display('prepay_two');
			} else {
				 $this->display('prepay');
			}
            
           } else {
               exit("<script language='javascript'>alert('请不要非法提交！![".$Sjt_Paytype."]'); location.href='http://".C("WEB_URL")."';</script>");
           }
    }
    
    private function gjzpd($ArrayList){
        foreach($ArrayList as $key => $value){
            if($this->_request($value) == ""){
                break;
                return false;
            }
        }
        return true;
    }
}
?>