wap 接入说明

1、目录说明
		
		|-- Android调用demo  	
		|-- iOS调用demo		
		|-- WAP调用demo	
		     |-- ASP 
                     |-- C#						
		     |-- JAVA						
		     |-- PHP	
		|-- RSA生成工具.rar（商户使用RSA 签名时用到）
  2、常见问题

  一、wap 支付为什么出现500 页面错误
   500 页面错误，大部分情况是由于请求参数传的不正确所致，查看原因如下，
   1、查询请求格式是否<input name="req_data" value=''/> value 外层是需要使用单引号,传双引号会和json  串里双引号冲突，导致数据请求错误。
   2、value  里面风控参数 要加斜杠转义符,"risk_item":"{\"user_info_mercht_userno\":\"abbess\",\"frms_ware_category \":\"2009\".. 这种格式。

  二、支付过程出现1003 错误
   1003 是风控拦截错误，正常是由于用户参数值不正确，或是过多测试导致触发我们风控规则所致，查看原因如下，
   1、看下user_id 是否写死，要传用户唯一编号，比如说用户手机。
   2、看下交易是否频繁。
   3、同个user_id 是否绑定很多不同的银行卡。
  三、wap 支付时出现1001 错误
   这个是签约错误，查看原因如下，
   1、待签名串格式不正确，我们这把请求数据中的所有元素(除sign本身)按照“key值=value值”的格式拼接起来，若顺序或者格式不正确的话，就会报错。
   2、key 值配置错误，商户正式商户号的key 值可在商户站（https://yintong.com.cn/merchant/trader/login.htm） -》安全中心-》商户秘钥维护  进行修改。

  3、php 接入指南
   log.txt 可查看商户情况的日志，会打印商户请求的待签名串。
   llpay.config.php 是配置文件，可对商户号和秘钥等参数进行修改，可修改商户号，签名方式和MD5_KEY 值。若修改为RSA 签名可在 key 文件夹下修改rsa_private_key.pem 秘钥
   notify_url.php 是异步通知文件  用户处理接受通知异步通知内容。
   return_url.php 是同步通知文件 ，用来处理同步处理内容。
   llpayapi.php  连连支付接口入口文件
  4、java 接入指南
    src\com\lianpay\share\config\PartnerConfig.java 可配置商户号 签名方式和秘钥
    \WebContent\index.jsp  连连支付入口
  5、c# 接入指南
    PartnerConfig.cs

wap 可配置商户号 签名方式和秘钥 
    支付入口文件
Default.aspx  支付入口文件

    notify_url.aspx,notify_url.aspx.cs
  

异步通知页面  

    urlReturn.aspx,urlReturn.aspx.cs  
支付结束后返回页面
  6、上线说明 
   商户要走上线流程，需要确认异步通知和风控参数无误的情况下，才可以上线,需要上线时可找下我们市场人员说下。