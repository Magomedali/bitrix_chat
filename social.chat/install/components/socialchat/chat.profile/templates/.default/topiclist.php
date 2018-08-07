<?php

$topics = isset($arResult['topics']) ? $arResult['topics'] : array();
$mbid = (int)$arResult['mbid'];

?>
<div id="profile_topics">
	<?php if(count($topics)){ ?>
	<table class="table table-bordered table-hovered">
		<?php
			foreach ($topics as $key => $t) {
				?>
				<tr>
					<td>
						<a href="/chat?tid=<?php echo $t['ID']?>"><?php echo $t['TITLE']?></a>
						<?php if(!$mbid){?>
						<div class="setting_topic">
							<a href="/chat/profile?action=newtopicform" data-tid="<?php echo $t['ID']?>" id="new_topic">
								<img src="/images/icon_edit.png" width="20">
							</a>
							<a href="/chat/profile?action=removetopic" data-tid="<?php echo $t['ID']?>" id="delete_topic">
								<img src="http://s1.iconbird.com/ico/2013/10/464/w512h5121380984637delete1.png" width="20">
							</a>
						</div>
						<?php } ?>
					</td>
				</tr>
				<?php
			}
		?>
	</table>
	<?php } ?>
</div>