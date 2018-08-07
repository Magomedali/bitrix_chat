<?php
header("Content-type: application/json");

//Получаем данные дб для подключения
$setting =  require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/.settings.php");


function  sendResponceError($errorName,$msg){
	$ans['action']="error";
	$ans['code'] = 1;
	$ans['errorName']=$errorName;
	$ans['userMessage']=$msg;

	return json_encode($ans);
};

$templateMsg = "/bitrix/components/socialchat/main.window/templates/.default/messages.php";

$getHtml = function($template,$arResult){
	ob_start();
	include($_SERVER["DOCUMENT_ROOT"].$template);
	$html = ob_get_clean();

	return $html;
};

if(isset($setting['connections']) && isset($setting['connections']['value']) && isset($setting['connections']['value']['default'])){
	$c = $setting['connections']['value']['default'];
	
	$db['host'] = isset($c['host']) ? $c['host'] : null;
	$db['dbname'] = isset($c['database']) ? $c['database'] : null;
	$db['user'] = isset($c['login']) ? $c['login'] : null;
	$db['pass'] = isset($c['password']) ? $c['password'] : null;

	if($db['host'] !== null && $db['dbname'] !== null && $db['user'] !== null && $db['pass'] !== null){
		
		try{
			$connection = "mysql:host=".$db['host'].";dbname=".$db['dbname'].";charset=utf8"; 

			$pdo = new PDO($connection,$db['user'],$db['pass']);

		}catch(Exeption $e){

			echo sendResponceError("dbConnectionError","Error db connection!");
			exit;

		}
		


	}else{
		echo sendResponceError("notHasDbParams","Not has data base connection params!");
		exit;
	}
}else{
	echo sendResponceError("notHasDbParams","Not has data base connection params!");
	exit;
}


$g = isset($_GET) && count($_GET) ? $_GET : null;

if($g){
	if(isset($g['task'])){

		if($g['task'] === "checkstate"){

			$params = json_decode($g['params'],true);

			$ans = array();
			if(isset($params['user']) && (int)$params['user'] && isset($params['activeTopic']) && isset($params['activeTopic']['id']) && isset($params['activeTopic']['last_msg']) && $params['activeTopic']['id'] !== null && (int)$params['activeTopic']['last_msg']){

				$a_id = (int)$params['activeTopic']['id'];
				$a_last_msg = (int)$params['activeTopic']['last_msg'];

				$res = $pdo->query("CALL get_new_msg({$a_id},{$a_last_msg})")->fetchAll();
				
				if($res && count($res) && !isset($res[0]["nothas"])){
				
					$data['messages'] = array_reverse($res);
					$data['user_id'] = (int)$params['user'];

					$html = $getHtml($templateMsg,$data);
					$ans['html'] = $html;

					$ans['params']['activeTopic']['id'] = $a_id;
					$ans['params']['activeTopic']['last_msg'] = $data['messages'][0]['ID']; //первый элемент дожен быть последнее доб смс
				}else{
					$ans["notHasNewMsg"] = 1;
				}

				//$ans['post'] = $params['otherTopics'];

				if(isset($params['otherTopics']) && is_array($params['otherTopics']) && count($params['otherTopics'])){
					$otherTopics = array();
					foreach ($params['otherTopics'] as $key => $t) {
						
						if(isset($t['id'])  && isset($t['last_msg'])){
							$temp = array();
							try{
								$id = (int)$t['id'];
								$last_msg = (int)$t['last_msg'];
								$sql = "select exists_new_msg({$id},{$last_msg}) as c";
								
								$sth = $pdo->prepare($sql);
								$sth->execute();
								$count = $sth->fetchColumn();
								
								$temp['id'] = (int)$t['id'];
								$temp['last_msg'] = (int)$t['last_msg'];
								$temp['count'] = $count;

							}catch(Exception $e){
								$temp['id'] = (int)$t['id'];
								$temp['last_msg'] = (int)$t['last_msg'];
								$temp['error'] = 1;
							}
							array_push($otherTopics, $temp);
						}
						
					}
					$ans['params']['otherTopics'] = $otherTopics;
				}

				$ans['action'] = 'commitchanges';
			}

			echo json_encode($ans);
			exit;

		}else{
			echo sendResponceError("wrongTask","got wrong task!");
			exit;
		}

	}else{
		echo sendResponceError("wrongRequest","got wrong request!");
		exit;
	}
}else{
	echo sendResponceError("notHasRequestParams","Not has request params!");
	exit;
}
?>
