<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserUtils;
use Bitrix\Main\UserTable;
use Social\Chat\MembersTable;
use Social\Chat\MessagesTable;
use Social\Chat\TopicTable;
use Social\Chat\PhotoalbumTable;
use Social\Chat\PhotoTable;
use Social\Chat\MembertopicsTable;

use \Bitrix\Main\Application;
use Bitrix\Main\Entity\Result;


class ChatProfile extends CBitrixComponent
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

        $mbid = isset($request['mbid']) ? (int)$request['mbid'] : 0;

        if($mbid){
          $memeber = MembersTable::getMember($mbid);
          $topics = TopicTable::getThemesbyOwner($mbid);
          
        }else{
            $id = CUser::GetID();
            $memeber = MembersTable::getMemberByUserId($id);
          
            $topics = isset($memeber['ID']) ? TopicTable::getThemesbyOwner($memeber['ID']) : array(); 
            $wishTopics = MembertopicsTable::getMemberWish($memeber['ID']);
        }
        
        $albums = isset($memeber['ID']) ? PhotoalbumTable::getAlbumsByOwner($memeber['ID']) : array();

        $this->arResult['mbid'] = $mbid;
        $this->arResult['member']=$memeber;
        $this->arResult['topics']=$topics;
        
        $this->arResult['wishTopics'] = $wishTopics;
        
        $this->arResult['albums']=$albums;

        return $this->arResult;
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


    public function addwishAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);

        $mbid = null;
        if(isset($member['ID']) && (int)$member['ID']){
            $mbid = (int)$member['ID'];
        }

        $tid = null;
        if(isset($request['tid']) && (int)$request['tid']){
            $tid = (int)$request['tid'];
            $topic = TopicTable::getRowById($tid);

            $tid = isset($topic['ID']) && (int)$topic['ID'] ? $topic['ID'] : null;
        }
            
        if($tid && $mbid){

            $res = MembertopicsTable::save($mbid,$tid);
        }  

        if($request->isAjaxRequest()){
            
            
            if($mbid){
                $wishTopics = MembertopicsTable::getMemberWish($mbid);
            }else{
                $wishTopics = array();
            }
            
            $this->arResult['wishTopics'] = $wishTopics;

            $template = "wishTopicsList";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }
    }


    public function changewishshowtopicAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);

        $mbid = null;
        if(isset($member['ID']) && (int)$member['ID']){
            $mbid = (int)$member['ID'];
        }

        $wid = null;
        if(isset($request['wid']) && (int)$request['wid']){
            $wid = (int)$request['wid'];
        }
            
        if($mbid && $wid){

            $state = isset($request['state']) && (int)$request['state'] ? 1 : 0;
            if($state){
                MembertopicsTable::show($mbid,$wid);
            }else{
                MembertopicsTable::hide($mbid,$wid);
            }
        }  

        if($request->isAjaxRequest()){
            
            
            if($mbid){
                $wishTopics = MembertopicsTable::getMemberWish($mbid);
            }else{
                $wishTopics = array();
            }
            
            $this->arResult['wishTopics'] = $wishTopics;

            $template = "wishTopicsList";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }
    }




    
    public function changesortwishAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);

        $mbid = null;
        if(isset($member['ID']) && (int)$member['ID']){
            $mbid = (int)$member['ID'];
        }

        $wid = null;
        if(isset($request['wish']) && (int)$request['wish']){
            $wid = (int)$request['wish'];
        }
            
        if($mbid && $wid){

            $value = isset($request['value']) && (int)$request['value'] ? (int)$request['value'] : 0;
            
            if(MembertopicsTable::existsMemberWish($mbid,$wid)){
                $res = MembertopicsTable::update($wid,array("SORT"=>$value));

                if($res->isSuccess()){
                    $this->arResult['result'] = 1;
                }else{
                    $this->arResult['result'] = 0;
                }
            }
            
        }  

        if($request->isAjaxRequest()){
            

        }else{
           LocalRedirect("/chat/profile");
        }
    }



    public function removewishAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);

        $mbid = null;
        if(isset($member['ID']) && (int)$member['ID']){
            $mbid = (int)$member['ID'];
        }

        $wid = null;
        if(isset($request['wid']) && (int)$request['wid']){
            $wid = (int)$request['wid'];
        }
            
        if($mbid && $wid){
            MembertopicsTable::removeWish($mbid,$wid);
        }  

        if($request->isAjaxRequest()){
            
            if($mbid){
                $wishTopics = MembertopicsTable::getMemberWish($mbid);
            }else{
                $wishTopics = array();
            }
            
            $this->arResult['wishTopics'] = $wishTopics;

            $template = "wishTopicsList";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }

    }



    public function searchtopicAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request->isAjaxRequest()){
            
            $key = isset($request['key']) && trim(strip_tags($request['key'])) != "" ? trim(strip_tags($request['key'])) : "";
            
            if(strlen($key) >= 3){
                $topics = TopicTable::getTopicsByKey($key);
            }else{
                $topics = array();
            }
            
            $this->arResult['key'] = $key;
            $this->arResult['topics'] = $topics;

            $template = "searchTopicResult";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }
    }



    public function loadavaAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

       
        //$this->arResult['loadava'] = 'yesyes';

        $files = $_FILES;

        if(isset($files['ava'])){
            $user_id = CUser::GetID();
            $ava = MembersTable::changeAva($user_id,$files['ava']);
            $this->arResult['res'] = $ava && 1;

            if($ava && file_exists(ALI_AVA_PATH.$ava)){
                $this->arResult['ava_path'] = ALI_PUBLIC_AVA_PATH.$ava;
                $this->arResult['ava_name'] = $ava;
            }

        }else{
            $this->arResult['res'] = 0;
        }
        
        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat/profile");
        }
        
    }




    public function newtopicformAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request->isAjaxRequest()){
            
            $a_topic = isset($request['topic_id']) ? (int)$request['topic_id'] : 0;
            
            if($a_topic){
                $this->arResult['topic_id'] = $a_topic;
                $this->arResult['topic'] = TopicTable::getRowById($a_topic); 
            }
            

            $template = "topicform";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }
    }



    public function proccesstopicformAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();


        $topic = isset($request['Topic']) ? $request['Topic'] : null;
        

        if($topic){
            $id = CUser::GetID();
            $memeber = MembersTable::getMemberByUserId($id);
            if(isset($memeber['ID']) && (int)$memeber['ID']){
                
                //Валидация
                $topic['TITLE'] = strip_tags(trim($topic['TITLE']));
                $topic['ORDER'] = (int)$topic['ORDER'] ? (int)$topic['ORDER'] : 0;
                $topic['OWNER_ID'] = isset($memeber['ID']) ? (int)$memeber['ID'] : 0;
                
                $topic_id = isset($request['topic_id']) ? $request['topic_id'] : 0;
                
                //Проверяем является ли редактируемы пользователь, создателем топика
                if($topic_id){
                    $cT = TopicTable::getRowById($topic_id);
                    if(isset($cT['ID']) && (int)$cT['ID'] && isset($cT['OWNER_ID']) && $cT['OWNER_ID'] == $topic['OWNER_ID']){
                        $res = TopicTable::save($topic,$topic_id);
                    }
                }else{
                    $res = TopicTable::save($topic,$topic_id);
                }
                


            }
        }
        
        
        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat/profile");
        }
    }




    public function removetopicAction(){
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();



        $topic_id = isset($request['topic_id']) ? $request['topic_id'] : null;
        $id = CUser::GetID();
        $memeber = MembersTable::getMemberByUserId($id);
        $owner_id = isset($memeber['ID']) ? $memeber['ID'] : null;
        
        if($topic_id && $owner_id){
            $res = TopicTable::getRow(array('select'=>array("ID"),'filter'=>array("OWNER_ID"=>$owner_id,"ID"=>$topic_id)));

            if(isset($res['ID']) && (int)$res['ID'] === (int)$topic_id){
                $this->arResult['topic_id'] = $topic_id;
                
                return "acceptRemovingTopic";
            }else{
                $this->arResult['result'] = false;
                $this->arResult['error'] = "topic not found!";
            }
        }else{
            $this->arResult['result'] = false;
            $this->arResult['error'] = "trying to remove others topic!";
        }
        
        return "errorRemoving";
        
        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat/profile");
        }
    }


    public function acceptremovingtopicAction(){
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if(!$request->isPost()){
            LocalRedirect("/chat/profile");
        }

        $topic_id = isset($request['topic_id']) ? $request['topic_id'] : null;
        $id = CUser::GetID();
        $memeber = MembersTable::getMemberByUserId($id);
        $owner_id = isset($memeber['ID']) ? $memeber['ID'] : null;
        
        if($topic_id && $owner_id){
            $res = TopicTable::getRow(array('select'=>array("ID"),'filter'=>array("OWNER_ID"=>$owner_id,"ID"=>$topic_id)));

            if(isset($res['ID']) && (int)$res['ID'] === (int)$topic_id){
                
                TopicTable::deleteWithMessages($topic_id);
            }
        }
        LocalRedirect("/chat/profile");
    }




    public function albumformAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if($request->isAjaxRequest()){
            
            $album_id = isset($request['album_id']) ? $request['album_id'] : 0;
            
            if($album_id){
                //здесь проверяем существует ли альбом под этиим id у тек пользователя
                $id = CUser::GetID();
                $memeber = MembersTable::getMemberByUserId($id);

                if(isset($memeber['ID']) && $memeber['ID']){
                    $album = PhotoalbumTable::getAlbumByOwnerAndId($album_id,(int)$memeber['ID']);

                    if(isset($album['ID']) && (int)$album['ID']){
                        //Отправляем данные для редактировния
                        $this->arResult['album_id'] = $album_id;
                        $this->arResult['album'] = $album;
                        $this->arResult['photos'] = PhotoTable::getPhotosByAlbum($album_id);
                    }
                    
                }
                
            }

            $template = "albumform";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }
    }


    public function processalbumformAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        $uid = CUser::GetID();
        if(!$uid){
            LocalRedirect("/chat/profile");
        }
        $member = MembersTable::getMemberByUserId($uid);
        if(!isset($member['ID']) || !$member['ID']){
            LocalRedirect("/chat/profile");
        }


        if(isset($request['Album'])){
            //Указываем чей альбом
            $album = $request['Album'];
            $album['OWNER_ID']=$member['ID'];

            //определяем есть id альбома или нет
            if(isset($request['album_id']) && $request['album_id']){
                $album_id = (int)$request['album_id'] ? (int)$request['album_id'] : 0;

                if($album_id){
                    $user_album = PhotoalbumTable::getAlbumByOwnerAndId($album_id,(int)$member['ID']);
                    if(!isset($user_album['ID']) || !$user_album['ID']){
                        LocalRedirect("/chat/profile");
                    }
                }

            }else{
                $album_id = 0;
            }

            $aid = PhotoalbumTable::save($album,$album_id);

            if($aid){
                if(isset($_FILES['album_main_photo']) && is_array($_FILES['album_main_photo'])){
                    //Добавляем новую аватарку для альбома
                    $main_image = PhotoalbumTable::changeMainImage($aid,$_FILES['album_main_photo']);
                }

                if(isset($_FILES['album_photos']) && is_array($_FILES['album_photos'])){
                    $res = PhotoTable::saveCollections($member['ID'],$aid,$_FILES['album_photos']);

                    if($res){
                       //updated new collection
                        $new_coll['UPDATED'] = new \Bitrix\Main\Type\DateTime();
                        PhotoalbumTable::save($new_coll,$aid); 
                    }
                    
                }
            }else{
               LocalRedirect("/chat/profile"); 
            }
        }
        
        LocalRedirect("/chat/profile");
    }













    public function albumremoveformAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        if($request->isAjaxRequest()){
            $album_id = isset($request['album_id']) ? $request['album_id'] : 0;
            if($album_id){
                //здесь проверяем существует ли альбом под этиим id у тек пользователя
                $id = CUser::GetID();
                $memeber = MembersTable::getMemberByUserId($id);
                if(isset($memeber['ID']) && $memeber['ID']){
                    $album = PhotoalbumTable::getAlbumByOwnerAndId($album_id,(int)$memeber['ID']);
                    if(isset($album['ID']) && (int)$album['ID']){
                        //Отправляем данные для редактировния
                        $this->arResult['album_id'] = $album_id;
                    }
                }
            }
            $template = "removealbumform";
            return $template;
        }else{
           LocalRedirect("/chat/profile");
        }
    }


    public function acceptremovingalbumAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        
        if(!$request->isPost()){
            LocalRedirect("/chat/profile");
        }

        $album_id = isset($request['album_id']) ? $request['album_id'] : 0;
        if($album_id){
                //здесь проверяем существует ли альбом под этиим id у тек пользователя
            $id = CUser::GetID();
            $memeber = MembersTable::getMemberByUserId($id);
            if(isset($memeber['ID']) && $memeber['ID']){
                $album = PhotoalbumTable::getAlbumByOwnerAndId($album_id,(int)$memeber['ID']);
                if(isset($album['ID']) && (int)$album['ID']){
                    $res = PhotoalbumTable::deleteWithImages($album_id);
                }
            }
        }
            
        LocalRedirect("/chat/profile");
    }
    


    public function removephotoAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        
        if(!$request->isAjaxRequest() && !$request->isPost()){
            LocalRedirect("/chat/profile");
        }

        $photo_id = isset($request['photo_id']) ? $request['photo_id'] : 0;
        if($photo_id){
                //здесь проверяем существует ли альбом под этиим id у тек пользователя
            $id = CUser::GetID();
            $memeber = MembersTable::getMemberByUserId($id);
            if(isset($memeber['ID']) && $memeber['ID']){
                $photo = PhotoTable::getRow(array("select"=>array("ID"),'filter'=>array("ID"=>$photo_id,'OWNER_ID'=>(int)$memeber['ID'])));

                if(isset($photo['ID']) && $photo['ID']){
                    $this->arResult['result'] = PhotoTable::deletePhoto($photo['ID']);
                }
            }
        }else{
            LocalRedirect("/chat/profile");
        }
    }


    public function formsettingsAction(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if(!$request->isAjaxRequest()){
            LocalRedirect("/chat/profile");
        }

        $id = CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);
        $this->arResult['member'] = $member;

        $template = "formsettings";
        return $template;   
    }


    public function savesettingsAction(){
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if(!$request->isPost()){
            LocalRedirect("/chat/profile");
        }

        $member_id = isset($request['member_id']) ? (int)$request['member_id'] : 0;


        if(!$member_id){
            LocalRedirect("/chat/profile");
        }


        $id = (int)CUser::GetID();
        $member = MembersTable::getMemberByUserId($id);

        if(isset($member['ID']) && $member['ID'] == $member_id){
            

            $user = isset($request['User']) && is_array($request['User']) ? $request['User'] : array();
            $member = isset($request['Member']) && is_array($request['Member']) ? $request['Member'] : array();

            $member['HIDE_PROFILE'] = isset($member['HIDE_PROFILE']) ? boolval($member['HIDE_PROFILE']) : false;

            $result = new Result();

            UserTable::checkFields($result,$id,$user);
            MembersTable::checkFields($result,$member_id,$member);

            if($result->isSuccess()){

                $mT = new MembersTable();
                $resM = $mT->update($member_id,$member);

                $resU = $mT->updateUser($id,$user);

                if(!$resM->isSuccess() || !$resU){
                    $this->arResult['error']['user'] = $resU;
                    $this->arResult['error']['member'] = $resM->getErrorMessages();
                }

            }else{
                $this->arResult['error']['checkFields'] = $result->getErrorMessages();
            }
        }

        if(isset($this->arResult['error']) && count($this->arResult['error'])){

            // print_r($this->arResult);
            // exit;
        }
        


        LocalRedirect("/chat/profile");
    }




    public function showalbumAction(){
        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();

        if(!$request->isAjaxRequest()){
            return "error";
        }

        $mbid = isset($request['mbid']) && (int)$request['mbid'] ? (int)$request['mbid'] : 0;
        $aid = isset($request['aid']) && (int)$request['aid'] ? (int)$request['aid'] : 0;
        
        if(!$mbid || !$aid){
            return "error";
        }

        $member = MembersTable::getMember($mbid);
        
        if(!isset($member['HIDE_PROFILE'])){
            return "error";
        }

        $id = CUser::GetID();

        if((int)$member['HIDE_PROFILE'] && (int)$id != (int)$member['USER_ID']){
            $this->arResult['hidden'] = 1;
        }else{
            $this->arResult['photos'] = PhotoTable::getPhotosByAlbumAndMember($aid,$mbid);
        }


        return "showalbum";
    }
}