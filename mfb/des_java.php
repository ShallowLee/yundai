<?php
/*
 * des加密类
*/
class DES_JAVA {
	var $key;
	function DES_JAVA($key) {
		$this->key = $key;
	}
	function encrypt($encrypt) {
		$encrypt = $this->pkcs5_pad ( $encrypt );
		$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_DES, MCRYPT_MODE_ECB ), MCRYPT_RAND );
		$passcrypt = mcrypt_encrypt ( MCRYPT_DES, $this->key, $encrypt, MCRYPT_MODE_ECB, $iv );
		return strtoupper ( bin2hex ( $passcrypt ) );
	}
	function decrypt($decrypt) {
		// $decoded = base64_decode($decrypt);
		$decoded = pack ( "H*", $decrypt );
		$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_DES, MCRYPT_MODE_ECB ), MCRYPT_RAND );
		$decrypted = mcrypt_decrypt ( MCRYPT_DES, $this->key, $decoded, MCRYPT_MODE_ECB, $iv );
		return $this->pkcs5_unpad ( $decrypted );
	}
	function pkcs5_unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );

		if ($pad > strlen ( $text ))
			return $text;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return $text;
		return substr ( $text, 0, - 1 * $pad );
	}
	function pkcs5_pad($text) {
		$len = strlen ( $text );
		$mod = $len % 8;
		$pad = 8 - $mod;
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
}
