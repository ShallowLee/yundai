<?php
include 'des_java.php';
class Pay {
	
	public function create_url($data){
		// return  http_build_query($data);
		$str='';
		foreach($data as $key=>$val){
			$str.=$key.'='.$val.'&';
		}
		return mb_substr($str,0,-1);
	}
	/**
	 * 生成加密参数
	 * @param string $data
	 */
	public function data_signature($data){
		$des = new DES_JAVA ( KEY );
		$dstbdatasign = $des->encrypt ( $data );		
		return $dstbdatasign; 
	}
	
	// 提交数据form	
	public function FORM_POST($url){
		$str='<form id="b2cform" name="b2cform" action="'.$url.'" method="post"></form><script type="text/javascript">document.b2cform.submit();</script>';
		echo $str;
	}
	
	/**
	 * 验证异步通知信息
	 * @param array $params
	 * @param str $dstbdatasign
	 * @return boolean
	 */
	function verify_sign($bonuse_key, $params, $return_sign){
		$sign = "";
		if (isset($params ['dstbdata']) && !is_null($params ['dstbdata']) && $params ['dstbdata'] != '') {
			$sign = $params ['dstbdata'];
		}
	
		$des = new DES_JAVA ( $bonuse_key );
	
		$dstbdatasign = $des->encrypt ( $sign );
	
		if($dstbdatasign == $return_sign){
			return true;
		}else{
			return false;
		}
	
	}
	
	
}