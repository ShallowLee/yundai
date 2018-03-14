<?php

/* *
 * 配置文件
 * 版本：1.2
 * 日期：2014-06-13
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201408071000001543
$llpay_config['oid_partner'] = '201408071000001539';

//秘钥格式注意不能修改（左对齐，右边有回车符）
$llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCmRl6Zn4MmtoBoelHRT6j6ounts/x1+GiJTB9/eBTl01cBK50h
mOUtGBcOVrJCa0C1NkR8BYgOT/WLfFT8cICw6XSJtf2uzZco71jbwXfFe8MiEx/L
XiQNQHuclpkUa1hXFUUo6Qat8X8L++pVZfjav40dPKf7oFWCYLWBCDOdyQIDAQAB
AoGANe0mqz4/o+OWu8vIE1F5pWgG5G/2VjBtfvHwWUARzwP++MMzX/0dfsWMXLsj
b0UnpF3oUizdFn86TLXTPlgidDg6h0RbGwMZou/OIcwWRzgMaCVePT/D1cuhyD7Y
V8YkjVHGnErfxyia1COswAqcpiS4lcTG/RqkAMsdwSZe640CQQDRvkQ7M2WJdydc
9QLQ9FoIMnKx9mDge7+aN6ijs9gEOgh1gKUjenLr6hcGlLRyvYDKQ4b1kes22FUT
/n+AMaEPAkEAyvH05KRzax3NNdRPI45N1KuT1kydIwL3KpOK6mWuHlffed2EiWLS
dhZNiZy9wWuwFPqkrZ8g+jL0iKcCD0mjpwJBAKbWxWmeCZ+eY3ZjAtl59X/duTRs
ekU2yoN+0KtfLG64RvBI45NkHLQiIiy+7wbyTNcXfewrJUIcNRjRcVRkpesCQEM8
BbX6BYLnTKUYwV82NfLPJRtKJoUC5n/kgZFGPnkvA4qMKOybIL6ehPGiS/tYge1x
XD1pCrPZTco4CiambuECQDNtlC31iqzSKmgSWmA5kErqVJB0f1i+a0CbQLlaPGYN
/qwa7TE13yByaUdDDaTIEUrDyuqWd5+IvlbwuVsSlMw=
-----END RSA PRIVATE KEY-----';


//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = '201408071000001539_sahdisa_20141205';

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

//版本号
$llpay_config['version'] = '1.2';

//请求应用标识 为wap版本，不需修改
$llpay_config['app_request'] = '3';


//签名方式 不需修改
$llpay_config['sign_type'] = strtoupper('RSA');

//订单有效时间  分钟为单位，默认为10080分钟（7天）
$llpay_config['valid_order'] ="10080";

//字符编码格式 目前支持 gbk 或 utf-8
$llpay_config['input_charset'] = strtolower('utf-8');

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$llpay_config['transport'] = 'http';
$llpay_config['risk_item'] = '{\"frms_ware_category\":\"2009\",\"user_info_mercht_userno\":\"123456\",\"user_info_dt_register\":\"20141015165530\",\"user_info_full_name\":\"张三\",\"user_info_id_no\":\"3306821990012121221\",\"user_info_identify_type\":\"1\",\"user_info_identify_state\":\"1\"}
';
$gateWary="http://yundai.itcitylife.com/Payapi_Llpay_Pay.html";


$notify="http://yundai.itcitylife.com/Payapi_Llpay_Notify.html";


//$llpay_config['oid_partner'] = '201710120001012537';
//
////秘钥格式注意不能修改（左对齐，右边有回车符）
//$llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
//MIICXQIBAAKBgQDP7OqQr/7Jvg87rIfBic//iK4/VOcPa16wxSrbT/NFfMYQzwIU
//tf4qvyuFQq5dTUiD/Z2unit+N+8ju80INsXWZ6dtAFqOoyzFmMuX06DXSz2fcJT+
//BLEm84mWRcdoPc2MO/hGJOVci3gvKLQ9yKKH4mPJ/j5LE8P8l3PS+bshUQIDAQAB
//AoGAU4NyN4kpCjj3f11t7ZN/4sAwVKmyYOQcVV3sN8hmCsvx9gBfcpgirWK5hT3i
//MQGAldtBAUjwaTLoL28YDCuLzDevz8aIGR+32D7Dpel7gRSEtjj25tBRqb23DJh3
//c/pQSlizQDw9tU78j1ZkXWAHnYiUTEVeGUZ2vDVv+v53lN0CQQDxkaGTmw9/tN3n
//Drhw0ZR/0wktmpyf57A/3faOjolOSQwyRllfv9qBDTTUqBVRL86tSzEAUBNLr1qJ
//QNb/q0YDAkEA3FjEvXqxeAzH5XBO5TjpVzrUDz9utxt3lUpeFYm5E8Bm2+v53YdK
//dp+Mw13X6uNRYzcbgljcE26xXIjuvvmVGwJBAM4Rr0XdRrFoNst+MTR8dDM+cVvn
//wqhd2moBDOy7BsIzaiYRAPi/DsR74Y9u+xBQufv2YoyjwnIT2iWvnDhpgMUCQQC6
//VcMCLQh46e39Q80kIM2Ku6/quQyqgerNb9dCRXYiktko71QclzVMPT5vVCOsedEw
//osB7qSNqt3f7Nb0X+L2zAkAFwno2kw/fR1itt2ks7yZCmvo65yXoA0UylNfXjtLK
//IcfN3m0FdlTKlgs8fDAIPhQ2kqzg0fOPZYj/3rIhGoze
//-----END RSA PRIVATE KEY-----';
//
//
////安全检验码，以数字和字母组成的字符
//$llpay_config['key'] = '201408071000001539_sahdisa_20141205';
//
////↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
//
////版本号
//$llpay_config['version'] = '1.2';
//
////请求应用标识 为wap版本，不需修改
//$llpay_config['app_request'] = '3';
//
//
////签名方式 不需修改
//$llpay_config['sign_type'] = strtoupper('RSA');
//
////订单有效时间  分钟为单位，默认为10080分钟（7天）
//$llpay_config['valid_order'] ="10080";
//
////字符编码格式 目前支持 gbk 或 utf-8
//$llpay_config['input_charset'] = strtolower('utf-8');
//
////访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
//$llpay_config['transport'] = 'http';
?>