<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>连连支付wap交易接口</title>
</head>
<?php



//商户用户唯一编号
          $user_id = $_POST['user_id'];
//$user_id = rand(100000000,999999999);

//平台商户密匙
$activate = $_POST['activate'];

//支付类型101001
          $busi_partner = $_POST['busi_partner'];
//$busi_partner = 101001;
//商户订单号
          $no_order = $_POST['no_order'];
//$no_order = time().rand(1000000000,9999999999);
//商户网站订单系统中唯一订单号，必填

//付款金额
          $money_order = $_POST['money_order'];
//$money_order = 0.01;
//必填

//商品名称
          $name_goods = $_POST['name_goods'];
//$name_goods = "testproduct";

//订单描述
          $info_order = $_POST['info_order'];
//$info_order = "testproductdesc";

//卡号
          $card_no = $_POST['card_no'];
//$card_no = 6212261202024692569;
//$card_no = 6222021307002365870;622908413017837419


//姓名
          $acct_name = $_POST['acct_name'];
//$acct_name = "胡长涛";
//$acct_name = "李俊";
//身份证号
          $id_no = $_POST['id_no'];
//$id_no = 500233198904144430;
//$id_no = 340222199009112315;
//协议号
          $no_agree = $_POST['no_agree'];

//风险控制参数
//          $risk_item = $_POST['risk_item'];

//订单有效期
          $valid_order = $_POST['valid_order'];

//$valid_order = "10080";

//服务器异步通知页面路径
          $notify_url = "http://yundai.itcitylife.com/lianlian/notify_url.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数

//页面跳转同步通知页面路径
//          $return_url = "http://yundai.itcitylife.com/lianlian/return_url.php";
$return_url = $_POST['return_url'];
//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

          /************************************************************/
          require_once ("llpay.config.php");
          require_once ("lib/llpay_submit.class.php");
            require_once ("lib/llpay_core.function.php");
//构造要请求的参数数组，无需改动
          $parameter = array (
              "oid_partner" => trim($llpay_config['oid_partner']),
              "app_request" => trim($llpay_config['app_request']),
              "sign_type" => trim($llpay_config['sign_type']),
              "valid_order" => trim($llpay_config['valid_order']),
              "user_id" => $user_id,
              "busi_partner" => $busi_partner,
              "no_order" => $no_order,
              "dt_order" => local_date('YmdHis', time()),
              "name_goods" => $name_goods,
              "info_order" => $info_order,
              "money_order" => $money_order,
              "notify_url" => $notify_url,
              "url_return" => $return_url,
              "card_no" => $card_no,
              "acct_name" => $acct_name,
              "id_no" => $id_no,
              "no_agree" => $no_agree,
              "risk_item" => $risk_item,
              "valid_order" => $valid_order
          );


          $data = array (
              "oid_partner" => trim($llpay_config['oid_partner']),
              "app_request" => trim($llpay_config['app_request']),
              "sign_type" => trim($llpay_config['sign_type']),
              "valid_order" => trim($llpay_config['valid_order']),
              "user_id" => $user_id,
              "busi_partner" => $busi_partner,
              "no_order" => $no_order,
              "dt_order" => local_date('YmdHis', time()),
              "name_goods" => $name_goods,
              "info_order" => $info_order,
              "money_order" => $money_order,
              "notify_url" => $notify_url,
              "url_return" => $return_url,
              "card_no" => $card_no,
              "acct_name" => $acct_name,
              "id_no" => $id_no,
              "no_agree" => $no_agree,
              "risk_item" => $risk_item,
              "valid_order" => $valid_order,
              "apiname" => "llpay",
              "activate" => $activate,
          );
//          首先验证商户密匙信息是否正确
          $activate = http_post($gateWary,$data);
          if ($activate==50000){
              echo "验证失败";
              exit();
          }
//var_dump($http_post);

//$activate = http_post($gateWary,$parameter);

          $llpaySubmit = new LLpaySubmit($llpay_config);
          $html_text = $llpaySubmit->buildRequestForm($parameter, "post", "确认");
          echo $html_text;

//echo "https://wap.lianlianpay.com/authpay.htm";
/* *
 * 功能：连连支付wap交易接口接入页
 * 版本：1.2
 * 修改日期：2014-06-13
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */

/**************************请求参数**************************/
////////////////////////////////////////////////////////
/// ////////////////////////////////////////////////////
//    $llpay_config['oid_partner'] = '201408071000001539';
//
//    //秘钥格式注意不能修改（左对齐，右边有回车符）
//    $llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
//    MIICXAIBAAKBgQCmRl6Zn4MmtoBoelHRT6j6ounts/x1+GiJTB9/eBTl01cBK50h
//    mOUtGBcOVrJCa0C1NkR8BYgOT/WLfFT8cICw6XSJtf2uzZco71jbwXfFe8MiEx/L
//    XiQNQHuclpkUa1hXFUUo6Qat8X8L++pVZfjav40dPKf7oFWCYLWBCDOdyQIDAQAB
//    AoGANe0mqz4/o+OWu8vIE1F5pWgG5G/2VjBtfvHwWUARzwP++MMzX/0dfsWMXLsj
//    b0UnpF3oUizdFn86TLXTPlgidDg6h0RbGwMZou/OIcwWRzgMaCVePT/D1cuhyD7Y
//    V8YkjVHGnErfxyia1COswAqcpiS4lcTG/RqkAMsdwSZe640CQQDRvkQ7M2WJdydc
//    9QLQ9FoIMnKx9mDge7+aN6ijs9gEOgh1gKUjenLr6hcGlLRyvYDKQ4b1kes22FUT
//    /n+AMaEPAkEAyvH05KRzax3NNdRPI45N1KuT1kydIwL3KpOK6mWuHlffed2EiWLS
//    dhZNiZy9wWuwFPqkrZ8g+jL0iKcCD0mjpwJBAKbWxWmeCZ+eY3ZjAtl59X/duTRs
//    ekU2yoN+0KtfLG64RvBI45NkHLQiIiy+7wbyTNcXfewrJUIcNRjRcVRkpesCQEM8
//    BbX6BYLnTKUYwV82NfLPJRtKJoUC5n/kgZFGPnkvA4qMKOybIL6ehPGiS/tYge1x
//    XD1pCrPZTco4CiambuECQDNtlC31iqzSKmgSWmA5kErqVJB0f1i+a0CbQLlaPGYN
//    /qwa7TE13yByaUdDDaTIEUrDyuqWd5+IvlbwuVsSlMw=
//    -----END RSA PRIVATE KEY-----';
//
//
//    //安全检验码，以数字和字母组成的字符
//    $llpay_config['key'] = '201408071000001539_sahdisa_20141205';
//
//    //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
//
//    //版本号
//    $llpay_config['version'] = '1.2';
//
//    //请求应用标识 为wap版本，不需修改
//    $llpay_config['app_request'] = '3';
//
//
//    //签名方式 不需修改
//    $llpay_config['sign_type'] = strtoupper('RSA');
//
//    //订单有效时间  分钟为单位，默认为10080分钟（7天）
//    $llpay_config['valid_order'] ="10080";
//
//    //字符编码格式 目前支持 gbk 或 utf-8
//    $llpay_config['input_charset'] = strtolower('utf-8');
//
//    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
//    $llpay_config['transport'] = 'http';
//////////////////////////////////////////////////////////////
/// //////////////////////////////////////////////////////////




?>
</body>
</html>