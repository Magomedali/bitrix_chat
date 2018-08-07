<?php require("top.php"); ?>

<?php

use \Bitrix\Main\Application;
use Social\Chat\TopicTable;
$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$topic = null;
if($request->isPost() && isset($request['Topic'])){

	$topic = $request['Topic'];
	$topic['TITLE'] = trim(strip_tags($topic['TITLE']));
	$topic['ORDER'] = (int)$topic['ORDER'];
	
	if(isset($request['update_id']) && (int)$request['update_id']){
		$res = TopicTable::update((int)$request['update_id'],$request['Topic']);
	}else{
		$res = TopicTable::add($request['Topic']);
	}
	
	if($res->isSuccess()){
		LocalRedirect("/bitrix/admin/social.chat_chat_setting.php");
	}else{
		$errors = $res->getErrorMessages();

		print_r($errors);
	}
}elseif($request && isset($request['id']) && (int)$request['id']){
	$topic = TopicTable::getRowById((int)$request['id']);

	//Проверить можно ли редактировать пользователю данную тему
}


$title = $topic && isset($topic['ID']) ? "Update Topic" : "New topic";

$APPLICATION->SetTitle($title);

?>

<h3><?php echo $title?></h3>

<form action="" method="POST">
	<p>
		<label>Topic Title</label>
		<input type="text" name="Topic[TITLE]" value="<?php echo $topic && isset($topic['TITLE']) ? $topic['TITLE'] : "" ?>">
		<label>Topic Sort</label>
		<input type="number" name="Topic[ORDER]" value="<?php echo $topic && isset($topic['ORDER']) ? $topic['ORDER'] : 0 ?>">
		<input type="hidden" name="Topic[OWNER_ID]" value="<?php echo $topic && isset($topic['OWNER_ID']) ? $topic['OWNER_ID'] : 0 ?>">
		<?php
			if($topic && isset($topic['ID'])){
				?>
				<input type="hidden" name="update_id" value="<?php echo $topic['ID']?>">
				<?php
			}
		?>
	</p>

	<p>
		<input type="submit" name="" value="SAVE">
	</p>
</form>
<? require("bottom.php"); ?>