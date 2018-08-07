<?php
use \Bitrix\Main\Localization\Loc;
?>
<div id="acceptRemoving">
	<h3><?php echo Loc::getMessage('ALI_MODULE_WARNING_REMOVE_TOPIC');?></h3>
	<form action="<?php echo POST_FORM_ACTION_URI;?>" method="POST">
		<input type="hidden" name="action" value="acceptremovingtopic">
		<input type="hidden" name="topic_id" value="<?php echo $arResult['topic_id']?>">
		<a id="topicForm_close"><?php echo Loc::getMessage('ALI_MODULE_CANSEL');?></a>
		<input type="submit" value="<?php echo Loc::getMessage('ALI_MODULE_CONFIRM');?>">
	</form>
</div>