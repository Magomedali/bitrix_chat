<?php


namespace Social\Chat;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
use \Bitrix\Main\Application;
use Social\Chat\helpers\upload;
use Social\Chat\helpers\alimage;

class MessagesTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ali_social_messages';
    }

    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            
            new Entity\IntegerField('FROM_ID'),
            new Entity\ReferenceField(
                'FROM_MEMBER',
                'Social\Chat\MembersTable',
                array('=this.FROM_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),

            new Entity\IntegerField('TO_ID'),
            new Entity\ReferenceField(
                'TO_MEMBER',
                'Social\Chat\MembersTable',
                array('=this.TO_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),

            new Entity\IntegerField('TOPIC_ID'),
            new Entity\ReferenceField(
                'TOPIC',
                'Social\Chat\TopicTable',
                array('=this.TOPIC_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),

            //Название
            new Entity\TextField('TEXT', array(
            )),

            new Entity\StringField('FILE_NAME', array(
            )),
            
            new Entity\IntegerField('FILE_FROM_FIELD'),

            new Entity\IntegerField('READED'),

            new Entity\DatetimeField('CREATED'),
        );
    }


    


    public function save($data){
        if(isset($data['Message']) && $data['Message']){
            $msg = $data['Message'];

            $msg_text = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent($msg['text']);

            $msg_file = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent($msg['file']);

            $msg_text = strip_tags($msg_text,"<img>");
            $msg_file = strip_tags($msg_file);
            $addData = array(
                "FROM_ID" => isset($msg['from']) && (int)$msg['from'] ? (int)$msg['from'] : null,
                "TO_ID" => isset($msg['to']) && (int)$msg['to'] ? (int)$msg['to'] : 0,
                "TOPIC_ID" => isset($msg['topic_id']) && (int)$msg['topic_id'] ? (int)$msg['topic_id'] : 0,
                "TEXT" => isset($msg['text']) && trim($msg_text) ? trim($msg_text) : "",
                "FILE_NAME" => isset($msg['file']) && trim($msg['file']) ? trim($msg_file) : "",
                "FILE_FROM_FIELD" => isset($msg['file_from_album']) && (int)$msg['file_from_album'] == 1 ? 1 : 0,
                "CREATED" => new \Bitrix\Main\Type\DateTime(),
            );

            $res = $this->add($addData);
            return $res->isSuccess() ? true :false;
             
        }else{
            return false;
        }
    }



    public static function getCommonMessages(){


        $messages = self::getList([
            'select'  => [
                'ID',
                'FROM_ID',
                'FROM_MEMBER_'=>'FROM_MEMBER',
                'FROM_AVA'=>'FROM_MEMBER.AVA',
                'FROM_MEMBER_USER_LAST_NAME'=>'FROM_MEMBER.USER.LAST_NAME',
                'FROM_MEMBER_USER_NAME'=>'FROM_MEMBER.USER.NAME',
                'TO_ID',
                'TO_MEMBER_'=>'TO_MEMBER',
                'TO_MEMBER_USER_LAST_NAME'=>'TO_MEMBER.LUSER.LAST_NAME',
                'TO_MEMBER_USER_NAME'=>'TO_MEMBER.LUSER.NAME',
                'TEXT',
                'FILE_NAME',
                'FILE_FROM_FIELD',
                'TOPIC_ID',
                'CREATED'
            ],
            'filter'=>[
                'TOPIC_ID'=>0
            ],
            'order'=>[
                'ID'=>'asc'
            ]
        ])->fetchAll();

        return $messages;
    }




    public static function getMessagesByTopic($topic = 0,$limit = 6, $start = 0){


        $messages = self::getList([
            'select'  => [
                'ID',
                'FROM_ID',
                'FROM_MEMBER_'=>'FROM_MEMBER',
                'FROM_AVA'=>'FROM_MEMBER.AVA',
                'FROM_MEMBER_USER_LAST_NAME'=>'FROM_MEMBER.USER.LAST_NAME',
                'FROM_MEMBER_USER_NAME'=>'FROM_MEMBER.USER.NAME',
                'TO_ID',
                'TO_MEMBER_'=>'TO_MEMBER',
                'TO_MEMBER_USER_LAST_NAME'=>'TO_MEMBER.LUSER.LAST_NAME',
                'TO_MEMBER_USER_NAME'=>'TO_MEMBER.LUSER.NAME',
                'TEXT',
                'FILE_NAME',
                'FILE_FROM_FIELD',
                'TOPIC_ID',
                'CREATED'
            ],
            'filter'=>[
                'TOPIC_ID'=>$topic
            ],
            'order'=>[
                'ID'=>'desc'
            ],
            'limit'=> $limit,
            'offset'=> $start,
        ])->fetchAll();


        return $messages;
    }






    public static function saveTempFile($user_id,$file){
        if(!$user_id || !$file) return false;
        

        $handle = new upload($file);
        $img_name = $user_id."_msg_".uniqid()."_".time();
        if ($handle->uploaded) {
            
             //переименовываем изображение
            $handle->file_new_name_body = $img_name;
            
            $handle->process(ALI_MSG_PATH);
            if ($handle->processed) {

                alimage::createSmallImages($file,$img_name,ALI_MSG_PATH);
                alimage::createMiddleImages($file,$img_name,ALI_MSG_PATH);

                $handle->clean();
                
                $img_name = $img_name.".".$handle->file_src_name_ext;
                
                return $img_name;
            }
        }
        return false;
    }






    public static function removeFile($file){
        
        if($file && file_exists($_SERVER['DOCUMENT_ROOT']."/".$file)){

            $pathParts = explode("/", $file);
            $pathParts = array_reverse($pathParts);
            $fName = $pathParts[0];
            
            if(file_exists(ALI_MSG_PATH.$fName)){
                unlink(ALI_MSG_PATH.$fName);
            }

            $small = ALI_MSG_PATH."small_".$fName;
            if(file_exists($small)){
                unlink($small);
            }

            $middle = ALI_MSG_PATH."middle_".$fName;
            if(file_exists($middle)){
                unlink($middle);
            }
            
        }

    }


/**
* Для получения сгенерированного sql запроса (НАЧАЛО)
*/
    public static function getSQLmessages(){
        $messages = self::getListSql([
            'select'  => [
                'ID',
                'FROM_ID',
                'FROM_MEMBER_'=>'FROM_MEMBER',
                'FROM_AVA'=>'FROM_MEMBER.AVA',
                'FROM_MEMBER_USER_LAST_NAME'=>'FROM_MEMBER.USER.LAST_NAME',
                'FROM_MEMBER_USER_NAME'=>'FROM_MEMBER.USER.NAME',
                'TO_ID',
                'TO_MEMBER_'=>'TO_MEMBER',
                'TO_MEMBER_USER_LAST_NAME'=>'TO_MEMBER.LUSER.LAST_NAME',
                'TO_MEMBER_USER_NAME'=>'TO_MEMBER.LUSER.NAME',
                'TEXT',
                'FILE_NAME',
                'TOPIC_ID',
                'CREATED'
            ],
            'filter'=>[
                'TOPIC_ID'=>$topic
            ],
            'order'=>[
                'ID'=>'desc'
            ],
            'limit'=> $limit,
            'offset'=> $start,
        ]); //->fetchAll();


        print_r($messages->getQuery());
        exit;
        return $messages;
    }
    public static function getListSql(array $parameters = array())
    {
        $query = static::query();

        if(!isset($parameters['select']))
        {
            $query->setSelect(array('*'));
        }

        foreach($parameters as $param => $value)
        {
            switch($param)
            {
                case 'select':
                    $query->setSelect($value);
                    break;
                case 'filter':
                    $value instanceof Filter ? $query->where($value) : $query->setFilter($value);
                    break;
                case 'group':
                    $query->setGroup($value);
                    break;
                case 'order';
                    $query->setOrder($value);
                    break;
                case 'limit':
                    $query->setLimit($value);
                    break;
                case 'offset':
                    $query->setOffset($value);
                    break;
                case 'count_total':
                    $query->countTotal($value);
                    break;
                case 'runtime':
                    foreach ($value as $name => $fieldInfo)
                    {
                        $query->registerRuntimeField($name, $fieldInfo);
                    }
                    break;
                case 'data_doubling':
                    if($value)
                    {
                        $query->enableDataDoubling();
                    }
                    else
                    {
                        $query->disableDataDoubling();
                    }
                    break;
                case 'cache':
                    $query->setCacheTtl($value["ttl"]);
                    if(isset($value["cache_joins"]))
                    {
                        $query->cacheJoins($value["cache_joins"]);
                    }
                    break;
                default:
                    throw new Main\ArgumentException("Unknown parameter: ".$param, $param);
            }
        }
        return $query;
    }
/**
* Для получения сгенерированного sql запроса  (КОНЕЦ)
*/



}