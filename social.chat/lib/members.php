<?php


namespace Social\Chat;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
// use Bitrix\Main\CUser;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Application;
use Social\Chat\helpers\upload;
use Social\Chat\helpers\alimage;

class MembersTable extends Entity\DataManager
{   

    public static $defaultProperties = array(
        'ID',
        'USER_ID',
        'AVA',
        'LOGIN'=>'USER.LOGIN',
        'HIDE_PROFILE',
        'NAME'=>'USER.NAME',
        'LAST_NAME'=>'USER.LAST_NAME',
        'IS_ONLINE'=>'USER.IS_ONLINE'
    );


    public static $defaultPropertiesBySortsOrders = array(
        'ID',
        'USER_ID',
        'AVA',
        'LOGIN'=>'USER.LOGIN',
        'HIDE_PROFILE',
        'NAME'=>'USER.NAME',
        'LAST_NAME'=>'USER.LAST_NAME',
        'IS_ONLINE'=>'USER.IS_ONLINE'
    );


    public static function getTableName()
    {
        return 'ali_social_members';
    }

    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            
            new Entity\IntegerField('USER_ID'),

            new Entity\ReferenceField(
                'USER',
                '\Bitrix\Main\UserTable',
                array('=this.USER_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),
            

            new Entity\ReferenceField(
                'LUSER',
                '\Bitrix\Main\UserTable',
                array('=this.USER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),

            new Entity\ReferenceField(
                'SALEORDER',
                'Social\Chat\helpers\OrderTable',
                array('=this.USER_ID' => 'ref.USER_ID'),
                array('join_type' => 'LEFT')
            ),

            new Entity\ExpressionField('SALEORDER_COUNT',
               'COUNT(%s)', array('SALEORDER.ID')
            ),

            //Ава
            new Entity\StringField('AVA', array(
            )),

            new Entity\BooleanField('HIDE_PROFILE',array(
                //'default_value' => 0,
            )),
        );
    }



    public static function fullStartData(){
        $users =UserTable::getList(array(
            'select'  => array('ID'),
        ))->fetchAll();

        if(is_array($users) && count($users)){


            foreach ($users as $key => $u) {
                
                if(isset($u['ID'])){
                    $res = self::add(array("USER_ID"=>$u['ID']));
                }
            }
        }
    }


    public static function addNewMember($user_id){
        if($user_id){
            self::add(array("USER_ID"=>$user_id));
        }
    }


    public static function getCurrentUserMember(){

        $id = CUser::GetID();
        return self::getMemberByUserId($id);
    }



    public static function getMemberByUserId($id){

        if($id){

            $params = [
                'select'=>[
                    'ID',
                    'USER_ID',
                    'HIDE_PROFILE',
                    'AVA',
                    'LOGIN'=>'USER.LOGIN',
                    'NAME'=>'USER.NAME',
                    'LAST_NAME'=>'USER.LAST_NAME'
                ],
                'filter'=>[
                    'USER_ID'=>$id
                ]
            ];
            return self::getRow($params);
        }
        return false;
    }


    public static function getMember($id){
        if($id){
            $params = [
                'select'=>self::$defaultProperties,
                'filter'=>[
                    'ID'=>$id
                ]
            ];
            return self::getRow($params);
        }
        return false;
    }


    public static function getMembers(){


        $select = self::$defaultPropertiesBySortsOrders;
        $select['SALEORDER_COUNT']= "SALEORDER_COUNT";
        $users =self::getList(array(
            'select'  => $select,
            'group'   => array("ID"),
            'order'   => array('IS_ONLINE'=>"DESC","SALEORDER_COUNT"=>"DESC","ID"=>"ASC")

        ))->fetchAll();


        

        return $users;
    }




    public static function getMembersByKey($key){

        $key = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent($key);

        $select = self::$defaultPropertiesBySortsOrders;
        
        $users =self::getList(array(
            'select'  => $select,
            'group'   => array("ID"),
            'filter'  => array("LOGIC"=>"OR",array("%NAME"=>$key),array("%LAST_NAME"=>$key))
        ))->fetchAll();


        return $users;
    }

    
    





    public static function changeAva($user_id,$ava){
        if(!$user_id || !$ava) return false;
        
        $handle = new upload($ava);
        $ava_name = $user_id."_".uniqid()."_".time();
        if ($handle->uploaded) {
            
            //переименовываем изображение
            $handle->file_new_name_body = $ava_name;
            
            $handle->process(ALI_AVA_PATH);
            if ($handle->processed) {
                
                alimage::createSmallImages($ava,$ava_name,ALI_AVA_PATH);
                alimage::createMiddleImages($ava,$ava_name,ALI_AVA_PATH);

                $ava_name = $ava_name.".".$handle->file_src_name_ext;
                
                //Удаляем файлы старой авы
                self::removeAvaFiles($user_id);
                
                $sql = "UPDATE ".self::getTableName()." SET `AVA`='{$ava_name}' WHERE `USER_ID`={$user_id}";
                Application::getConnection(self::getConnectionName())->queryExecute($sql);
                
                $handle->clean();
                return $ava_name;
            }
        }
        return false;
    }






    public static function removeAvaFiles($user_id){
        $oldAva = self::getRow(array(
                    'select'=>['AVA'],
                    'filter'=>['USER_ID'=>$user_id],
                ));

        if(isset($oldAva['AVA'])){
            $oldAvaFile = ALI_AVA_PATH."".$oldAva['AVA'];
            if(file_exists($oldAvaFile)){
                unlink($oldAvaFile);
            }


            $oldAvaFileMiddle = ALI_AVA_PATH."middle_".$oldAva['AVA'];
            if(file_exists($oldAvaFileMiddle)){
                unlink($oldAvaFileMiddle);
            }
            

            $oldAvaFileSmall = ALI_AVA_PATH."small_".$oldAva['AVA'];
            if(file_exists($oldAvaFileSmall)){
                unlink($oldAvaFileSmall);
            }
        }
    }



    public function updateUser($id,$data){
        

        if(is_array($data) && count($data)){
            $setters = array();
            foreach ($data as $key => $value) {
                $setters[] = $key ."='". $value."'";
            }

            if(count($setters)){
                $sql_setters = implode(", ", $setters);
                $sql = "UPDATE ".UserTable::getTableName()." SET ".$sql_setters." WHERE ID = {$id}";

                Application::getConnection(self::getConnectionName())->queryExecute($sql);
                return true;
            }
        }

        return false;
        
    }

}