<?php
use \Bitrix\Main\Localization\Loc;
?>

<div class="row">
	<div class="col-xs-12">
		<?php
			if(isset($arResult['hidden'])){
		?>
			<div class="row">
				<div class="col-xs-12">
					<div class="alert alert-danger">
						<?php echo Loc::getMessage('ALI_MODULE_PROFILE_HIDDEN');?>
					</div>
				</div>
			</div>
		<?php
			}elseif(isset($arResult['photos']) && is_array($arResult['photos']) && count($arResult['photos'])){
		?>
			<div class="row">
				<div class="col-xs-12">
					<h2><?php echo Loc::getMessage('ALI_MODULE_ALBUM');?></h2>
					<div id="albumshow">
						<ul>
						<?php
							foreach ($arResult['photos'] as $key => $photo) {
								if(!$photo['FILE_NAME'] || !file_exists(ALI_ALBUM_PATH."".$photo['FILE_NAME'])){
									continue;
								}

								$created = strtotime($photo['CREATED']);

						?>
							<li>
								<div class="photo">
									<div class="photo-img">
										<?php

										$time = 12 * 3600; // 12 часов 
										if(time() - $time <= $created){
											//фотки добавленные не ранее чем за 12 часа
											?>
											<a class="info_new"></a>
											<?php
										}
										?>
										<a rel="example_group" href="<?php echo ALI_PUBLIC_ALBUM_PATH."".$photo['FILE_NAME']?>">
											<img src="<?php echo ALI_PUBLIC_ALBUM_PATH."middle_".$photo['FILE_NAME']?>" class="img_alb">
										</a>
										<!-- <span><?php echo date("y-m-d H:i",$created)?></span> -->
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
		<?php
			}else{
		?>
			<div class="row">
				<div class="col-xs-12">
					<div class="alert alert-warning">
						<?php echo Loc::getMessage('ALI_MODULE_ALBUM_IS_EMPTY');?>
					</div>
				</div>
			</div>
		<?php
			}
		?>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$("a[rel=example_group]").fancybox({
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
	});
});
</script>