<?php
use \Bitrix\Main\Localization\Loc;

$albums = isset($arResult['albums']) && count($arResult['albums']) ? $arResult['albums'] : array();
?>
<div class="row">
	<div class="col-xs-12">
		<?php
			if(count($albums)){
		?>
		<h3><?php echo Loc::getMessage('ALI_MODULE_YOUR_ALBUMS');?></h3>
		<h6><?php echo Loc::getMessage('ALI_MODULE_SELECT_FROM_ALBUM');?></h6>
		<ul>
		<?php	
			foreach ($albums as $k => $item) {
			?>
			<li>
				<div>
					<a href="/chat?action=openalbum" class="openalbum"  data-aid="<?php echo $item['ID']?>">
					<?php if($item['MAIN_IMAGE'] && file_exists(ALI_ALBUM_PATH."".$item['MAIN_IMAGE'])){?>
						<img src="<?php echo ALI_PUBLIC_ALBUM_PATH."small_".$item['MAIN_IMAGE']?>" class="album_ava" width="150px">
					<?php }else{ ?>
						<img src="<?php echo $componentPath?>/images/noimage.png" class="album_ava" width="150px">
					<?php } ?>
					</a>
				</div>
				<div>
					<p><?php echo $item['TITLE']?></p>
				</div>
			</li>
			<?php			
			}
		?>
		</ul>
		<?php
			}else{
				?>
				<div>
					<h3><?php echo Loc::getMessage('ALI_MODULE_NOT_HAVE_ALBUM')?></h3>
				</div>
				<?php
			}
		?>
	</div>
</div>