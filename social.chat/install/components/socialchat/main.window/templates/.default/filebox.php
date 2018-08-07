<?php
use \Bitrix\Main\Localization\Loc;

$this->addExternalJS("/bitrix/components/socialchat/chat.profile/js/jquery.form.js");
$this->addExternalJS("/bitrix/components/socialchat/main.window/js/filebox.js");
$this->addExternalCss("/bitrix/components/socialchat/main.window/css/filebox.css");

$current_member_id = isset($arResult['current_member']) && isset($arResult['current_member']['ID']) ? $arResult['current_member']['ID'] : 0;
?>
<div id="file-box">
	<!--div>
		<a href="#" id="showmethods">FILE</a>
	</div-->
	<div id="file-methods2">
		<div class="newfile">
			<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST" enctype="multipart/form-data" id="form-add-file">
				<input type="hidden" name="action" value="filemessage">
				<input type="hidden" name="member" value="<?php echo $current_member?>">
				<!--label for="add-file">Add file</label-->
				<input type="file" name="message_file" id="add-file">
			</form>
			<div id="loaded_image">
				<div class="target_loaded_image">
					
				</div>
				<div class="panel-manage">
					<a href="/chat/?action=cleartempfile" id="clear_loaded_image"><?php echo Loc::getMessage('ALI_MODULE_TO_CLEAR');?></a>
				</div>
			</div>
		</div>
		<div class="selectfile">
			<a href="/chat?action=albumlist" data-mid="<?php echo $current_member_id?>" id="loadalbums"><?php echo Loc::getMessage('ALI_MODULE_FROM_ALBUM');?></a>
			<div id="target_albums"></div>
		</div>
	</div>
</div>