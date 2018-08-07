<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use \Bitrix\Main\Localization\Loc;

$mbid = (int)$arResult['mbid'];
$member = $arResult['member'];
$memebr_name = $member['LAST_NAME'] || $member['NAME'] ? $member['LAST_NAME']." ".$member['NAME'] : "#".$member['ID'];

if(!$mbid){
	// $this->addExternalJS("/bitrix/components/socialchat/chat.profile/js/fileserver.js");
	$this->addExternalJS("/bitrix/components/socialchat/chat.profile/js/jquery.form.js");
	$this->addExternalJS("/bitrix/components/socialchat/chat.profile/js/profile.js");
}

$this->addExternalJS("/bitrix/components/socialchat/chat.profile/js/main.js");
$this->addExternalJS("/chat/js/fancybox/jquery.easing-1.3.pack.js");
$this->addExternalJS("/chat/js/fancybox/jquery.mousewheel-3.0.4.pack.js");
$this->addExternalJS("/chat/js/fancybox/jquery.fancybox-1.3.4.pack.js");

$this->addExternalCss("/bitrix/components/socialchat/chat.profile/css/profile.css");
$this->addExternalCss("/chat/js/fancybox/jquery.fancybox-1.3.4.css");

?>
<div id="chat_container">
	<div class="container">
	<div class="content_profil"> 
	<div class="row">
		<div class="col-xs-12">
			<h1><?php echo $mbid ? $memebr_name : GetMessage("ALI_PROFILE_TITLE");?></h1>
			<a href="/chat/" class="back_chat"><?php echo GetMessage("ALI_CHAT_LINK")?></a>
		</div>
	</div>
	<?php 
		if($mbid && $member['HIDE_PROFILE'] == 1){
	?>

	<div class="row">
		<div class="col-xs-6">
			<h3>User hide hisself profile</h3>
		</div>
	</div>

	<?php
		}else{
	?>
	<?php if(!$mbid){?>
		<div class="setting_profile">
			<p><a href="/chat/profile?action=formsettings" id="getformsettings"><?php echo Loc::getMessage('ALI_MODULE_SETTINGS');?></a></p>
		</div>
	<?php } ?>
	<div class="row">
		<div class="col-sm-6 col-xs-12">
			<h4><?php echo Loc::getMessage('ALI_MODULE_AVA');?></h4>
			<div class="pr_ava">
				<div>
					<?php
						if($member['AVA'] && file_exists(ALI_AVA_PATH.$member['AVA'])){
							?>
							<img src="<?php echo ALI_PUBLIC_AVA_PATH."small_".$member['AVA']?>" id="profile_ava">
							<?php
						}else{
							?>
							<img src="<?php echo $componentPath?>/images/noimage.png" id="profile_ava">
							<?php
						}
					?>
				</div>
			</div>		
		</div>
		<? /*
		<div class="col-sm-6 col-xs-12">
			<h4>Настройки</h4>
			<ul>
				<li><span><?php echo GetMessage('ALI_PROFILE_NAME');?>:</span><?php echo $member['NAME'];?></li>
				<li>
					<span><?php echo GetMessage('ALI_PROFILE_LAST_NAME');?>:</span>
					<?php echo $member['LAST_NAME'] ? $member['LAST_NAME'] : GetMessage('ALI_PROFILE_NO_POINTED');?>
				</li>
			</ul>	
		</div>
		*/?>
		<div class="col-sm-6 col-xs-12">
			<div class="posrel">
				<h4><?php echo Loc::getMessage('ALI_MODULE_TOPICS');?></h4>
				<?php if(!$mbid){?>
					<a href="/chat/profile?action=newtopicform" class="add_bl" id="new_topic"> + </a>
					<div id="new_topic_target"></div>
				<?php } ?>
			</div>
			<div id="profile_topics_target">
				<?php
					$this->getComponent()->includeComponentTemplate("topiclist");
				?>
			</div>
				
			<?php if(!$mbid){?>
				<div class="wish-topics">
					<?php $this->getComponent()->includeComponentTemplate("wishTopics");?>
				</div>
			<?php } ?>

		</div>
	</div>
	<hr> 
	<div class="row">
		<div class="col-lg-12 col-sm-6 col-xs-12">
			<div class="posrel">
				<h4><?php echo GetMessage('ALI_PHOTO_ALBUMS');?></h4>
				<?php if(!$mbid){?>
					<a href="/chat/profile?action=albumform" class="add_bl open_albumform"> + </a>
				<?php } ?>
			</div>
			
			<div id="profile_albums_target">
				<?php
					$this->getComponent()->includeComponentTemplate("albumlist");
				?>
			</div>
		</div> 
	</div>

		
	</div>
	<?php } ?>
	</div>
	</div>
</div>



<div id="modal-window">
	<div id="modal-blank">
		<div id="modal-close-cover"></div>
		<div id="modal-window-container">
			<div id="modal">
				<div id="modal-close-btn">X</div>
				<div id="modal-content"></div>
			</div>
		</div>
		<script type="text/javascript">
			$("body").on("click","#modal-close-cover,#modal-close-btn,.modal-close-btn",function(){
				$("#modal-window").hide();
			})
		</script>
	</div>
</div>
