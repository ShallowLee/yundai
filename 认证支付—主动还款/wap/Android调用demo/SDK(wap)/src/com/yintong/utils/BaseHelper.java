package com.yintong.utils;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.util.Log;
/**
 * ������
 * 
 */
public class BaseHelper{
    public static final String PARAM_EQUAL = "=";
    public static final String PARAM_AND   = "&";

    /**
     * ��ת�ַ�������
     * 
     * @param is
     * @return
     */
    public static String convertStreamToString(InputStream is)
    {
        BufferedReader reader = new BufferedReader(new InputStreamReader(is));
        StringBuilder sb = new StringBuilder();
        String line = null;
        try
        {
            while ((line = reader.readLine()) != null)
            {
                sb.append(line);
            }
        } catch (IOException e)
        {
            e.printStackTrace();
        } finally
        {
            try
            {
                is.close();
            } catch (IOException e)
            {
                e.printStackTrace();
            }
        }
        return sb.toString();
    }

    /**
     * ��ʾdialog
     * 
     * @param context
     *            ����
     * @param strTitle
     *            ����
     * @param strText
     *            ����
     * @param icon
     *            ͼ��
     */
    public static void showDialog(Activity context, String strTitle,
            String strText, int icon)
    {
        try
        {
            AlertDialog.Builder tDialog = new AlertDialog.Builder(context);
            tDialog.setIcon(icon);
            tDialog.setTitle(strTitle);
            tDialog.setMessage(strText);
            tDialog.setPositiveButton("ȷ��", null);
            tDialog.show();
        } catch (Exception e)
        {

        }
    }

    /**
     * ��ӡ��Ϣ
     * 
     * @param tag
     *            ��ǩ
     * @param info
     *            ��Ϣ
     */
    public static void log(String tag, String info)
    {
        Log.i(tag, info);
    }

    /**
     * ��ȡȨ��
     * 
     * @param permission
     *            Ȩ��
     * @param path
     *            ·��
     */
    public static void chmod(String permission, String path)
    {
        try
        {
            String command = "chmod " + permission + " " + path;
            Runtime runtime = Runtime.getRuntime();
            runtime.exec(command);
        } catch (IOException e)
        {
            e.printStackTrace();
        }
    }

    //
    // show the progress bar.
    /**
     * ��ʾ������
     * 
     * @param context
     *            ����
     * @param title
     *            ����
     * @param message
     *            ��Ϣ
     * @param indeterminate
     *            ȷ����
     * @param cancelable
     *            �ɳ���
     * @return
     */
    public static ProgressDialog showProgress(Context context,
            CharSequence title, CharSequence message, boolean indeterminate,
            boolean cancelable)
    {
        ProgressDialog dialog = new ProgressDialog(context);
        dialog.setTitle(title);
        dialog.setMessage(message);
        dialog.setIndeterminate(indeterminate);
        dialog.setCancelable(false);

        dialog.show();
        return dialog;
    }

    /**
     * �ַ���תjson����
     * 
     * @param str
     * @param split
     * @return
     */
    public static JSONObject string2JSON(String str, String split)
    {
        JSONObject json = new JSONObject();
        try
        {
            String[] arrStr = str.split(split);
            for (int i = 0; i < arrStr.length; i++)
            {
                String[] arrKeyValue = arrStr[i].split("=");
                json.put(arrKeyValue[0],
                        arrStr[i].substring(arrKeyValue[0].length() + 1));
            }
        }

        catch (Exception e)
        {
            e.printStackTrace();
        }

        return json;
    }

    public static JSONObject string2JSON(String str)
    {
        try
        {
            return new JSONObject(str);
        } catch (JSONException e)
        {
            e.printStackTrace();
        }
        return new JSONObject();
    }

    public static String toJSONString(Object obj)
    {
        JSONObject json = new JSONObject();
        try
        {
            List<NameValuePair> list = bean2Parameters(obj);
            for (NameValuePair nv : list)
            {
                json.put(nv.getName(), nv.getValue());
            }
        } catch (JSONException e)
        {
            e.printStackTrace();
        }
        return json.toString();
    }

    /**
     * ��beanת���ɼ�ֵ���б�
     * 
     * @param bean
     * @return
     */
    public static List<NameValuePair> bean2Parameters(Object bean)
    {
        if (bean == null)
        {
            return null;
        }
        List<NameValuePair> parameters = new ArrayList<NameValuePair>();

        // ȡ��bean����public ����
        Method[] Methods = bean.getClass().getMethods();
        for (Method method : Methods)
        {
            if (method != null && method.getName().startsWith("get")
                    && !method.getName().startsWith("getClass"))
            {
                // �õ����Ե�����
                String value = "";
                // �õ�����ֵ
                try
                {
                    String className = method.getReturnType().getSimpleName();
                    if (className.equalsIgnoreCase("int"))
                    {
                        int val = 0;
                        try
                        {
                            val = (Integer) method.invoke(bean);
                        } catch (InvocationTargetException e)
                        {
                            Log.e("InvocationTargetException", e.getMessage(),
                                    e);
                        }
                        value = String.valueOf(val);
                    } else if (className.equalsIgnoreCase("String"))
                    {
                        try
                        {
                            value = (String) method.invoke(bean);
                        } catch (InvocationTargetException e)
                        {
                            Log.e("InvocationTargetException", e.getMessage(),
                                    e);
                        }
                    }
                    if (value != null && value != "")
                    {
                        // ��Ӳ���
                        // ����������ת��Ϊid��ȥ��get������������ĸ��ΪСд
                        String param = method.getName().replaceFirst("get", "");
                        if (param.length() > 0)
                        {
                            String first = String.valueOf(param.charAt(0))
                                    .toLowerCase();
                            param = first + param.substring(1);
                        }
                        parameters.add(new BasicNameValuePair(param, value));
                    }
                } catch (IllegalArgumentException e)
                {
                    Log.e("IllegalArgumentException", e.getMessage(), e);
                } catch (IllegalAccessException e)
                {
                    Log.e("IllegalAccessException", e.getMessage(), e);
                }
            }
        }
        return parameters;
    }

    /**
     * ��Object����List<NameValuePair>ת����key��������������key=value&...��ʽ����
     * 
     * @param list
     * @return
     */
    public static String sortParam(Object order)
    {
        List<NameValuePair> list = bean2Parameters(order);
        return sortParam(list);
    }

    /**
     * ��List<NameValuePair>��key��������������key=value&...��ʽ����
     * 
     * @param list
     * @return
     */
    public static String sortParam(List<NameValuePair> list)
    {
        if (list == null)
        {
            return null;
        }
        Collections.sort(list, new Comparator<NameValuePair>(){
            @Override
            public int compare(NameValuePair lhs, NameValuePair rhs)
            {
                return lhs.getName().compareToIgnoreCase(rhs.getName());
            }
        });
        StringBuffer sb = new StringBuffer();
        for (NameValuePair nameVal : list)
        {
            if (null != nameVal.getValue() && !"".equals(nameVal.getValue()))
            {
                sb.append(nameVal.getName());
                sb.append(PARAM_EQUAL);
                sb.append(nameVal.getValue());
                sb.append(PARAM_AND);
            }
        }
        String params = sb.toString();
        if (sb.toString().endsWith(PARAM_AND))
        {
            params = sb.substring(0, sb.length() - 1);
        }
        Log.v("��ǩ����", params);
        return params;
    }
}