<?php


if(!defined(ALI_AVA_PATH)){
	define("ALI_AVA_PATH", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/socialchat/main.window/files/ava/");
}

if(!defined(ALI_PUBLIC_AVA_PATH)){
	define("ALI_PUBLIC_AVA_PATH", "/bitrix/components/socialchat/main.window/files/ava/");
}

if(!defined(ALI_ALBUM_PATH)){
	define("ALI_ALBUM_PATH", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/socialchat/main.window/files/album/");
}

if(!defined(ALI_PUBLIC_ALBUM_PATH)){
	define("ALI_PUBLIC_ALBUM_PATH", "/bitrix/components/socialchat/main.window/files/album/");
}


if(!defined(ALI_MSG_PATH)){
	define("ALI_MSG_PATH", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/socialchat/main.window/files/message/");
}


if(!defined(ALI_PUBLIC_MSG_PATH)){
	define("ALI_PUBLIC_MSG_PATH", "/bitrix/components/socialchat/main.window/files/message/");
}


$messages = isset($arResult['messages']) ? $arResult['messages'] : null;
$previous = isset($arResult['previous']) ? 1 : 0;
$user_id = isset($arResult['user_id']) ? $arResult['user_id'] : 0;

$isAdmin = isset($USER) && is_object($USER) ? $USER->isAdmin() : false;

$componentPath = !isset($componentPath) ? "/bitrix/components/socialchat/main.window" : $componentPath;



if(isset($messages) && is_array($messages)){
	$messages = array_reverse($messages);
	foreach ($messages as $key => $msg) {

		$notMyMsg = $msg['FROM_MEMBER_USER_ID'] != $user_id; 

	 	$class = !$notMyMsg ? "right" : "left";

	 	$m_from = $msg['FROM_MEMBER_USER_LAST_NAME'] || $msg['FROM_MEMBER_USER_NAME'] ? $msg['FROM_MEMBER_USER_LAST_NAME']." ".$msg['FROM_MEMBER_USER_NAME'] : "USER_#".$msg['FROM_ID'];

	 	$m_to = $msg['TO_ID'] || $msg['TO_MEMBER_USER_NAME'] ? $msg['TO_MEMBER_USER_LAST_NAME']." ".$msg['TO_MEMBER_USER_NAME'] : null;

	 	if($msg['FROM_MEMBER_AVA'] && file_exists(ALI_AVA_PATH.$msg['FROM_MEMBER_AVA'])){
			$linkAvaFrom = ALI_PUBLIC_AVA_PATH.$msg['FROM_MEMBER_AVA'];
		}else{
			$linkAvaFrom = $componentPath."/images/noimage.png";
		}


		if($msg['TO_MEMBER_AVA'] && file_exists(ALI_AVA_PATH.$msg['TO_MEMBER_AVA'])){
			$linkAvaTo = ALI_PUBLIC_AVA_PATH.$msg['TO_MEMBER_AVA'];
		}else{
			$linkAvaTo = $componentPath."/images/noimage.png";
		}
		
?>
		<div id="msg<?php echo $msg['ID']?>" data-id="<?php echo $msg['ID']?>" class="message <?php echo $class?>">
			<div class="message_item">
				<div class="m_ava"></div>
				<div class="m_data">
					<div class="m_text_block">
						<span class="m_from">
							
							<?php if($notMyMsg){?>
								<a href="/chat/profile?mbid=<?php echo $msg['FROM_ID']?>" class="set_member_to" data-member-id="<?php echo $msg['FROM_ID']?>">
									<img src="<?echo $linkAvaFrom?>" title="<?php echo $m_from?>" />
									<span style="display: none;"><?php echo $m_from?></span>
								</a>
								<span class="span_set_member_to"> >> </span>
							<?php }else{
								?>
								<img src="<? echo $linkAvaFrom; ?>"  title="<?php echo $m_from?>" />
								<span style="display: none;"><?php echo $m_from?></span> 
								<?php
							}?>
							<?php if($m_to){?>
								<img src="/chat/style/images/say.png" class="sayto">
								<a href="/chat/profile?mbid=<?php echo $msg['TO_ID']?>" class="set_member_to" data-member-id="<?php echo $msg['TO_ID']?>">
									<img src="<? echo $linkAvaTo; ?>" title="<?php echo $m_to?>" />
									<span style="display: none;"><?php echo $m_to?></span> 
								</a>
								<span class="span_set_member_to"> >> </span>
							<?php } ?>	
						</span>
						<div class="msgtext">
							<?php
								if($msg['FILE_NAME']){
									if(file_exists($_SERVER['DOCUMENT_ROOT'].$msg['FILE_NAME'])){
										$pathParts = explode("/", $msg['FILE_NAME']);
										$pathParts = array_reverse($pathParts);
										$fName = $pathParts[0];
										if((int)$msg['FILE_FROM_FIELD']){
											$file_path = ALI_PUBLIC_ALBUM_PATH . "middle_" . $fName;
										}else{
											$file_path = ALI_PUBLIC_MSG_PATH . "middle_" . $fName;
										}
										

										if(file_exists($_SERVER['DOCUMENT_ROOT'].$file_path)){
									?>
											<span class="msg-file"><img src="<?php echo $file_path;?>"></span>
									<?php
										}

									}else{
									?>
										<span class="no-image">image not found</span>
									<?php
									}
								}
							?>

							<div><?php echo $msg['TEXT']?></div>
								
						</div>
						<span class="m_date"><?php echo date("d.m.Y H:i",strtotime($msg['CREATED']))?></span>
						<?php 
							if($isAdmin === true){
								?>
								<p>
									<a href="/chat?action=removemsg" data-id="<?php echo $msg['ID']; ?>" class="btn-removemsg">Удалить</a>
								</p>
								<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>


<?php
	}

	if($previous && count($messages)){
	?>
		<!-- p class="previous_messages" style="text-align: center;">Предыдущие сообщения</p -->
	<?php
	}
}
?>