<?php

use \Bitrix\Main\Localization\Loc;
$member = isset($arResult['member'])? $arResult['member'] : null;

?>
<?php if(isset($member['ID']) && $member['ID']){?>
<div class="row">
	<div class="col-xs-12">
		<h2><?php echo Loc::getMessage('ALI_MODULE_PROFILE_SETTINGS');?></h2>
	</div>
	<div class="col-xs-6">
		<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST">
			<input type="hidden" name="action" value="savesettings">
			<input type="hidden" name="member_id" value="<?php echo $member['ID']?>">
			<div class="row">
				<div class="col-xs-6">
					<p>
						<label for="user-name"><?php echo Loc::getMessage('ALI_MODULE_NAME');?></label>
						<input type="text" name="User[NAME]" id="user-name" value="<?php echo $member['NAME']?>" class="form-control" required>
					</p>
					<p>
						<label for="user-lastname"><?php echo Loc::getMessage('ALI_MODULE_LAST_NAME');?></label>
						<input type="text" name="User[LAST_NAME]" value="<?php echo $member['LAST_NAME']?>" id="user-lastname" class="form-control" required>
					</p>
					<p>
						<label for="member-hideprofile"><?php echo Loc::getMessage('ALI_MODULE_HIDE_PROFILE');?></label>
						<input type="checkbox" name="Member[HIDE_PROFILE]" value="1" id="member-hideprofile" <?php echo isset($member['HIDE_PROFILE']) && (int)$member['HIDE_PROFILE'] ? "checked" :""?>>
					</p>
					<input type="submit" value="<?php echo Loc::getMessage('ALI_MODULE_SAVE');?>" class="btn btn-success">
				</div>
			</div>
		</form>
	</div>
	<div class="col-xs-6">
		<div id="message_block"></div>
		<?php if(!$mbid){?>
		<form action="<?php echo POST_FORM_ACTION_URI;?>?action=loadava"  enctype="multipart/form-data" method="POST" id="uploadfile">
			<input type="hidden" name="action" value="loadava">
			<input type="file" name="ava" size="27" id="field-ava">
			<?php //echo CFile::InputFile("IMAGE_ID", 20, null);?>
			<input type="submit" id="change_ava" class="btn btn-success" value="<?php echo Loc::getMessage('ALI_MODULE_AVA_SETTINGS');?>">
		</form>
		<?php } ?>
	</div>
</div>
<?php } ?>