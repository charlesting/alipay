<?php

namespace CT\Alipay\src;

use CT\Alipay\lib\AlipaySubmit;

class PayApi
{
	function __construct(){
		$this->alipay_config = $this->config();
	}

	function AlipayApi() {
		$this->alipay_config = $this->config();
	}

	public function config(){
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者id，以2088开头的16位纯数字
		$pay_config['partner']		= '20888888888888888';

		//收款支付宝账号
		$pay_config['seller_id']	= $pay_config['partner'];

		//商户的私钥（后缀是.pen）文件相对路径
		$pay_config['private_key_path']	= dirname(__FILE__).'/../key/rsa_private_key.pem';

		//支付宝公钥（后缀是.pen）文件相对路径
		$pay_config['ali_public_key_path']= dirname(__FILE__).'/../key/alipay_public_key.pem';

		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

		//签名方式 不需修改
		$pay_config['sign_type']    = strtoupper('RSA');

		//字符编码格式 目前支持 gbk 或 utf-8
		$pay_config['input_charset']= strtolower('utf-8');

		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$pay_config['cacert']    = getcwd().'\\cacert.pem';

		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$pay_config['transport']    = 'http';
		return $pay_config;
	}

	public function submit($data){
		$alipay_config = $this->config();
		/**************************请求参数**************************/

		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//服务器异步通知页面路径
		$notify_url = "http://koudaileyuan.com/buy/alinotify";
		//需http://格式的完整路径，不能加?id=123这类自定义参数

		//页面跳转同步通知页面路径
		$return_url = "http://koudaileyuan.com/buy/alireturn";
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

		//商户订单号
		$out_trade_no = $data['WIDout_trade_no'];
		//商户网站订单系统中唯一订单号，必填

		//订单名称
		$subject = $data['WIDsubject'];
		//必填

		//付款金额
		$total_fee = $data['WIDtotal_fee'];
		//必填

		//商品展示地址
		$show_url = $data['WIDshow_url'];
		//必填，需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

		//订单描述
		$body = $data['WIDbody'];
		//选填

		//超时时间
		$it_b_pay = $data['WIDit_b_pay'];
		//选填

		//钱包token
		$extern_token = $data['WIDextern_token'];
		//选填

		/************************************************************/

		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service" => "alipay.wap.create.direct.pay.by.user",
			"partner" => trim($alipay_config['partner']),
			"seller_id" => trim($alipay_config['seller_id']),
			"payment_type"	=> $payment_type,
			"notify_url"	=> $notify_url,
			"return_url"	=> $return_url,
			"out_trade_no"	=> $out_trade_no,
			"subject"	=> $subject,
			"total_fee"	=> $total_fee,
			"show_url"	=> $show_url,
			"body"	=> $body,
			"it_b_pay"	=> $it_b_pay,
			"extern_token"	=> $extern_token,
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);

		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
		return $html_text;
	}
}
