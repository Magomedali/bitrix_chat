<?php
use \Bitrix\Main\Localization\Loc;
$albums = isset($arResult['albums']) ? $arResult['albums'] : null;
$mbid = (int)$arResult['mbid'];


?>
<div id="profile_albums">
	<?php if($albums && is_array($albums)){?>
		<?php
			foreach ($albums as $key => $item) {

				$updated = strtotime($item['UPDATED']);
				?>
				<div class="col-lg-3 col-sm-6 alb" style="padding-left: 0">
					<div>
						<?php if(!$mbid){?>
						<div class="red_alb">
							<a href="/chat/profile?action=albumform&album_id=<?php echo $item['ID']?>" class="open_albumform">
								<img src="/images/icon_edit.png" width="20"> 
							</a>
							<!--a class="info_new"></a--> 
						</div>
						<?php } ?>
						<div>
							<?php
								$time = 12 * 3600; // 12 часов 
								if(time() - $time <= $updated){
								//фотки добавленные не ранее чем за 12 часа
							?>
								<a class="info_new"></a>
							<?php
								}
							?>

							<a href="/chat/profile?action=showalbum" class="showalbum" data-mbid="<?php echo $item['OWNER_ID']?>" data-aid="<?php echo $item['ID']?>">
							<?php if($item['MAIN_IMAGE'] && file_exists(ALI_ALBUM_PATH."".$item['MAIN_IMAGE'])){?>
							<img src="<?php echo ALI_PUBLIC_ALBUM_PATH."small_".$item['MAIN_IMAGE']?>" class="album_ava">
							<?php }else{ ?>
							<img src="<?php echo $componentPath?>/images/noimage.png" class="album_ava">
							<?php } ?>
							</a>
						</div>
						<div>
							<p class="album_title"><?php echo $item['TITLE']?></p>
						</div>
					</div>
				</div>
				<?php
			}
		?>
	<?php } ?>
</div>