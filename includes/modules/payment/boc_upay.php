<?php

/**
 * ECSHOP 交通银行插件
 * ============================================================================
 * * 版权所有 2014 顾启明，并保留所有权利。
 * 网站地址:  http://cruiser.github.io/qmsky/
 * ----------------------------------------------------------------------------
 * 本软件允许在遵守Apache License v2.0下使用。
 * ============================================================================
 * $Author: Qiming Gu $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/boc_upay.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'boc_upay_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'Qiming Gu';

    /* 网址 */
    $modules[$i]['website'] = 'cruiser.github.io/qmsky';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */

    return;
}

/**
 * 类
 */
class boc_upay
{

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function boc_upay()
    {
    }

    function __construct()
    {
        $this->boc_upay();
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
//        if (!defined('EC_CHARSET'))
//        {
//            $charset = 'utf-8';
//        }
//        else
//        {
//            $charset = EC_CHARSET;
//        }
//
//
//
        $parameter = array(
            'interfaceVersion'      => '1.0.0.0',
            'orderid'      => $order['order_sn'],
            'orderDate'      => date("Ymd"),
            'orderTime'      => date("His"),
            'tranType'      => '0',
            'amount'      => $order['order_amount'],
            'curType'      => 'CNY',
            'orderContent'      => '',
            'orderMono'      => $order['log_id'],
            'phdFlag'      => '',
            'notifyType'      => '1',
            'merURL'      => return_url('boc_upay'),
            'goodsURL'      => return_url('boc_upay'),
            'jumpSeconds'      => '5',
            'payBatchNo'      => '',
            'proxyMerName'      => '',
            'proxyMerType'      => '',
            'proxyMerCredentials'      => '',
            'netType'      => '0',
            'issBankNo'      => 'OTHERS'
        );

        ksort($parameter);
        reset($parameter);

        $param = '';
        $sign  = '';

        foreach ($parameter AS $key => $val)
        {
            $param .= "$key=" .urlencode($val). "&";
            $sign  .= "$key=$val&";
        }

//        $param = substr($param, 0, -1);
//        $sign  = substr($sign, 0, -1). $payment['boc_key'];
//        //$sign  = substr($sign, 0, -1). ALIPAY_AUTH;

        $button = '<div style="text-align:center"><input type="button" onclick="window.open(\'includes/modules/payment/boc_merchant.php?'.$sign.'\')" value="' .$GLOBALS['_LANG']['pay_button']. '" /></div>';

        return $button;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        if (empty($_REQUEST['notifyMsg']))
        {
            return false;
        }
	require_once(dirname(__FILE__) . "/boc_config.php");

	$tranCode = "cb2200_verify";
	$notifyMsg = $_REQUEST["notifyMsg"];   
	$lastIndex = strripos($notifyMsg,"|");
	$signMsg = substr($notifyMsg,$lastIndex+1); //签名信息
	$srcMsg = substr($notifyMsg,0,$lastIndex+1);//原文

	//连接地址
	$socketUrl = "tcp://".$socket_ip.":".$socket_port;
	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
	$retMsg="";
	//
	if (!$fp) {
		return false;
	} else 
	{
		$in  = "<?xml version='1.0' encoding='UTF-8'?>";
		$in .= "<Message>";
		$in .= "<TranCode>".$tranCode."</TranCode>";
		$in .= "<MsgContent>".$notifyMsg."</MsgContent>";
		$in .= "</Message>";
		fwrite($fp, $in);
		while (!feof($fp)) {
			$retMsg =$retMsg.fgets($fp, 1024);
			
		}
		fclose($fp);
	}	
	
	//解析返回xml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;

	error_log("retCode=".$retCode_value."  "."errMsg=".$errMsg_value, 3, 'errors.log');
	if($retCode_value != '0')
	{
	   return false;
        }
	error_log('*******'.$srcMsg.'******',3,'errors.log');
	$arr = preg_split("/\|{1,}/",$srcMsg);
	error_log('*******'.$arr[2].'******'.$arr[9].'******'.$arr[1].'*****',3,'errors.log');
	$order_log = base64_decode($arr[16]);
	if (!check_money($order_log, $arr[2]))
	{
		error_log('check_money_fail',3,'errors.log');
		return false;
	}
//error_log(var_dump($arr[9]), 3, 'errors.log');
	if ('1' == $arr[9])
	{
		/* 改变订单状态 */
		error_log('order_ok',3,'errors.log');
		order_paid($order_log, 2);

		return true;
	}else
	{
		return false;
	}

    }
}

?>
