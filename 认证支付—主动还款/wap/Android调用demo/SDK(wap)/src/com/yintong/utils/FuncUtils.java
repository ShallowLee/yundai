/**
 * 
 */
package com.yintong.utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.AlertDialog;
import android.app.Service;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Vibrator;
import android.provider.Settings;

/**
 * 公共工具类
 * @author kristain
 */
public class FuncUtils{
    private static final String PARAM_EQUAL     = "=";
    private static final String PARAM_AND       = "&";
    private static final String PARAM_SIGNATURE = "signature";
    private static final String PARAM_SIGN_TYPE = "sign_type";
    private static final String PARAM_SIGN_KEY  = "sign_key";
    private static final String SIGN_TYPE_MD5   = "MD5";

    public static boolean isNull(String str)
    {
        if (str == null || "".equals(str) || str.equalsIgnoreCase("NULL"))
        {
            return true;
        }
        return false;
    }

    
    /**
     * 厘转元方法
     * @param money
     * @return
     */
    public static String formatLTYMoneny(String money)
    {
        if (money == null || money.trim().equals("") || money.equals("null"))
        {
            return "0";
        }
        try
        {
            double dmoney = Double.parseDouble(money);
            dmoney /= 1000;
            DecimalFormat format = new DecimalFormat("#0.00");
            return format.format(dmoney);
        } catch (Exception e)
        {
            return "0";
        }
    }

    /**
     * 分转元方法
     * 
     * @param money
     * @return
     */
    public static String formatFTYMoneny(String money)
    {
        if (money == null || money.trim().equals("") || money.equals("null"))
        {
            return "0";
        }
        try
        {
            double dmoney = Double.parseDouble(money);
            dmoney /= 100;
            DecimalFormat format = new DecimalFormat("#0.0");
            return format.format(dmoney);
        } catch (Exception e)
        {
            return "0";
        }
    }

    /**
     * 
     * 功能描述：元转里方法
     * 
     * @param yuan
     * @return
     */
    public static String formatYTLMoneny(String yuan)
    {
        try
        {
            double dYuan = Double.parseDouble(yuan);
            double dLi = dYuan * 1000;
            DecimalFormat format = new DecimalFormat("#");
            return format.format(dLi);
        } catch (Exception e)
        {
            return "0";
        }

    }

    /***
     * 
     * @title isNetworkAvaiable
     * @descript 判断是否有可用网络
     * @param @param context
     * @param @return
     * @throws
     */
    public static boolean isNetworkAvaiable(Context context)
    {
        ConnectivityManager conMan = (ConnectivityManager) context
                .getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo netinfo = conMan.getActiveNetworkInfo();
        return netinfo != null && netinfo.isConnected();
    }

    /**
     * 获取当前程序的版本号
     * 
     * @param context
     * @return
     */
    public static String getVersionName(Context context)
    {
        PackageManager packageManager = context.getPackageManager();
        // getPackageName()是你当前类的包名，0代表是获取版本信息
        PackageInfo packInfo = null;
        try
        {
            packInfo = packageManager.getPackageInfo(context.getPackageName(),
                    0);
        } catch (NameNotFoundException e)
        {
            e.printStackTrace();
            return "0";
        }
        return packInfo.versionName;
    }

    /**
     * 获取当前程序的版本
     * 
     * @param context
     * @return
     */
    public static int getVersionCode(Context context)
    {
        PackageManager packageManager = context.getPackageManager();
        // getPackageName()是你当前类的包名，0代表是获取版本信息
        PackageInfo packInfo = null;
        try
        {
            packInfo = packageManager.getPackageInfo(context.getPackageName(),
                    0);
        } catch (NameNotFoundException e)
        {
            e.printStackTrace();
            return 1;
        }
        return packInfo.versionCode;
    }

   

    /**
     * @param baseActivity
     * @param systemNetworkUnlink
     */
    public static void builderNetworkDialog(final Context context)
    {
        // 网络不可用,弹框提示
        new AlertDialog.Builder(context)
                .setTitle("网络错误")
                .setMessage("网络错误，请检查网络设置！")
                // 设置网络
                .setPositiveButton("设置网络",
                        new DialogInterface.OnClickListener(){
                            public void onClick(DialogInterface dialog,
                                    int which)
                            {
                                context.startActivity(new Intent(
                                        Settings.ACTION_WIRELESS_SETTINGS));// 进入无线网络配置界面
                                dialog.cancel();
                            }
                        })
                // 取消
                .setNegativeButton("取消", new DialogInterface.OnClickListener(){
                    public void onClick(DialogInterface dialog, int which)
                    {
                        return;
                    }
                }).show();
        return;
    }

    /**
     * 参数1：表示停200ms, 震80ms, 停30ms，震80ms 参数2：-1 表示不重复，非-1表示从指定的下标开始重复震动！ 注意
     * ：如第二个参数是0, 则一圈一圈的循环震动下去了；如果是2，这第一遍震动后，从"20”这个参数开始再循环震动！
     * 一般用vv.vibrate(500);//震半秒钟就行了 vv.cancel();//停止震动,基本只有在循环震动时才用得到这个
     * 
     * @param context
     */
    public static void zhendong(final Context context)
    {
        Vibrator vv = (Vibrator) context.getApplicationContext()
                .getSystemService(Service.VIBRATOR_SERVICE);
        vv.vibrate(500);// 震半秒钟
        vv.vibrate(new long[] { 200, 80, 30, 80 }, -1);
    }

    /**
     * yyyy-MM-dd HH:mm:ss 时间转换为 yyyy-MM-dd
     * 
     * @param dateTime
     * @return
     */
    public static String shortTime(String dateTime)
    {
        if (FuncUtils.isNull(dateTime))
        {
            return null;
        }
        try
        {
            SimpleDateFormat formater = new SimpleDateFormat(
                    "yyyy-MM-dd HH:mm:ss");
            Date date = formater.parse(dateTime);
            formater.applyPattern("yyyy-MM-dd");
            return formater.format(date);
        } catch (Exception e)
        {
            return null;
        }

    }
    
    /**
     * yyyyMMddHHmmss 时间转换为 yyyy-MM-dd
     * 
     * @param dateTime
     * @return
     */
    public static String formatTime(String dateTime)
    {
        if (FuncUtils.isNull(dateTime))
        {
            return null;
        }
        try
        {
            SimpleDateFormat formater = new SimpleDateFormat(
                    "yyyyMMddHHmmss");
            Date date = formater.parse(dateTime);
            formater.applyPattern("yyyy-MM-dd HH:mm");
            return formater.format(date);
        } catch (Exception e)
        {
            return null;
        }
    }
    

  

    /**
     * 格式化银行卡
     * 
     * @return
     */
    public static String formatCardNumber(String card)
    {
        String temp = "";
        if (FuncUtils.isNull(card))
        {
            return card;
        }
        card = card.replaceAll(" ", "");
        int len = card.length();
        for (int i = 0; i < len; i++)
        {
            if (i % 4 == 0)
            {
                int end = i + 4;
                if (end > len)
                {
                    end = len;
                }
                temp = temp + card.substring(i, end) + " ";
            }
        }
        if (temp.endsWith(" "))
        {
            temp = temp.substring(0, temp.length() - 1);
        }
        return temp;
    }

    /**
     * 返回银行卡后4位
     * 
     * @param id
     * @return
     */
    public static String getShortCardNo(String cardNo)
    {
        if (FuncUtils.isNull(cardNo))
        {
            return cardNo;
        }
        if (cardNo.length() > 4)
        {
            return cardNo.substring(cardNo.length() - 4, cardNo.length());
        } else
        {
            return cardNo;
        }
    }

   

}
