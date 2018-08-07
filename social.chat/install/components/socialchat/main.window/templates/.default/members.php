<?php

use \Bitrix\Main\Localization\Loc;

$current_member = isset($arResult['current_member']) ? $arResult['current_member'] : 0;


?>
		<div class="overlay"></div>
		<div class="current_member">
			<div class="col-xs-8 ">
				<p>
					<a href="/chat/profile" data-id="<?php echo $USER->GetID();?>">
						<?php if($current_member['AVA'] && file_exists(ALI_AVA_PATH.$current_member['AVA'])){
							$linkAva = ALI_PUBLIC_AVA_PATH.$current_member['AVA'];
						}else{
							$linkAva = $componentPath."/images/noimage.png";
						}?>
						<img src="<?php echo $linkAva?>" class="useronline">
						
						<span><?php echo $USER->getParam("NAME")?></span>
					</a>
				</p>
			</div>
			<div class="col-xs-4">
				<?php
					$this->getComponent()->includeComponentTemplate("search");
				?>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="#" class="view_all_members"><?php echo Loc::getMessage('VIEW_ALL_MEMBERS');?></a>
				</div>
			</div>
		</div>
		<div id="chat_members_list">
		<?php
			if(isset($arResult['users']) && is_array($arResult['users'])){
				foreach ($arResult['users'] as $key => $user) {
					if($USER->GetID() == $user['USER_ID']){
						continue;
					}

					$user_name = $user['LAST_NAME']." ".$user['NAME'];
					
					?>
					<div class="member_item <? echo ($user['IS_ONLINE'] === "Y") ? 'online_user': ''; ?>">
						<a href="/chat/profile?mbid=<?php echo $user['ID']?>" class="set_member_to" data-member-id="<?php echo $user['ID']?>">
							
							<?php if($user['AVA'] && file_exists(ALI_AVA_PATH.$user['AVA'])){
								$linkAva = ALI_PUBLIC_AVA_PATH.$user['AVA'];
							}else{
								$linkAva = $componentPath."/images/noimage.png";
							}?>

							<img src="<?php echo $linkAva?>" style="border-radius: 100px;width: 50px;height: 50px;">

							<span class="name_member"><?php echo strlen($user_name) > 1 ? $user_name : "user#".$user['ID']?></span>
						</a>
						<span class="span_set_member_to" data-member-id="<?php echo $user['ID']?>"></span>
					</div>
					<?php
				}
			}
		?>
		</div>
		<div id="all_members">
		<?php
			if(isset($arResult['users']) && is_array($arResult['users'])){
				foreach ($arResult['users'] as $key => $user) {
					if($USER->GetID() == $user['USER_ID']){
						continue;
					}

					$user_name = $user['LAST_NAME']." ".$user['NAME'];
					
					?>
					<div class="all_member_item <? echo ($user['IS_ONLINE'] === "Y") ? 'online_user': ''; ?>">
						<a href="/chat/profile?mbid=<?php echo $user['ID']?>" class="all_set_member_to" data-member-id="<?php echo $user['ID']?>">
							
							<?php if($user['AVA'] && file_exists(ALI_AVA_PATH.$user['AVA'])){
								$linkAva = ALI_PUBLIC_AVA_PATH.$user['AVA'];
							}else{
								$linkAva = $componentPath."/images/noimage.png";
							}?>

							<img src="<?php echo $linkAva?>" style="border-radius: 100px;width: 50px;height: 50px;">

							<span class="all_name_member"><?php echo strlen($user_name) > 1 ? $user_name : "user#".$user['ID']?></span>
						</a>
						<span class="all_span_set_member_to" data-member-id="<?php echo $user['ID']?>"></span>
					</div>
					<?php
				}
			}
		?>
		</div>