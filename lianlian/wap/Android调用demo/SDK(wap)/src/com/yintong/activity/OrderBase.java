package com.yintong.activity;

import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;

import com.yintong.utils.BaseHelper;
import com.yintong.utils.Constants;
import com.yintong.utils.PartnerConfig;
import com.yintong.utils.PayOrder;
import com.yintong.utils.Rsa;
import com.yintong.wapsdk.R;

/**
 * 订单支付页面
 * @author kristain
 *
 */
public class OrderBase extends Activity{

    private Button jump_btn;
    int            ret_code = 0x000001;

    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        jump_btn = (Button) findViewById(R.id.jump_btn);

        jump_btn.setOnClickListener(new OnClickListener(){
            public void onClick(View v)
            {
                // 调用pay方法进行支付
                Intent intent = new Intent(OrderBase.this, WapPay.class);
                SimpleDateFormat dataFormat = new SimpleDateFormat(
                        "yyyyMMddHHmmss");
                Date date = new Date();
                String timeString = dataFormat.format(date);
                PayOrder order = new PayOrder();
                order.setApp_request("1");
                order.setBusi_partner("101001");
                order.setDt_order(timeString);
                order.setInfo_order("用户购买高级套房一间");
                order.setMoney_order("0.01");
                order.setName_goods("高级套房一间");
                order.setNo_order(timeString);
                order.setNotify_url("https://test.yintong.com.cn/bizgateway/getPayNotifyData.htm");
                order.setOid_partner(PartnerConfig.PARTNER);
                order.setSign_type("RSA");
                order.setUrl_return("");
                String content = BaseHelper.sortParam(order);
                Log.i(OrderBase.class.getSimpleName(), "content:{" + content
                        + "}");
                String sign = Rsa.sign(content, PartnerConfig.RSA_YT_PRIVATE);
                order.setSign(sign);
                content = BaseHelper.toJSONString(order);
                Log.i(OrderBase.class.getSimpleName(), "req_data=" + content
                        + "");
                intent.putExtra(Constants.REQ_PARAM, "req_data=" + content);
                startActivityForResult(intent, ret_code);
            }
        });
    }

    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        if (requestCode == ret_code)
        {
        }
    }

}
