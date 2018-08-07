<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;
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


if(!CUser::IsAuthorized()){
	header("Location: /");
	exit;
}

$this->addExternalJS("/bitrix/components/socialchat/main.window/js/alichat.js");
$this->addExternalJS("/chat/js/slick/slick.min.js");
$this->addExternalJS("/chat/js/chat.js");

$this->addExternalCss("/bitrix/components/socialchat/main.window/css/alichat.css");
$this->addExternalCss("/chat/js/slick/slick.css");
$this->addExternalCss("/chat/style/style.css");
$this->addExternalCss("/chat/style/media.css");


$current_member_id = isset($arResult['current_member']) && isset($arResult['current_member']['ID']) ? $arResult['current_member']['ID'] : 0;
$user_id = isset($arResult['user_id']) ? $arResult['user_id'] : 0;

$current_member = isset($arResult['current_member']) ? $arResult['current_member'] : 0;
?>
<div id="chat_container">
<div class="row main_window ">
	<div class="container">
	<div class="row">
		<div class="col-xs-12 members">
			<?php
				$this->getComponent()->includeComponentTemplate("members");
			?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12">
			<div id="window" class="row">
				<div class="col-xs-12 window_chat">
					<div id="topics">
						<?php
							$this->getComponent()->includeComponentTemplate("topics");
						?>
					</div>
					<div class="messages-block">
						<div id="messages" class="messages">	
						<?php
							$this->getComponent()->includeComponentTemplate("messages");
						?>
						</div>
					</div>
					<div class="new-text-block">
						<?php if($current_member_id){?>
						<form action="<?php echo POST_FORM_ACTION_URI?>" method="POST" id="form-add-msg">
							<div id="message-box">
								<div>
									<!-- <textarea id="new_message" name="Message[text]" class="form-control" style=""></textarea> -->
									<div id="new_message" data-name="Message[text]" contenteditable></div>
								</div>
								<div id="message-box-smiles">
									<?php
										$this->getComponent()->includeComponentTemplate("smiles");
									?>
								</div>
								
							</div>
							<span id="send_to"><?php echo Loc::getMessage('ALI_MODULE_TO');?>: <span>&nbsp;</span></span>
							<div class="block_button_send">
								<input type="hidden" name="action" value="newmessage">
								<input type="hidden" id="new_message_topic" name="Message[topic_id]" value="<?php echo $arResult['active_topic']?>">
								<input type="hidden" name="Message[from]" value="<?php echo $current_member_id?>">
								<input type="hidden" name="Message[file]" id="message-file" value="">
								<input type="hidden" name="Message[file_from_album]" id="message-file_from_album" value="0">
								<input type="hidden" name="Message[to]" value="" id="new_message_to">
								<input type="hidden" name="user_id" value="<?php echo $user_id?>" id="current_user_id">
								<input type="submit" name="" id="btn-add-msg"  class="btn btn-primary" value="Отправить">
								
							</div>
						</form>

							<div id="message-box-file">
								<?php
									$this->getComponent()->includeComponentTemplate("filebox");
								?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
</div>
<?php
	$this->getComponent()->includeComponentTemplate("modal");
?>