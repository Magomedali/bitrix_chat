<?php require("top.php"); ?>

<?php

use \Bitrix\Main\Application;
use Social\Chat\TopicTable;
$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$topic = null;

if($request->isPost()  && isset($request['Topic']) && isset($request['Topic']['ID'])){
	$id = (int)$request['Topic']['ID'];
	
	if(isset($request['accepted_delete']) && (int)$request['accepted_delete'] && (int)$id){
		TopicTable::deleteWithMessages((int)$id);
	}

	LocalRedirect("/bitrix/admin/social.chat_chat_setting.php");
}

if($request && isset($request['id']) && (int)$request['id']){
	$topic = TopicTable::getRowById((int)$request['id']);
	//Проверить можно ли редактировать пользователю данную тему
}

if(!$topic){
	LocalRedirect("/bitrix/admin/social.chat_chat_setting.php");
}

$title = $topic && isset($topic['ID']) ? "Delete Topic" : "Error topic";

$APPLICATION->SetTitle($title);

?>

<h3><?php echo $title?></h3>

<form action="" method="POST">
	<p>
		<input type="hidden" name="Topic[ID]" value="<?php echo $topic && isset($topic['ID']) ? $topic['ID'] : 0 ?>">
		<label for="accept">Accept removing "<?php echo $topic['TITLE']?>"</label>
		<input id="accept" type="checkbox" name="accepted_delete" value="1">
		<p style="color: red;">If you will remove this topic, you lose all messages in this topic!!!</p>
	</p>

	<p>
		<input type="submit" name="" value="DELETE">
	</p>
</form>
<? require("bottom.php"); ?>