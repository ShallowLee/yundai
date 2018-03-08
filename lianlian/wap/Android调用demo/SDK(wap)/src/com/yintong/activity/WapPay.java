package com.yintong.activity;

import org.apache.http.util.EncodingUtils;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.graphics.Bitmap;
import android.net.http.SslError;
import android.os.Bundle;
import android.os.Handler;
import android.view.KeyEvent;
import android.webkit.SslErrorHandler;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import com.yintong.utils.Constants;
import com.yintong.utils.FuncUtils;
import com.yintong.utils.PartnerConfig;
import com.yintong.wapsdk.R;

/**
 * Wap支付页面
 * @author kristain
 */
public class WapPay extends Activity{

    private WebView        mLoginWebView;
    private ProgressDialog progressDialog;
    private AlertDialog    alertDialog;
    private String         post_data = "";
    private Handler mHandler = new Handler();

    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.webview);
        post_data = getIntent().getStringExtra(Constants.REQ_PARAM);
        if (!FuncUtils.isNull(post_data))
        {
            initWebView(post_data);// 初始化webview
        } else
        {
            Toast.makeText(WapPay.this, "请求参数错误", Toast.LENGTH_LONG).show();
        }
    }

    private void initWebView(String post_data)
    {
        mLoginWebView = (WebView) findViewById(R.id.loginView);

        WebSettings settings = mLoginWebView.getSettings();
        settings.setSupportZoom(true); //
        settings.setBuiltInZoomControls(true); // 设置支持缩放

        // 加载网页
        mLoginWebView.getSettings().setJavaScriptEnabled(true);
        mLoginWebView.postUrl(PartnerConfig.WAP_URL, // http://10.10.110.236:81/wap/payment.htm
                EncodingUtils.getBytes(post_data.replace("+", "%2B"), "UTF-8"));

        // 网页加载进度条
        progressDialog = ProgressDialog.show(this, null, "正在加载,请稍后...");
        alertDialog = new AlertDialog.Builder(this).create(); // 创建AlertDialog
        mLoginWebView.setWebViewClient(new MyWebViewClient());
        mLoginWebView.addJavascriptInterface(new Object(){
            public void backTrader(final String str)
            {
                mHandler.post(new Runnable(){
                    public void run()
                    {
                        WapPay.this.finish();
                        if(Constants.PAY_SUCCESS.equals(str)){
                            Toast.makeText(WapPay.this,
                                   "支付成功",
                                    Toast.LENGTH_LONG).show();
                        }else if(Constants.PAY_FAILURE.equals(str)){
                            Toast.makeText(WapPay.this,
                                    "支付失败",
                                     Toast.LENGTH_LONG).show();
                        }else if(Constants.PAY_CANCEL.equals(str)){
                            Toast.makeText(WapPay.this,
                                    "中途取消操作",
                                     Toast.LENGTH_LONG).show();
                        }
                       
                    }
                });
            }
        }, "trader");
    }

    class MyWebViewClient extends WebViewClient{
        /**
         * 拦截URL地址，进行业务操作
         */
        public boolean shouldOverrideUrlLoading(WebView view, String url)
        {
            return false;
        }

        public void onPageStarted(WebView view, String url, Bitmap favicon)
        {
            if (!progressDialog.isShowing())
            { // 网页开始加载时，显示进度条。
                progressDialog.show();
            }
        }

        public void onPageFinished(WebView view, String url)
        {
            if (progressDialog.isShowing())
            { // 加载完毕后，进度条不显示
                progressDialog.dismiss();
            }
        }

        public void onReceivedError(WebView view, int errorCode,
                String description, String failingUrl)
        {
            Toast.makeText(WapPay.this, "网页加载出错", Toast.LENGTH_LONG).show();
            alertDialog.setTitle("Error");
            alertDialog.setMessage(description);
            alertDialog.setButton("ok", new DialogInterface.OnClickListener(){
                public void onClick(DialogInterface dialog, int which)
                {
                }
            });
            alertDialog.show();
        }

        public void onReceivedSslError(WebView view, SslErrorHandler handler,
                SslError error)
        {
            handler.proceed(); // 接受所有网站的证书
        }
    }

    public boolean onKeyDown(int keyCode, KeyEvent event)
    {
      /*  if ((keyCode == KeyEvent.KEYCODE_BACK) && (mLoginWebView.canGoBack()))
        {
            mLoginWebView.goBack();
            return true;
        }*/
        return super.onKeyDown(keyCode, event);
    }

}
