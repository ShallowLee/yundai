<?php
class LlpayAction extends Action
{
    public function index()
    {
//        header("Content-Type:text/html; charset=utf-8");
//        $data = json_encode($_POST);
//        M('log')->add(array("log"=>$data,"time"=>date("Y-m-d H:i:s")));
//
//
//        $activate = M('user')->where(array("activate"=>$_POST['activate']))->find();
//        $sjapi       = M('sjapi')->where(array("apiname"=>$_POST['apiname']))->find();
//        if ($activate){
//            $data = array(
//                "UserId" => $activate['id'],
//                "TransID" => $_POST['no_order'],
//                "trademoney"=>$_POST['money_order'],
//                "sxfmoney" => $_POST['money_order']*$sjapi['fl'],
//                "sxflmoney" => 0,
//                "OrderMoney" => $_POST['money_order']-$_POST['money_order']*$sjapi['fl'],
//                "ProductName" => $_POST['name_goods'],
//                "Sjt_Return_url" => $_POST['url_return'],
//                "tongdao" => $sjapi['myname'],
//                "TradeDate" => date("Y-m-d H:i:s"),
//                "Amount" => 0,
//                "zt" => 0,
//                "typepay" =>0,
//            );
//            M('order')->add($data);
//            echo "10000";
//        }else{
//            echo "50000";
//        }
//        exit();
    }

    public function Pay()
    {
        header("Content-Type:text/html; charset=utf-8");

        $data = json_encode($_POST);
        M('log')->add(array("log"=>$data,"time"=>date("Y-m-d H:i:s")));

        $where['activate'] = $_POST['activate'];

        $activate = M('user')->where(array("activate"=>$_POST['activate']))->find();
        $sjapi       = M('sjapi')->where(array("apiname"=>$_POST['apiname']))->find();
        if ($activate){
            $data = array(
                "UserID" => $activate['id'],
                "TransID" => $_POST['no_order'],
                "trademoney"=>$_POST['money_order'],
                "sxfmoney" => $_POST['money_order']*$sjapi['fl'],
                "sxflmoney" => 0,
                "OrderMoney" => $_POST['money_order']-$_POST['money_order']*$sjapi['fl'],
                "ProductName" => $_POST['name_goods'],
                "Sjt_Return_url" => $_POST['url_return'],
                "tongdao" => $sjapi['myname'],
                "TradeDate" => date("Y-m-d H:i:s"),
                "Amount" => 0,
                "Zt" => 0,
                "typepay" =>0,
            );

            M('order')->add($data);
            echo "10000";
        }else{
            echo "50000";
        }
        exit();
    }

    public function Notify()
    {
        header("Content-Type:text/html; charset=utf-8");
        $data = json_encode($_POST);
        $arr['no_order'] = $_POST['no_order'];
        $arr['result_pay'] = $_POST['result_pay'];
        $arr['user_id'] = $_POST['user_id'];//用户id
        $arr['name_goods'] = $_POST['name_goods'];//商品名称
        $arr['info_order'] = $_POST['info_order'];
        $arr['money_order'] = $_POST['money_order'];
        $arr['activate'] = $_POST['activate'];
        $arr['md5'] = md5($arr['activate'].$arr['$name_goods'].time());
        $array = json_encode($arr);



        $url = "http://llpay.itcitylife.com/";
//        $result = $this->http_post($url,$array);
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        //1=设置头文件的信息作为数据流输出
        curl_setopt($ch,CURLOPT_HEADER,0);
        //1=设置获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
        //设置是post还是get方法
        curl_setopt($ch,CURLOPT_POST,1);
        //传递变量
        curl_setopt($ch,CURLOPT_POSTFIELDS,$_POST);
        $result = curl_exec($ch);
        curl_close($ch);
        var_dump($result);
//
//        M('log')->add(array("log"=>$data,"time"=>date("Y-m-d H:i:s")));


        if ($arr['result_pay']=="SUCCESS"){
            M('order')->where(array("TransID"=>$arr['no_order']))->save(array("Zt"=>1));
            $order = M('order')->where(array("TransID"=>$arr['no_order']))->find();
            $where['Shh'] = $order['UserID'];
            $Listuser = M('Listuser')->where($where)->find();
            $money = $Listuser['money']+$_POST['money_order']*0.990;
            M('Listuser')->where($where)->save(array("money"=>$money));
            echo "10000";
        }else{
            echo "50000";
        }


        exit();
    }
    //HTTP post请求
    public function http_post($url,$param,$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
            $is_curlFile = true;
        } else {
            $is_curlFile = false;
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        if (is_string($param)) {
            $strPOST = $param;
        }elseif($post_file) {
            if($is_curlFile) {
                foreach ($param as $key => $val) {
                    if (substr($val, 0, 1) == '@') {
                        $param[$key] = new \CURLFile(realpath(substr($val,1)));
                    }
                }
            }
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
}