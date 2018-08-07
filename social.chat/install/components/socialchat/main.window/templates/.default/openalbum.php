<?php
use \Bitrix\Main\Localization\Loc;
?>
<div class="row">
	<div class="col-xs-12">
		<h2><?php echo Loc::getMessage('ALI_MODULE_ALBUM');?></h2>
		<a href="/chat?action=albumlist" class="loadalbums"><?php echo Loc::getMessage('ALI_MODULE_BACK_TO_ALBUMBS');?></a>
		<div id="albumshow">
			<ul>
			<?php
				foreach ($arResult['photos'] as $key => $photo) {
					if(!$photo['FILE_NAME'] || !file_exists(ALI_ALBUM_PATH."".$photo['FILE_NAME'])){
					continue;
				}
			?>
				<li>
					<div class="photo">
						<div class="photo-img stick-album-photo">
							<img src="<?php echo ALI_PUBLIC_ALBUM_PATH."".$photo['FILE_NAME']?>" width="150px">
						</div>
					</div>
				</li>
			<?php	
				}
			?>
			</ul>
		</div>
	</div>
</div>