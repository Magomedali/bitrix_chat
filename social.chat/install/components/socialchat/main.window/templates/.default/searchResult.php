<?php
use \Bitrix\Main\Localization\Loc;
$users = $arResult['users'];

$show = count($users) ? true : false;

?>
<div id="searchResult_items">
	

<?php if($show){?>
<ul id="chat_members_list_search">
		<?php
			if(isset($arResult['users']) && is_array($arResult['users'])){
				foreach ($arResult['users'] as $key => $user) {
					$user_name = $user['LAST_NAME']." ".$user['NAME'];
					?>
					<li class="<? echo ($user['IS_ONLINE'] === "Y") ? 'online_user': ''; ?>">
						<a href="/chat/profile?mbid=<?php echo $user['ID']?>">
							
							<?php if($user['AVA'] && file_exists(ALI_AVA_PATH.$user['AVA'])){
								$linkAva = ALI_PUBLIC_AVA_PATH.$user['AVA'];
							}else{
								$linkAva = $componentPath."/images/noimage.png";
							}?>
							<img src="<?php echo $linkAva?>" style="border-radius: 100px;width: 50px;height: 50px;">

							<span class="name_member"><?php echo strlen($user_name) > 1 ? $user_name : "user#".$user['ID']?></span>
						</a>
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