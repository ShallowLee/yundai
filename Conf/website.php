<?php
    if($_SERVER['SERVER_NAME']=='yundai.itcitylife.com') {//测试网址
        return array(
            'WEB_URL' => 'yundai.itcitylife.com',
            'WEB_URL_CZ' => 'yundai.itcitylife.com',
            'WEB_APIURL' => 'yundai.itcitylife.com',
            'WEB_NAME'   => '云代付',
            'ADMIN_NAME'  => 'admin',
            'WEB_TEL'  => '4000250520',
            'WEB_COMPANY'  => '杭州云代科技有限公司',
            'WEB_ICP'  => '浙ICP备14037779号'
        );
    }else{//上线网址
        return array(
            'WEB_URL' => 'pay08.hzit.com',
            'WEB_URL_CZ' => 'pay08.hzit.com',
            'WEB_APIURL' => 'pay08.hzit.com',
            'WEB_NAME'   => '云代付',
            'ADMIN_NAME'  => 'admin',
            'WEB_TEL'  => '4000250520',
            'WEB_COMPANY'  => '杭州云代科技有限公司',
            'WEB_ICP'  => '浙ICP备14037779号'

        );
}

?>