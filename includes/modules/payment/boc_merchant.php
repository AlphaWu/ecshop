<?php
// PHP version of merchant.jsp
//’‚ «B2CAPIÕ®”√∞ÊµƒphpøÕªß∂Àµ˜”√≤‚ ‘
//◊˜    ’ﬂ£∫bocomm
//¥¥Ω® ±º‰£∫2012-4-10
?>

<?php
	//socketøøip	
	$socket_ip = "127.0.0.1";		
	//socketøøøø	
	$socket_port = "8080";		
	//øøø	
	$merchID = "301310063009501";
	//ªÒµ√±Ìµ•¥´π˝¿¥µƒ ˝æ›
	$interfaceVersion = $_REQUEST["interfaceVersion"];		
	$merID = $merchID; //…Ãªß∫≈Œ™πÃ∂®	
	$orderid = $_REQUEST["orderid"];
	$orderDate = $_REQUEST["orderDate"];
	$orderTime = $_REQUEST["orderTime"];
	$tranType = $_REQUEST["tranType"];
	$amount = $_REQUEST["amount"];
	$curType = $_REQUEST["curType"];
	$orderContent = $_REQUEST["orderContent"];
	$orderMono = $_REQUEST["orderMono"];
	$phdFlag = $_REQUEST["phdFlag"];
	$notifyType = $_REQUEST["notifyType"];
	$merURL = $_REQUEST["merURL"];
	$goodsURL = $_REQUEST["goodsURL"];
	$jumpSeconds = $_REQUEST["jumpSeconds"];
	$payBatchNo = $_REQUEST["payBatchNo"];
	$proxyMerName = $_REQUEST["proxyMerName"];
	$proxyMerType = $_REQUEST["proxyMerType"];
	$proxyMerCredentials = $_REQUEST["proxyMerCredentials"];
	$netType = $_REQUEST["netType"];
	$issBankNo = $_REQUEST["issBankNo"];
	$tranCode = "cb2200_sign";

	$source = "";
	
	//htmlentities($orderMono,"ENT_QUOTES","GB2312");
	//¡¨Ω”◊÷∑˚¥Æ
	$source = $interfaceVersion."|".$merID."|".$orderid."|".$orderDate."|".$orderTime."|".$tranType."|"
	.$amount."|".$curType."|".$orderContent."|".$orderMono."|".$phdFlag."|".$notifyType."|".$merURL."|"
	.$goodsURL."|".$jumpSeconds."|".$payBatchNo."|".$proxyMerName."|".$proxyMerType."|".$proxyMerCredentials."|".$netType;


	//¡¨Ω”µÿ÷∑
	$socketUrl = "tcp://".$socket_ip.":".$socket_port;
	$fp = stream_socket_client($socketUrl, $errno, $errstr, 30);
	$retMsg="";
	//
	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else 
	{
		$in  = "<?xml version='1.0' encoding='UTF-8'?>";
		$in .= "<Message>";
		$in .= "<TranCode>".$tranCode."</TranCode>";
		$in .= "<MsgContent>".$source."</MsgContent>";
		$in .= "</Message>";
		fwrite($fp, $in);
		while (!feof($fp)) {
			$retMsg =$retMsg.fgets($fp, 1024);
			
		}
		fclose($fp);
	}	
	//echo "retMsg=".$retMsg."***************";
	//Ω‚Œˆ∑µªÿxml
	$dom = new DOMDocument;
	$dom->loadXML($retMsg);

	$retCode = $dom->getElementsByTagName('retCode');
	$retCode_value = $retCode->item(0)->nodeValue;
	
	$errMsg = $dom->getElementsByTagName('errMsg');
	$errMsg_value = $errMsg->item(0)->nodeValue;

	$signMsg = $dom->getElementsByTagName('signMsg');
	$signMsg_value = $signMsg->item(0)->nodeValue;

	$orderUrl = $dom->getElementsByTagName('orderUrl');
	$orderUrl_value = $orderUrl->item(0)->nodeValue;
	//echo "retMsg=".$retMsg;
	//echo $retCode_value." ".$errMsg_value." ".$signMsg_value." ".$orderUrl_value;

	if($retCode_value != "0")
       {
            echo "Ωª“◊∑µªÿ¬Î£∫".$retCode_value."<br>";
            echo "Ωª“◊¥ÌŒÛ–≈œ¢£∫" .$errMsg_value."<br>";
       }
       else
       {

?> 

<html>
    <head>
        <title>…Ãªß∂©µ•≤‚ ‘</title>
        <meta http-equiv = "Content-Type" content = "text/html;charset=GBK">
    </head>

	<body bgcolor = "#FFFFFF" text = "#000000" onload="form1.submit()">
        <form name = "form1" method = "post" action = "<?php echo($orderUrl_value); ?>">
            <input type = "hidden" name = "interfaceVersion" value = "<?php echo($interfaceVersion); ?>">
            <input type = "hidden" name = "merID" value = "<?php echo($merchID); ?>">
            <input type = "hidden" name = "orderid" value = "<?php echo($orderid); ?>">
            <input type = "hidden" name = "orderDate" value = "<?php echo($orderDate); ?>">
            <input type = "hidden" name = "orderTime" value = "<?php echo($orderTime); ?>">
            <input type = "hidden" name = "tranType" value = "<?php echo($tranType); ?>">
            <input type = "hidden" name = "amount" value = "<?php echo($amount); ?>">
            <input type = "hidden" name = "curType" value = "<?php echo($curType); ?>">
            <input type = "hidden" name = "orderContent" value = "<?php echo($orderContent); ?>">
            <input type = "hidden" name = "orderMono" value = "<?php echo($orderMono); ?>">
            <input type = "hidden" name = "phdFlag" value = "<?php echo($phdFlag); ?>">
            <input type = "hidden" name = "notifyType" value = "<?php echo($notifyType); ?>">
            <input type = "hidden" name = "merURL" value = "<?php echo($merURL); ?>">
            <input type = "hidden" name = "goodsURL" value = "<?php echo($goodsURL); ?>">
            <input type = "hidden" name = "jumpSeconds" value = "<?php echo($jumpSeconds); ?>">
            <input type = "hidden" name = "payBatchNo" value = "<?php echo($payBatchNo); ?>">
            <input type = "hidden" name = "proxyMerName" value = "<?php echo($proxyMerName); ?>">
            <input type = "hidden" name = "proxyMerType" value = "<?php echo($proxyMerType); ?>">
            <input type = "hidden" name = "proxyMerCredentials" value = "<?php echo($proxyMerCredentials); ?>">
            <input type = "hidden" name = "netType" value = "<?php echo($netType); ?>">
            <input type = "hidden" name = "merSignMsg" value = "<?php echo($signMsg_value); ?>">
            <input type = "hidden" name = "issBankNo" value = "<?php echo($issBankNo); ?>">
        </form>
    </body>
 
</html>
<?php
	}
?>
