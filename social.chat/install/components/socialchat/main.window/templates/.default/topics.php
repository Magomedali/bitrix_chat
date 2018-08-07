<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$topics = isset($arResult['topics']) ? $arResult['topics'] : null;

$cTopic = isset($arResult['c_topic']) ? $arResult['c_topic'] : null;

$main_topic_last_msg = isset($arResult['main_topic_last_msg']) ? $arResult['main_topic_last_msg'] : null;

?>

<div class="topics_menu_block">
		<ul class="topics_menu">
			<li class="topic_item <?php echo $arResult['active_topic'] == 0 ? 'topic_item_active' : '';?>" data-id="0" data-last="<?php echo $main_topic_last_msg?>">
				<a href="/chat/?action=open_topic&topic_id=0">Общая</a>
			</li>
			<?php if($cTopic && isset($cTopic['ID'])){
				$last_msg = isset($cTopic['LAST_MSG']) ? (int)$cTopic['LAST_MSG'] : 0;
			?>
			<li class="topic_item topic_item_active" data-id="<?php echo $cTopic['ID']?>" data-last="<?php echo $last_msg?>">
				<a href='/chat/?action=open_topic&topic_id=<?php echo $cTopic['ID']?>'><?php echo $cTopic['TITLE']?></a>
			</li>
			<?php } ?>
<?php
if(isset($topics) && is_array($topics)){
	foreach ($topics as $key => $t) {

		if(isset($cTopic['ID']) && $cTopic['ID'] == $t['TOPIC_ID']){
			continue;
		}

	 	$active_class = $t['ID'] == $arResult['active_topic'] ? "topic_item_active" : "";

	 	$last_msg = isset($t['LAST_MSG']) ? (int)$t['LAST_MSG'] : 0; 
?>
	<li class="topic_item <?php echo $active_class;?>" data-id="<?php echo $t['ID']?>" data-last="<?php echo $last_msg?>">
		<a href='/chat/?action=open_topic&topic_id=<?php echo $t['ID']?>'><?php echo $t['T_TITLE']?></a>
	</li>		
<?php
	}
}
?>
	</ul>
</div>