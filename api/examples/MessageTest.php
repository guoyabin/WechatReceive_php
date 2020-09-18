<?php /*
 * Copyright (C) 2017 All rights reserved.
 *   
 * @File MessageTest.php
 * @Brief 
 * @Author abelzhu, abelzhu@tencent.com
 * @Version 1.0
 * @Date 2017-12-26
 *
 */

function sendmessage($user,$code){
	include_once(__DIR__."/../src/CorpAPI.class.php");
	include_once(__DIR__."/../src/ServiceCorpAPI.class.php");
	include_once(__DIR__."/../src/ServiceProviderAPI.class.php");
	$config = require(__DIR__."/config.php");
	
	$agentId = $config['APP_ID'];
	$api = new CorpAPI($config['CORP_ID'], $config['APP_SECRET']);
	try { 
		
		$message = new Message();
		{
			$message->sendToAll = false;
			$message->touser = $user;
			print_r($message->touser);
			$message->toparty = 1;
			$message->totag= 1;
			$message->agentid = $agentId;
			$message->safe = 0;
			$message->messageContent = new TextMessageContent(
				//$content = '【恒信通】尊敬的 '. $user[0] .' 您好,您本次登录VPN服务的验证码为: '.$code
				$content = $code
			);
			//$message->messageContent = new NewsMessageContent(
				//array(
					//new NewsArticle(
					//    $title = "Got you !", 
					//    $description = "Who's this cute guy testing me ?", 
					//    $url = "https://work.weixin.qq.com/wework_admin/ww_mt/agenda", 
					//    $picurl = "https://p.qpic.cn/pic_wework/167386225/f9ffc8f0a34f301580daaf05f225723ff571679f07e69f91/0", 
					//    $btntxt = "btntxt"
					//),
				//)
			//);
		}
		$invalidUserIdList = null;
		$invalidPartyIdList = null;
		$invalidTagIdList = null;
		$api->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
	} catch (Exception $e) { 
		echo $e->getMessage() . "\n";
	}
}

?>