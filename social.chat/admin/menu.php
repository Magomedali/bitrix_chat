<?php
IncludeModuleLangFile(__FILE__);

if(\Bitrix\Main\ModuleManager::isModuleInstalled('social.chat')){

  $aMenu = [
    "parent_menu" => "global_menu_content", // поместим в раздел "Сервис"
    "sort"        => 100,                    // вес пункта меню
    "url"         => "social.chat_chat_setting.php",  // ссылка на пункте меню
    "text"        => GetMessage("ADMIN_ALI_CHAT_MENU_TITLE"),       // текст пункта меню
    "title"       => GetMessage("ADMIN_ALI_CHAT_MENU_TITLE"), // текст всплывающей подсказки
   //"icon"        => "form_menu_icon", // малая иконка
   //"page_icon"   => "form_page_icon", // большая иконка
    "items_id"    => "menu_chat",  // идентификатор ветви
    "items"       => array(),          // остальные уровни меню сформируем ниже.
  ];

  $aMenu['items'][]=[
        "url" => "social.chat_topic_form.php",
        "text"        => GetMessage("ADMIN_ALI_CHAT_MENU_NEW_TOPIC"),
        "title"       => GetMessage("ADMIN_ALI_CHAT_MENU_NEW_TOPIC"),
  ];
}else{
  $aMenu = false;
}


return $aMenu;