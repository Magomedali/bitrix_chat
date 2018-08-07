<?php
use \Bitrix\Main\Localization\Loc;

$topics = $arResult['topics'];

$show = count($topics) ? true : false;

?>
<div id="searchResult_items">
	

<?php if($show){?>
<ul id="chat_topics_list_search">
		<?php
			if(isset($topics) && is_array($topics)){
				foreach ($topics as $key => $t) {
					
					?>
					<li class="">
						<?php echo $t['TITLE']?>
						<a href="/chat?tid=<?php echo $t['ID']?>"><?php echo Loc::getMessage('ALI_MODULE_OPEN');?></a>
						&nbsp
						&nbsp
						<a href="/chat/profile/?action=addwish&tid=<?php echo $t['ID']?>" class="btn-add-wish"><?php echo Loc::getMessage('ALI_MODULE_ADD_WISH');?></a>
					</li>
					<?php
				}
			}
		?>
</ul>
<?php }else{
	?>
	<li><?php echo Loc::getMessage('ALI_MODULE_NO_RESULTS');?></li>
	<?php
}?>
</div>