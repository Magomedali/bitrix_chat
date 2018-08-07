<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/settings.php"); // site settings?>
<?
IncludeTemplateLangFile(__FILE__);
?>

<!DOCTYPE html>
<html lang="<?=LANGUAGE_ID?>">
	<head>
		<script>
			// var popupWin;
			// var chat_window;

			// var openChat = function(){
			// 	if(window.name !== "ali_chat_window"){
			// 		//var url = "<?php echo $_SERVER['REQUEST_URI']?>";
			// 		popupWin = window.open(window.location,'ali_chat_window','location,width=900,height=600,top=0');
						
			// 		popupWin.focus();
			// 		if(window.name !== "ali_chat_window_parent"){
			// 			popupWin.opener.name = "ali_chat_window_parent";
						
			// 		}else{
			// 			popupWin.close();

			// 			popupWin = window.open(window.location,'ali_chat_window','location,width=900,height=600,top=0');
						
			// 			popupWin.focus();
			// 		}
			// 		popupWin.opener.location = "/";
			// 	}	
			// }
			// openChat();
		</script>
		<meta charset="<?=SITE_CHARSET?>">
		<META NAME="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/png" href="<?=SITE_TEMPLATE_PATH?>/images/favicon.png" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<script type="text/javascript" src="/chat/template/js/jquery.js"></script>

		<?$APPLICATION->ShowHead();?>
		<?CJSCore::Init(array("fx"));?>
		<title><?$APPLICATION->ShowTitle();?></title>
		<link rel="stylesheet" type="text/css" href="/chat/template/css/bootstrap3.css">
		<link rel="stylesheet" type="text/css" href="/chat/template/css/ali_chat.css">
		<link rel="stylesheet" type="text/css" href="/chat/style/style.css"> 
		<script type="text/javascript" src="/chat/template/js/bootstrap3.js"></script>
		
		<script type="text/javascript" src="/chat/template/js/ali_chat.js"></script>
		
		
	</head>
<body class="loading <?if (INDEX_PAGE == "Y"):?>index<?endif;?><?if(!empty($TEMPLATE_PANELS_COLOR) && $TEMPLATE_PANELS_COLOR != "default"):?> panels_<?=$TEMPLATE_PANELS_COLOR?><?endif;?>">

	