<?php


use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);
Class social_chat extends CModule
{
    var $exclusionAdminFiles;
    var $errors = array();
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__."/version.php");

        $this->exclusionAdminFiles=array(
            '..',
            '.',
            'menu.php',
            'operation_description.php',
            'task_description.php',
            'top.php',
            'bottom.php',
        );

        $this->MODULE_ID = 'social.chat';
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("ALI_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("ALI_MODULE_DESC");

		$this->PARTNER_NAME = Loc::getMessage("ALI_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("ALI_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS='Y';
        $this->MODULE_GROUP_RIGHTS = "Y";
	}

    //Определяем место размещения модуля
    public function GetPath($notDocumentRoot=false)
    {
        
        if($notDocumentRoot){
            return "/bitrix/modules/".$this->MODULE_ID;
            //return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
        }else{
            return dirname(__DIR__);
        }
    }

    //Проверяем что система поддерживает D7
    public function isVersionD7()
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    function InstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        if(!Application::getConnection(\Social\Chat\MembersTable::getConnectionName())->isTableExists(
            Base::getInstance('\Social\Chat\MembersTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Social\Chat\MembersTable')->createDbTable();
            \Social\Chat\MembersTable::fullStartData();
        }

        if(!Application::getConnection(\Social\Chat\TopicTable::getConnectionName())->isTableExists(
            Base::getInstance('\Social\Chat\TopicTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Social\Chat\TopicTable')->createDbTable();
        }


        if(!Application::getConnection(\Social\Chat\MessagesTable::getConnectionName())->isTableExists(
            Base::getInstance('\Social\Chat\MessagesTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Social\Chat\MessagesTable')->createDbTable();
        }

        if(!Application::getConnection(\Social\Chat\PhotoalbumTable::getConnectionName())->isTableExists(
            Base::getInstance('\Social\Chat\PhotoalbumTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Social\Chat\PhotoalbumTable')->createDbTable();
        }


        if(!Application::getConnection(\Social\Chat\PhotoTable::getConnectionName())->isTableExists(
            Base::getInstance('\Social\Chat\PhotoTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Social\Chat\PhotoTable')->createDbTable();
        }


        if(!Application::getConnection(\Social\Chat\MembertopicsTable::getConnectionName())->isTableExists(
            Base::getInstance('\Social\Chat\MembertopicsTable')->getDBTableName()
            )
        )
        {
            Base::getInstance('\Social\Chat\MembertopicsTable')->createDbTable();
        }
        

        $this->installDbProgramming();

        return true;
    }

    function installDbProgramming(){

        global $DB;

        $file = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/mysql/get_new_msg.sql";
        $sql = file_get_contents($file);
        $DB->QueryLong($sql);

        $file = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/mysql/view_messages.sql";
        $sql = file_get_contents($file);
        $DB->QueryLong($sql);

        $file = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/mysql/get_last_msg.sql";
        $sql = file_get_contents($file);
        $DB->QueryLong($sql);

        $file = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/mysql/exists_new_msg.sql";
        $sql = file_get_contents($file);
        $DB->QueryLong($sql);

    }

    function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        Application::getConnection(\Social\Chat\MembersTable::getConnectionName())->
             queryExecute('drop table if exists '.Base::getInstance('\Social\Chat\MembersTable')->getDBTableName());

        Application::getConnection(\Social\Chat\TopicTable::getConnectionName())->
             queryExecute('drop table if exists '.Base::getInstance('\Social\Chat\TopicTable')->getDBTableName());

        Application::getConnection(\Social\Chat\MessagesTable::getConnectionName())->
             queryExecute('drop table if exists '.Base::getInstance('\Social\Chat\MessagesTable')->getDBTableName());

        Application::getConnection(\Social\Chat\PhotoTable::getConnectionName())->
             queryExecute('drop table if exists '.Base::getInstance('\Social\Chat\PhotoTable')->getDBTableName());

        Application::getConnection(\Social\Chat\PhotoalbumTable::getConnectionName())->
             queryExecute('drop table if exists '.Base::getInstance('\Social\Chat\PhotoalbumTable')->getDBTableName());

        Application::getConnection(\Social\Chat\MembertopicsTable::getConnectionName())->
             queryExecute('drop table if exists '.Base::getInstance('\Social\Chat\MembertopicsTable')->getDBTableName());
            
        global $DB, $DBType, $APPLICATION;

        $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/{$this->MODULE_ID}/install/db/mysql/uninstall.sql");
        
    }

	function InstallEvents()
	{

        
        // \Bitrix\Main\EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'TestEventD7', $this->MODULE_ID, '\Academy\D7\Event', 'eventHandler');
	}

	function UnInstallEvents()
	{
        //\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'TestEventD7', $this->MODULE_ID, '\Academy\D7\Event', 'eventHandler');
	}

	function InstallFiles($arParams = array())
	{
        $path=$this->GetPath()."/install/components";

        if(\Bitrix\Main\IO\Directory::isDirectoryExists($path))
            CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        else
            throw new \Bitrix\Main\IO\InvalidPathException($path);

        $pathPage = $this->GetPath()."/install/pages";
        if(\Bitrix\Main\IO\Directory::isDirectoryExists($pathPage))
            CopyDirFiles($pathPage, $_SERVER["DOCUMENT_ROOT"]."/", true, true);
        else
            throw new \Bitrix\Main\IO\InvalidPathException($pathPage);


        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin'))
        {
            CopyDirFiles($this->GetPath() . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"); //если есть файлы для копирования
            if ($dir = opendir($path))
            {
                while (false !== $item = readdir($dir))
                {
                    if (in_array($item,$this->exclusionAdminFiles))
                        continue;
                    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item,
                        '<'.'? require($_SERVER["DOCUMENT_ROOT"]."'.$this->GetPath(true).'/admin/'.$item.'");?'.'>');
                }
                closedir($dir);
            }
        }

        return true;
	}

	function UnInstallFiles()
	{
        
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/socialchat/');
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/chat/');

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/install/admin/', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
                    \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
        if($this->isVersionD7())
        {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

            #работа с .settings.php
            $configuration = Conf\Configuration::getInstance();
            $social_chat_module=$configuration->get('social_chat_module');
            $social_chat_module['install']=$social_chat_module['install']+1;
            $configuration->add('social_chat_module', $social_chat_module);
            $configuration->saveConfiguration();
            #работа с .settings.php
        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage("ALI_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("ALI_INSTALL_TITLE"), $this->GetPath()."/install/step.php");
	}

	function DoUninstall()
	{
       
        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request["step"]<2)
        {
            $APPLICATION->IncludeAdminFile(Loc::getMessage("ALI_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
        }
        elseif($request["step"]==2)
        {
            $this->UnInstallFiles();
			$this->UnInstallEvents();

            if($request["savedata"] != "Y")
                $this->UnInstallDB();

            \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

            #работа с .settings.php
            $configuration = Conf\Configuration::getInstance();
            $social_chat_module=$configuration->get('social_chat_module');
            $social_chat_module['uninstall']=$social_chat_module['uninstall']+1;
            $configuration->add('social_chat_module', $social_chat_module);
            $configuration->saveConfiguration();
            #работа с .settings.php

            $APPLICATION->IncludeAdminFile(Loc::getMessage("ALI_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
        }
	}

    function GetModuleRightList()
    {
        return array(
            "reference_id" => array("D","K","S","W"),
            "reference" => array(
                "[D] ".Loc::getMessage("ALI_DENIED"),
                "[K] ".Loc::getMessage("ALI_READ_COMPONENT"),
                "[S] ".Loc::getMessage("ALI_WRITE_SETTINGS"),
                "[W] ".Loc::getMessage("ALI_FULL"))
        );
    }
}
?>