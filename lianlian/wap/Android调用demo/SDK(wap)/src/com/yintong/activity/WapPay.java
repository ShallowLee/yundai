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
 * Wap֧��ҳ��
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
            initWebView(post_data);// ��ʼ��webview
        } else
        {
            Toast.makeText(WapPay.this, "�����������", Toast.LENGTH_LONG).show();
        }
    }

    private void initWebView(String post_data)
    {
        mLoginWebView = (WebView) findViewById(R.id.loginView);

        WebSettings settings = mLoginWebView.getSettings();
        settings.setSupportZoom(true); //
        settings.setBuiltInZoomControls(true); // ����֧������

        // ������ҳ
        mLoginWebView.getSettings().setJavaScriptEnabled(true);
        mLoginWebView.postUrl(PartnerConfig.WAP_URL, // http://10.10.110.236:81/wap/payment.htm
                EncodingUtils.getBytes(post_data.replace("+", "%2B"), "UTF-8"));

        // ��ҳ���ؽ�����
        progressDialog = ProgressDialog.show(this, null, "���ڼ���,���Ժ�...");
        alertDialog = new AlertDialog.Builder(this).create(); // ����AlertDialog
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
                                   "֧���ɹ�",
                                    Toast.LENGTH_LONG).show();
                        }else if(Constants.PAY_FAILURE.equals(str)){
                            Toast.makeText(WapPay.this,
                                    "֧��ʧ��",
                                     Toast.LENGTH_LONG).show();
                        }else if(Constants.PAY_CANCEL.equals(str)){
                            Toast.makeText(WapPay.this,
                                    "��;ȡ������",
                                     Toast.LENGTH_LONG).show();
                        }
                       
                    }
                });
            }
        }, "trader");
    }

    class MyWebViewClient extends WebViewClient{
        /**
         * ����URL��ַ������ҵ�����
         */
        public boolean shouldOverrideUrlLoading(WebView view, String url)
        {
            return false;
        }

        public void onPageStarted(WebView view, String url, Bitmap favicon)
        {
            if (!progressDialog.isShowing())
            { // ��ҳ��ʼ����ʱ����ʾ��������
                progressDialog.show();
            }
        }

        public void onPageFinished(WebView view, String url)
        {
            if (progressDialog.isShowing())
            { // ������Ϻ󣬽���������ʾ
                progressDialog.dismiss();
            }
        }

        public void onReceivedError(WebView view, int errorCode,
                String description, String failingUrl)
        {
            Toast.makeText(WapPay.this, "��ҳ���س���", Toast.LENGTH_LONG).show();
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
            handler.proceed(); // ����������վ��֤��
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
