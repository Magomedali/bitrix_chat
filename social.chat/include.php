<?php

define("ALI_AVA_PATH", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/socialchat/main.window/files/ava/");
define("ALI_PUBLIC_AVA_PATH", "/bitrix/components/socialchat/main.window/files/ava/");

define("ALI_ALBUM_PATH", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/socialchat/main.window/files/album/");
define("ALI_PUBLIC_ALBUM_PATH", "/bitrix/components/socialchat/main.window/files/album/");

define("ALI_MSG_PATH", $_SERVER['DOCUMENT_ROOT']."/bitrix/components/socialchat/main.window/files/message/");
define("ALI_PUBLIC_MSG_PATH", "/bitrix/components/socialchat/main.window/files/message/");

//Количество сообщении на страницу(на загрузку)
define("MESSAGE_LIMIT", 15);

define("ALI_SMALL_IMAGE_SIZE",525);

define("ALI_MIDDLE_IMAGE_SIZE",550);

// AddEventHandler('main', 'OnBeforeProlog', 'CustomSetLastActivityDate');
// function CustomSetLastActivityDate() {	
if($GLOBALS['USER']->IsAuthorized()) {
    CUser::SetLastActivityDate($GLOBALS['USER']->GetID());
}
// }



class AliEvents{
	// создаем обработчик события "OnAfterUserRegister"
    function OnAfterUserRegisterHandler(&$arFields)
    {
        // если регистрация успешна то
        if($arFields["USER_ID"]>0)
        {	
        	try{
        		//Добавляем в таблицу members
	            \Social\Chat\MembersTable::addNewMember($arFields["USER_ID"]);
        	}catch(Exception $e){

        	}
            
        }
        return $arFields;
    }
}
// регистрируем обработчик события "OnAfterUserRegister"
RegisterModuleDependences("main", "OnAfterUserRegister", "social.chat", "AliEvents", "OnAfterUserRegisterHandler");