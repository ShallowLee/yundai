<?php
class Config{
    private $cfg = array(
		//�ӿ������ַ���̶����䣬�����޸�
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
		//�����̻��ţ��̻����Ϊ�Լ���
        'mchId'=>'101530272052',
		//������Կ���̻����Ϊ�Լ���
        'key'=>'e531c6d047bcb4794d24dba4d66ba8a5',
		//�汾��Ĭ��2.0
        'version'=>'2.0'
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>