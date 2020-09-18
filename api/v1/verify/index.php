<?php

include_once (__DIR__."/../../../callback/WXBizMsgCrypt.php");
include_once (__DIR__."/../../examples/MessageTest.php");

$encodingAesKey = "EXPQpNLqwBxQ4Paknp2fSAtgyNpgypM4RP3Lra26QLS";
$token = "05mQCTR";
$receiveid = "wxe47a066672546da1";

function getmessages($token,$encodingAesKey,$receiveid){	
	$sVerifyMsgSig = $_GET["msg_signature"];
	$sVerifyTimeStamp = $_GET["timestamp"];
	$sVerifyNonce = $_GET["nonce"];
	//这里如果用$_POST是获取不到数据的，这里小编吃了好几次亏。
	$postStr = file_get_contents("php://input");//①读取POST数据，并且返回加密后的XML格式文本。注意此时的$postStr返回的是XML密文
	$sMsg = "";//②解密XML数据  现在还是空
	$wxcpt = new WXBizMsgCrypt($token,$encodingAesKey,$receiveid); 
	
	$errCode = $wxcpt->DecryptMsg($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $postStr, $sMsg);
	
	if ($errCode == 0) {
			// 解密成功，sMsg即为xml格式的明文
			$xml = new DOMDocument();
			$xml->loadXML($sMsg);
			//取出发送消息的UserID
			$FromUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;  
			//取出发送的消息内容体
			$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;  
			if($content == '验证码'){
				$code = rand(1000, 9999);
				//shell_exec('../../../shell/tihuan.sh '. $FromUserName . " " . $code);
				//改用PHP原生替换方法,不用shell去替换
				changepasswd($FromUserName,$code);
				sendmessage(array($FromUserName),'【恒信通】尊敬的 '. $FromUserName .' 您好,您本次登录VPN服务的验证码为: '.$code);
				$now=date('Y-m-d', time());
				$programlog='../../../logs/chengxu.log_'. $now;
				file_put_contents($programlog, $FromUserName."本次验证码为:".$code . "\r\n", FILE_APPEND);
			}else{
				sendmessage(array($FromUserName),'【恒信通】尊敬的 '. $FromUserName .' 您好,未能理解您输入的: ' .$content);
			}
	} else {
			file_put_contents($programlog,"微信错误代码ERR:".$errCode . "\r\n", FILE_APPEND);
	}
}

function changepasswd($FromUserName,$code){
	$oldfile = "/etc/ppp/chap-secrets";
	$bakfile = "../../../logs/chap-secrets";
	$now=date('Y-m-d_H:i:s', time());
	$bakfile=$bakfile . '_bak_'. $now;
	$filea = fopen($oldfile, "r") or exit("Unable to open filea!");
	$fileb = fopen($bakfile, "w+") or exit("Unable to open fileb!");
	while(!feof($filea)){
		$neirong=fgets($filea);
		if(preg_match("/" . $FromUserName ."\b/",$neirong)){
			$neirong=preg_replace("/(\d{4})(\b)/",$code,$neirong);
		}
		fwrite($fileb,$neirong);
	}
	fclose($filea);
	fclose($fileb);
	copy($bakfile,$oldfile);
}


if (!isset($_GET['echostr'])) {
	$getmessages=getmessages($token,$encodingAesKey,$receiveid);
}else{
	//接受验证数据
	$sVerifyMsgSig = $_GET["msg_signature"];
	$sVerifyTimeStamp = $_GET["timestamp"];
	$sVerifyNonce = $_GET["nonce"];
	$sVerifyEchoStr = $_GET["echostr"]; 
	$sEchoStr = "";	
	$wechatObj = new WXBizMsgCrypt($token,$encodingAesKey,$receiveid);
	$errCode = $wechatObj->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);

		if ($errCode == 0) { 

		  echo $sEchoStr;
		} else { 
		  print("ERR: " . $errCode . "\n\n"); 
		}

	}
?>
