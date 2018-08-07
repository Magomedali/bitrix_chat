<?php

$topic_id = isset($arResult['topic_id']) ? $arResult['topic_id'] : 0;

$topicdata = isset($arResult['topic']) ? $arResult['topic'] : 0;
?>

<div class="topicForm">
	<span id="topicForm_close"><?php echo GetMessage("ALI_PROFILE_CLOSE");?></span>
	<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST">
		<input type="hidden" name="action" value="proccesstopicform">
		<?php
			if($topic_id){
				?>
				<input type="hidden" name="topic_id" value="<?php echo $topic_id?>">
				<?php
			}
		?>
		<label><?php echo GetMessage("ALI_PROFILE_NAME");?></label>
		<input type="text" name="Topic[TITLE]" value="<?php echo isset($topicdata['TITLE']) ? $topicdata['TITLE'] : ''?>" required='TRUE'>
		<br>
		<label><?php echo GetMessage("ALI_PROFILE_SORT");?></label>
		<input type="number" name="Topic[ORDER]" value="<?php echo isset($topicdata['ORDER']) ? $topicdata['ORDER'] : 0?>">
		<input type="submit" name="proccesstopicform" value="<?php echo GetMessage("ALI_PROFILE_SAVE");?>">
	</form>
</div>