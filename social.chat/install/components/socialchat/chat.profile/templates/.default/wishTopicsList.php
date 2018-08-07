<?php

$topics = isset($arResult['wishTopics']) && is_array($arResult['wishTopics']) ? $arResult['wishTopics'] : array();

$show = count($topics) ? true : false;

?>

<?php if($show){?>
<table id="wishTopicList" class="table table-bordered table-hovered">
	<!--tr>
		<th>#</th>
		<th>TITLE</th>
		<th>SORT</th>
		<th>SHOW</th>
		<th>actions</th>
	</tr-->
		<?php
			if(isset($topics) && is_array($topics)){
				foreach ($topics as $key => $t) {
					?>
					<tr>
						<!--td><?php echo $key + 1?></td-->
						<td><?php echo $t['T_TITLE']?></td>
						<!--td>
							<input type="number" value="<?php echo $t['SORT']?>" min="0" data-id="<?php echo $t['ID']?>" class="wish_sort_change">
						</td>
						<td><?php echo $t['SHOW_TOPIC'] ? "true" : "false"; ?></td-->
						<td>
							<a href="/chat?tid=<?php echo $t['T_ID']?>"><?php echo GetMessage("ALI_PROFILE_OPEN");?></a>
							&nbsp
							<a href="/chat/profile/?action=changewishshowtopic&wid=<?php echo $t['ID']?>&state=<?php echo $t['SHOW_TOPIC'] ? 0 : 1; ?>" class="btn-remove-wish"><?php echo $t['SHOW_TOPIC'] ? GetMessage("ALI_PROFILE_HIDE") : GetMessage("ALI_PROFILE_SHOW");?></a>
							<a href="/chat/profile/?action=removewish&wid=<?php echo $t['ID']?>" class="btn-remove-wish"><?php echo GetMessage("ALI_PROFILE_REMOVE");?></a>
						</td>
						
					</tr>
					<?php
				}
			}
		?>
</table>
<?php } ?>
	