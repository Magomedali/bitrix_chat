<?php
use \Bitrix\Main\Localization\Loc;
$album_id = isset($arResult['album_id']) ? (int)$arResult['album_id'] : null;

if($album_id){
?>

<div class="row">
	<div class="col-xs-12">
		<div class="alert alert-danger">
			<?php echo Loc::getMessage('ALI_MODULE_WARNING_REMOVE_ALBUM');?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST">
			<input type="hidden" name="action" value="acceptremovingalbum">
			<input type="hidden" name="album_id" value="<?php echo $album_id?>">
			<a class="modal-close-btn btn btn-primary"><?php echo Loc::getMessage('ALI_MODULE_CANSEL');?></a>
			<input type="submit" value="<?php echo Loc::getMessage('ALI_MODULE_REMOVE');?>" class="btn btn-danger">
		</form>
	</div>
</div>

<?php } ?>