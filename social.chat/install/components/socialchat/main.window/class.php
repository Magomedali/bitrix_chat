<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserUtils;
use Bitrix\Main\UserTable;
use Social\Chat\MembersTable;
use Social\Chat\MessagesTable;
use Social\Chat\TopicTable;
use Social\Chat\PhotoTable;
use Social\Chat\PhotoalbumTable;
use Social\Chat\MembertopicsTable;

use \Bitrix\Main\Application;


class ChatWindow extends CBitrixComponent
{
  

    protected function checkModules()
    {
        if (!Loader::includeModule('social.chat'))
        {
            ShowError(Loc::getMessage('ALI_MODULE_NOT_INSTALLED'));
            return false;
        }

        return true;
    }


    public function executeAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if(isset($request['action'])){

            $action = strtolower(trim(strip_tags($request['action'])))."Action";

            if(method_exists($this, $action)){

                return $this->$action();

            }else{
                return $this->defaultAction();
            }

        }else{
            return $this->defaultAction();
        }
    }




    public function defaultAction(){
        
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $a_topic = isset($request['tid']) ? $request['tid'] : 0;

        $users = MembersTable::getMembers();
        $messages = MessagesTable::getMessagesByTopic($a_topic,MESSAGE_LIMIT);
        
        //$topics = TopicTable::getThemesbyOwner();
        
        //print_r($topics);
        $main_topic_last_msg = TopicTable::getThemeLastMsg(0);
        
        if($a_topic){
            $c_topic = TopicTable::getTheme($a_topic);

            $this->arResult['c_topic'] = $c_topic;
        }

        $id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);
        
        $topics = array();
        if(isset($member['ID']) && $member['ID']){
            $topics = MembertopicsTable::getMemberShowTopics($member['ID']);
        }
        
        

        $this->arResult['users'] = $users;
        $this->arResult['messages'] = $messages;
        $this->arResult['topics'] = $topics;
        $this->arResult['main_topic_last_msg'] =  $main_topic_last_msg;
        $this->arResult['active_topic'] = $a_topic;
        $this->arResult['current_member'] = $member;
        $this->arResult['user_id'] = $id;

        return $this->arResult;
    }

    /**
    * return $template for rendering;
    */
    public function searchAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request->isAjaxRequest()){
            
            $key = isset($request['key']) && trim(strip_tags($request['key'])) != "" ? trim(strip_tags($request['key'])) : "";
            
            if(strlen($key) >= 3){
                $members = MembersTable::getMembersByKey($key);
            }else{
                $members = array();
            }
            
            $this->arResult['key'] = $key;
            $this->arResult['users'] = $members;

            $template = "searchResult";
            return $template;
        }else{
           $this->defaultAction();
        }
    }





    /**
    * return $template for rendering;
    */
    public function open_topicAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request->isAjaxRequest()){
            
            $a_topic = isset($request['topic_id']) ? (int)$request['topic_id'] : 0;
            
            $messages = MessagesTable::getMessagesByTopic($a_topic,MESSAGE_LIMIT);
            
            $this->arResult['messages'] = $messages;
            $this->arResult['user_id'] = CUser::GetID();

            $template = "messages";
            return $template;
        }else{
           $this->defaultAction();
        }
    }


    /**
    * removing messages;
    */
    public function removemsgAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

                    
        if(CUser::GetParam("ADMIN")){

            if(isset($request['mid'])){
                $mid = (int)$request['mid'];

                $model =  MessagesTable::getRowById($mid);

                if(isset($model['ID']) && isset($model['TOPIC_ID'])){
                    $a_topic = (int)$model['TOPIC_ID'];

                    if(isset($model['FILE_NAME']) && trim($model['FILE_NAME']) != "" && (int)$model['FILE_FROM_FIELD'] != 1){
                        MessagesTable::removeFile($model['FILE_NAME']);
                    }
                    MessagesTable::delete($mid);

                    
                }
            }
        }

        if($request->isAjaxRequest()){

            $a_topic = isset($a_topic) ? $a_topic : 0;

            $messages = MessagesTable::getMessagesByTopic($a_topic,MESSAGE_LIMIT);
            $this->arResult['messages'] = $messages;
            $this->arResult['user_id'] = CUser::GetID();

            $template = "messages";
            return $template;
        }else{
            LocalRedirect("/chat/");
        }

        
    }











    /**
    * return $template for rendering;
    */
    public function newmessageAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $user_id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($user_id);

        if(isset($member['ID']) && (int)$member['ID']){
            if($request->isPost() && isset($request['Message'])){
                $msg['Message'] = $request['Message'];

                $msg['Message']['from'] = (int)$member['ID'];

                $model = new MessagesTable();
                if($model->save($msg)){

                }else{

                }
            }
        }
        
        //$req = new \Bitrix\Main\Request;
        if($request->isAjaxRequest()){

            $a_topic = isset($request['Message']) && isset($request['Message']['topic_id']) ? (int)$request['Message']['topic_id'] : 0;

            $messages = MessagesTable::getMessagesByTopic($a_topic,MESSAGE_LIMIT);
            $this->arResult['messages'] = $messages;
            $this->arResult['user_id'] = CUser::GetID();

            $template = "messages";
            return $template;
        }else{
            LocalRedirect("/chat/");
        }
    }


    public function filemessageAction(){
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

       
        $this->arResult['loadava'] = 'yesyes';

        $files = $_FILES;
        $this->arResult['files'] = $files;
        if(isset($files['message_file'])){
            $user_id = CUser::GetID();
            $img = MessagesTable::saveTempFile($user_id,$files['message_file']);
            $this->arResult['res'] = $img && 1;

            if($img && file_exists(ALI_MSG_PATH.$img)){
                 $this->arResult['img_path'] = ALI_PUBLIC_MSG_PATH.$img;
                 $this->arResult['img_name'] = $img;
            }

        }else{
            $this->arResult['res'] = 0;
        }
        
        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat");
        }
    }


    public function cleartempfileAction(){
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $user_id = CUser::GetID();
        if(isset($request['tmpfile']) && $user_id){
            
            $pos = strripos($request['tmpfile'], ALI_PUBLIC_ALBUM_PATH);
            //не даем удалять файл из альбомов
            if($pos !== false){
                $this->arResult['res'] = 1;
                $this->arResult['error'] = "try to delete photo from album";
            }else{
                $img = $_SERVER['DOCUMENT_ROOT'].$request['tmpfile'];

                if($img && file_exists($img)){
                    unlink($img);
                    $this->arResult['res'] = 1;
                }
            }   
            

        }else{
            $this->arResult['res'] = 0;
        }
        
        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat");
        }
    }



    public function albumlistAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $user_id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($user_id);
        if(isset($member['ID']) && (int)$member['ID']){
            $mid = (int)$member['ID'];

            $this->arResult['member'] = $member;
            $this->arResult['albums'] = PhotoalbumTable::getAlbumsByOwner($mid);

            return "albumlist";
        }

        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat");
        }
    }


    public function openalbumAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $user_id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($user_id);

        $aid = isset($request['aid']) ? (int)$request['aid'] : 0;

        if($aid && isset($member['ID']) && (int)$member['ID']){
            $mid = (int)$member['ID'];

            $this->arResult['member'] = $member;
            $this->arResult['photos'] = PhotoTable::getPhotosByAlbumAndMember($aid,$mid);

            return "openalbum";
        }

        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat");
        }
    }


    public function previousmessageAction(){
        
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $user_id = CUser::GetID();
        $topic = isset($request['topic']) ? (int)$request['topic'] : null;
        $offset = isset($request['offset']) ? (int)$request['offset'] : 0;
        if($user_id && $topic !== null){

            $messages = MessagesTable::getMessagesByTopic($topic,MESSAGE_LIMIT,$offset);
            $this->arResult['messages'] = $messages;
            $this->arResult['previous'] = 1;
            $this->arResult['user_id'] = CUser::GetID();
            
            $template = "messages";
            return $template;
        }

        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat");
        }
    }


    public function executeComponent()
    {
        $this->includeComponentLang('class.php');

        if($this->checkModules())
        {   

            $template = $this->executeAction();

            $context = Application::getInstance()->getContext();
            $request = $context->getRequest();
            if($request->isAjaxRequest()){
                
                // $this->returnAjaxJsonResult();
                if($template){
                    $this->returnAjaxHtmlResult($template);
                }else{
                    $this->returnAjaxJsonResult();
                }
            }else{
                $this->includeComponentTemplate();
            }
        }
    }



    protected function returnAjaxJsonResult(){

        $jsonData = \Bitrix\Main\Web\Json::encode($this->arResult);
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        echo $jsonData; 
        exit;
    }


    protected function returnAjaxHtmlResult($html_page=""){
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        $this->includeComponentTemplate($html_page);
        exit;
    }
};