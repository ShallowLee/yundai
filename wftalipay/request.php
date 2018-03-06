<?php
/**
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 * 
 * ================================================================
 */
require('Utils.class.php');
require('config/config.php');
require('class/RequestHandler.class.php');
require('class/ClientResponseHandler.class.php');
require('class/PayHttpClient.class.php');

Class Request{
    //$url = 'http://192.168.1.185:9000/pay/gateway';

    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $cfg = null;
    
    public function __construct(){
        $this->Request();
    }

    public function Request(){
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = new Config();

        $this->reqHandler->setGateUrl($this->cfg->C('url'));
        $this->reqHandler->setKey($this->cfg->C('key'));
    }
    
    public function index(){
        $method = isset($_REQUEST['method'])?$_REQUEST['method']:'submitOrderInfo';
        switch($method){
            case 'submitOrderInfo'://提交订单
                $this->submitOrderInfo();
            break;
            case 'queryOrder'://查询订单
                $this->queryOrder();
            break;
            case 'submitRefund'://提交退款
                $this->submitRefund();
            break;
            case 'queryRefund'://查询退款
                $this->queryRefund();
            break;
            case 'callback':
                $this->callback();
            break;
        }
    }
    
    /**
     * 提交订单信息
     */
    public function submitOrderInfo(){
		$status = "500";
		$code_img_url = "";
		$msg = "";
        $this->reqHandler->setReqParams($_GET,array('method'));
        $this->reqHandler->setParameter('service','pay.alipay.native');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('notify_url',$this->cfg->C('notify_url'));
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        
        $data = Utils::toXml($this->reqHandler->getAllParameters());
        //var_dump($this->reqHandler->getAllParameters());
		//exit;
		//{ ["body"]=> string(10) "buyservice" ["key"]=> string(32) "e531c6d047bcb4794d24dba4d66ba8a5" ["mch_create_ip"]=> string(9) "127.0.0.1" ["mch_id"]=> string(12) "101530272052" ["nonce_str"]=> int(1890850452) ["notify_url"]=> string(50) "http://pay08.hzit.com/Payapi_WftAlipay_PayUrl.html" ["out_trade_no"]=> string(20) "95015720180211105527" ["partner"]=> string(12) "101530272052" ["returnurl"]=> string(43) "http://pay08.hzit.com/PayApi/Result_url.php" ["service"]=> string(17) "pay.alipay.native" ["total_fee"]=> string(1) "1" ["version"]=> string(3) "2.0" ["sign"]=> string(32) "88725BEFB2B0D2E2CACA9E89B39E9705" }
        
        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    //echo json_encode(array('status'=>0,'code_img_url'=>$this->resHandler->getParameter('code_img_url'),'code_url'=>$this->resHandler->getParameter('code_url')));
					//echo "<img src=".$this->resHandler->getParameter('code_img_url')."><p>请使用支付宝钱包扫描<br>二维码以完成支付</p>";
                    //exit();
                    $status = "0";
					$code_img_url = $this->resHandler->getParameter('code_img_url');
                }else{
                    //echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')));
					$msg = "Error Code: ".$this->resHandler->getParameter('err_code')." Error Message: ".$this->resHandler->getParameter('err_msg');
                }
            }
            //echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            //echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
			$msg = "Response Code: ".$this->pay->getResponseCode()." Error Info: ".$this->pay->getErrInfo();
        }
		//var_dump($this->resHandler->getAllParameters());
		//exit;
		//{ ["appid"]=> string(16) "2016081701760348" ["charset"]=> string(5) "UTF-8" ["code_img_url"]=> string(95) "https://pay.swiftpass.cn/pay/qrcode?uuid=https%3A%2F%2Fqr.alipay.com%2Fbax09500mqcprizq1gcu207c" ["code_url"]=> string(46) "https://qr.alipay.com/bax09500mqcprizq1gcu207c" ["mch_id"]=> string(12) "101530272052" ["nonce_str"]=> string(10) "2401714483" ["result_code"]=> string(1) "0" ["sign"]=> string(32) "8727AC8C223B4356BB913250624A36C9" ["sign_type"]=> string(3) "MD5" ["status"]=> string(1) "0" ["version"]=> string(3) "2.0" }

		//$sign = md5($this->cfg->C('mchId')."CNY".$nonce_str);
		//$gateWary = "http://pay08.hzit.com/Payapi_WftAlipay_PayUrl.html";
		//$params = "status=".$status."&code_img_url=".$code_img_url."&msg=".urlencode($msg)."&noncestr=".$nonce_str."&sign=".$sign;

		$paymethod = "wftalipay";
		$out_trade_no = $_GET["out_trade_no"];
		$returnurl = $_GET["returnurl"];
		$nonce_str = $this->resHandler->getParameter('nonce_str');
		$usertoken = $_GET["usertoken"];
		$postkey = md5($usertoken."CNY".$nonce_str);
		$params = "out_trade_no=".$out_trade_no."&status=".$status."&code_img_url=".$code_img_url."&msg=".urlencode($msg)."&nonce_str=".$nonce_str."&usertoken=".$usertoken."&postkey=".$postkey."&paymethod=".$paymethod;
		header("location:$returnurl?$params");
		exit();
    }

    /**
     * 查询订单
     */
    public function queryOrder(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            echo json_encode(array('status'=>500,
                                   'msg'=>'请输入商户订单号或平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.query');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                $res = $this->resHandler->getAllParameters();
                Utils::dataRecodes('查询订单',$res);
                //支付成功会输出更多参数，详情请查看文档中的7.1.4返回结果
                echo json_encode(array('status'=>200,'msg'=>'查询结果请查看result.txt文件！','data'=>$res));

                exit();
            }
            echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }
    
    /**
     * 提交退款
     */
    public function submitRefund(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            echo json_encode(array('status'=>500,
                                   'msg'=>'请输入商户订单号或平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refund');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('op_user_id',$this->cfg->C('mchId'));//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                 'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                 'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                                 'refund_id'=>$this->resHandler->getParameter('refund_id'),
                                 'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                                 'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                                 'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee'));*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('提交退款',$res);
                    echo json_encode(array('status'=>200,'msg'=>'提交退款成功,请查看result.txt文件！','data'=>$res));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')));
                    exit();
                }
            }
            echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }
  /**
     * 查询退款
     */
    public function queryRefund(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        if(count($this->reqHandler->getAllParameters()) === 0){
            echo json_encode(array('status'=>500,
                                   'msg'=>'请输入商户订单号,平台订单号,商户退款单号,平台退款单号其中一个!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refundquery');//接口类型
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);//设置请求地址与请求参数
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                  'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                  'refund_count'=>$this->resHandler->getParameter('refund_count'));
                    for($i=0; $i<$res['refund_count']; $i++){
                        $res['out_refund_no_'.$i] = $this->resHandler->getParameter('out_refund_no_'.$i);
                        $res['refund_id_'.$i] = $this->resHandler->getParameter('refund_id_'.$i);
                        $res['refund_channel_'.$i] = $this->resHandler->getParameter('refund_channel_'.$i);
                        $res['refund_fee_'.$i] = $this->resHandler->getParameter('refund_fee_'.$i);
                        $res['coupon_refund_fee_'.$i] = $this->resHandler->getParameter('coupon_refund_fee_'.$i);
                        $res['refund_status_'.$i] = $this->resHandler->getParameter('refund_status_'.$i);
                    }*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('查询退款',$res);
                    echo json_encode(array('status'=>200,'msg'=>'查询成功,请查看result.txt文件！','data'=>$res));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code')));
                    exit();
                }
            }
            echo json_encode(array('status'=>500,'msg'=>$this->resHandler->getContent()));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }
    
    
    /**
     * 异步通知回调方法
     */
    public function callback(){
        $xml = file_get_contents('php://input');
        //$res = Utils::parseXML($xml);
        $this->resHandler->setContent($xml);
		//var_dump($this->resHandler->setContent($xml));
        $this->resHandler->setKey($this->cfg->C('key'));
        if($this->resHandler->isTenpaySign()){
			//file_put_contents('./notifylog.txt',"[".date("Y-m-d H:i:s")."] 异步通知: out_trade_no=".$this->resHandler->getParameter('out_trade_no')."  total_fee=".$this->resHandler->getParameter('total_fee')."  status=".$this->resHandler->getParameter('status')."  result_code=".$this->resHandler->getParameter('result_code')."\r\n\r\n",FILE_APPEND);
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
				//此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                Utils::dataRecodes('接口回调,返回通知参数',$this->resHandler->getAllParameters());

				echo 'success';

				$out_trade_no = $this->resHandler->getParameter('out_trade_no');
				$out_transaction_id = $this->resHandler->getParameter('out_transaction_id');
				$total_fee = $this->resHandler->getParameter('total_fee');
				$result_code = $this->resHandler->getParameter('result_code');
				$pay_result = $this->resHandler->getParameter('pay_result');
				//生成加密验签字符串
				$preEncodeStr=$out_trade_no.$total_fee.$out_transaction_id."CNY";
				$PostKey=md5($preEncodeStr);

				$params = "out_trade_no=".$out_trade_no."&out_transaction_id=".$out_transaction_id."&total_fee=".$total_fee."&result_code=".$result_code."&pay_result=".$pay_result."&PostKey=".$PostKey;
				file_put_contents('./notifylog.txt',"[".date("Y-m-d H:i:s")."] 异步通知: params=".$params."\r\n\r\n",FILE_APPEND);

				//提交到支付方法接口WftAlipayAction
				$ch=curl_init(); 
				curl_setopt($ch,CURLOPT_URL,"http://pay08.hzit.com/Payapi_WftAlipay_NotifyUrl.html");
				//1=设置头文件的信息作为数据流输出
				curl_setopt($ch,CURLOPT_HEADER,0); 
				//1=设置获取的信息以文件流的形式返回，而不是直接输出
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,0); 
				//设置是post还是get方法
				curl_setopt($ch,CURLOPT_POST,1); 
				//传递变量
				curl_setopt($ch,CURLOPT_POSTFIELDS,$params); 
				$result = curl_exec($ch);
				curl_close($ch);

                exit();
            }else{
                echo 'failure';
                exit();
            }
        }else{
            echo 'failure';
        }
    }
}

$req = new Request();
$req->index();
?>