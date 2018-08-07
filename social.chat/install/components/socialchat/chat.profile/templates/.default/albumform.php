<?php
use \Bitrix\Main\Localization\Loc;

$album_id = isset($arResult['album_id']) ? (int)$arResult['album_id'] : null;
$album = isset($arResult['album']) ? $arResult['album'] : null;
$photos = isset($arResult['photos']) ? $arResult['photos'] : null;
?>

<div class="row">
	<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6">
					<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST" enctype="multipart/form-data" id="uploadAlbumPhotos">
					<input type="hidden" name="action" value="processalbumform">
					<?php
						if($album_id){
					?>
						<input type="hidden" name="album_id" value="<?php echo $album_id?>">
					<?php } ?>
					<h3>
						<?php echo Loc::getMessage('ALI_MODULE_ALBUM');?>
						<input type="submit" value="<?php echo Loc::getMessage('ALI_MODULE_SAVE');?>" class="btn btn-success">
					</h3>
					<p>
						<label for="album-title"><?php echo Loc::getMessage('ALI_MODULE_ALBUM_TITLE');?></label>
						<input type="text" name="Album[TITLE]" class="form-control" id="album-title" value="<?php echo $album ? $album['TITLE'] :''?>" required>
					</p>
					<p>
						<?php if($album && $album['MAIN_IMAGE'] && file_exists(ALI_ALBUM_PATH."".$album['MAIN_IMAGE'])){?>
							<img src="<?php echo ALI_PUBLIC_ALBUM_PATH.$album['MAIN_IMAGE']?>" class="album_ava" width="150px">
							<?php }else{ ?>
							<img src="<?php echo $componentPath?>/images/noimage.png" class="album_ava" width="150px">
						<?php } ?>
					</p>
					<p>
						<label for="album-main-file"><?php echo Loc::getMessage('ALI_MODULE_ALBUM_MAIN_IMAGE');?></label>
						<input type="file" name="album_main_photo" id="album-main-file">	
					</p>
					<p>
						<label for="album-photos"><?php echo Loc::getMessage('ALI_MODULE_ALBUM_IMAGES');?></label>
						<input type="file" name="album_photos[]" id="album-photos" multiple>	
					</p>
					</form>
				</div>
				<div class="col-xs-6">
					<?php if($album_id){?>
						<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST" id="formremovealbum">
							<input type="hidden" name="action" value="albumremoveform">
							<input type="hidden" name="album_id" value="<?php echo $album_id?>">
							<input type="submit" value="<?php echo Loc::getMessage('ALI_MODULE_DELETE');?>" class="btn btn-danger">
						</form>
					<?php } ?>
				</div>
			</div>
		
	</div>
	<?php if($photos && is_array($photos)){?>
	<div class="col-xs-12">
		<div class="row">
			<div class="col-xs-12">
				<ul class="edit_list_album">
				<?php foreach ($photos as $k => $p) { 

					if(!$p['FILE_NAME'] || !file_exists(ALI_ALBUM_PATH."".$p['FILE_NAME'])){
						continue;
					}
				?>
					<li class="photo_item">
						<div class="photo_item_block">
							<img src="<?php echo ALI_PUBLIC_ALBUM_PATH.$p['FILE_NAME']?>" width="150px">
							<a href="/chat/profile?action=removephoto&photo_id=<?php echo $p['ID']?>" data-id="<?php echo $p['ID']?>" class='photo_delete'>x </a>
						</div>
					</li>
				<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<?php } ?>
</div>