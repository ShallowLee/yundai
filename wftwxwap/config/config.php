<?php
class Config{
    private $cfg = array(
		//接口请求地址，固定不变，无需修改
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
		//测试商户号，商户需改为自己的
        'mchId'=>'101530272052',
		//测试密钥，商户需改为自己的
        'key'=>'e531c6d047bcb4794d24dba4d66ba8a5',
		//版本号默认2.0
        'version'=>'2.0'
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>