package com.yintong.utils;

import java.io.Serializable;

public class PayOrder implements Serializable{
    private static final long serialVersionUID = 1L;
    private String            oid_partner;          // 商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201304121000001004
    private String            notify_url;           // 连连钱包支付平台在用户支付成功后通知商户服务端的地址
    private String            busi_partner;         // 虚拟商品销售：101001,实物商品销售：109001
    private String            no_order;             // 商户系统唯一订单号
    private String            dt_order;             // 订单时间 格式：YYYYMMDDH24MISS 14位数字，精确到秒
    private String            no_goods;
    private String            name_goods;
    private String            money_order;          // 该笔订单的资金总额，单位为RMB-元。大于0的数字，精确到小数点后两位。如：49.65
    private String            sign_type;            // 参与签名
    private String            info_order;           //
    private String            oid_userno;           // 注册用户号
    private String            bank_code;            // 银行编号
    private String            force_bank;           // 是否强制使用该银行的银行卡标志(0为不强制，1为强制)
    private String            pay_type;             // 支付方式(2:借记卡,3:信用卡)
    private String            url_return;           //
    private String app_request;
     

    public String getOid_userno()
    {
        return oid_userno;
    }

    public void setOid_userno(String oid_userno)
    {
        this.oid_userno = oid_userno;
    }

    private String sign;

    public String getSign()
    {
        return sign;
    }

    public void setSign(String sign)
    {
        this.sign = sign;
    }

    public String getSign_type()
    {
        return sign_type;
    }

    public void setSign_type(String sign_type)
    {
        this.sign_type = sign_type;
    }

    public String getOid_partner()
    {
        return oid_partner;
    }

    public void setOid_partner(String oid_partner)
    {
        this.oid_partner = oid_partner;
    }

    public String getNotify_url()
    {
        return notify_url;
    }

    public void setNotify_url(String notify_url)
    {
        this.notify_url = notify_url;
    }

    public String getBusi_partner()
    {
        return busi_partner;
    }

    public void setBusi_partner(String busi_partner)
    {
        this.busi_partner = busi_partner;
    }

    public String getNo_order()
    {
        return no_order;
    }

    public void setNo_order(String no_order)
    {
        this.no_order = no_order;
    }

    public String getDt_order()
    {
        return dt_order;
    }

    public void setDt_order(String dt_order)
    {
        this.dt_order = dt_order;
    }

    public String getNo_goods()
    {
        return no_goods;
    }

    public void setNo_goods(String no_goods)
    {
        this.no_goods = no_goods;
    }

    public String getName_goods()
    {
        return name_goods;
    }

    public void setName_goods(String name_goods)
    {
        this.name_goods = name_goods;
    }

    public String getMoney_order()
    {
        return money_order;
    }

    public void setMoney_order(String money_order)
    {
        this.money_order = money_order;
    }

    public String getInfo_order()
    {
        return info_order;
    }

    public void setInfo_order(String info_order)
    {
        this.info_order = info_order;
    }

    public String getBank_code()
    {
        return bank_code;
    }

    public void setBank_code(String bank_code)
    {
        this.bank_code = bank_code;
    }

    public String getForce_bank()
    {
        return force_bank;
    }

    public void setForce_bank(String force_bank)
    {
        this.force_bank = force_bank;
    }

    public String getPay_type()
    {
        return pay_type;
    }

    public void setPay_type(String pay_type)
    {
        this.pay_type = pay_type;
    }

    public String getUrl_return()
    {
        return url_return;
    }

    public void setUrl_return(String url_return)
    {
        this.url_return = url_return;
    }

    public String getApp_request()
    {
        return app_request;
    }

    public void setApp_request(String app_request)
    {
        this.app_request = app_request;
    }
    
    

}
