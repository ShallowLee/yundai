<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        'mchId'=>'101530272052',		                //商户号，商户正式上线时需更改为自己的
        'key'=>'e531c6d047bcb4794d24dba4d66ba8a5',      //密钥，商户需更改为自己的
		'notify_url'=>'http://pay08.hzit.com/wftwxsm/request.php?method=callback',   //异步通知地址，必填，要能被外网访问到
        'version'=>'2.0'
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>