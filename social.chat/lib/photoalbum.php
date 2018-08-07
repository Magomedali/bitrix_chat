<?php

namespace Social\Chat;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;
use Social\Chat\helpers\upload;
use Social\Chat\helpers\alimage;
use Social\Chat\PhotoTable;

class PhotoalbumTable extends Entity\DataManager
{

    public static $defaultSelect = array(
                'ID',
                'TITLE',
                'MAIN_IMAGE',
                'OWNER_ID',
                'MEMBER_'=>'MEMBER',
                'MEMBER_USER_'=>'MEMBER.LUSER',
                'UPDATED',
                'CREATED',
            );

    public static function getTableName()
    {
        return 'ali_social_photoalbums';
    }




    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            
            new Entity\IntegerField('OWNER_ID'),

            //Название
            new Entity\StringField('TITLE', array(
                'required' => true,
            )),

            //Ава
            new Entity\StringField('MAIN_IMAGE', array(
            )),

            new Entity\ReferenceField(
                'MEMBER',
                'Social\Chat\MembersTable',
                array('=this.OWNER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new Entity\DatetimeField('CREATED'),
            new Entity\DatetimeField('UPDATED'),

            
        );
    }




    public static function deleteWithImages($id){

        if(!$id) return false;

        //Удаление всех фото по альбому
        PhotoTable::deleteByAlbum($id);

        //Удаляем изображение и сам альбом
        $mi = self::getRow(array('select'=>array("MAIN_IMAGE"),'filter'=>array("ID"=>$id)));
        if(isset($mi['MAIN_IMAGE'])){

            
            if($mi['MAIN_IMAGE'] && file_exists(ALI_ALBUM_PATH."".$mi['MAIN_IMAGE'])){
                unlink(ALI_ALBUM_PATH."".$mi['MAIN_IMAGE']);
            }

            if($mi['MAIN_IMAGE'] && file_exists(ALI_ALBUM_PATH."middle_".$mi['MAIN_IMAGE'])){
                unlink(ALI_ALBUM_PATH."middle_".$mi['MAIN_IMAGE']);
            }

            if($mi['MAIN_IMAGE'] && file_exists(ALI_ALBUM_PATH."small_".$mi['MAIN_IMAGE'])){
                unlink(ALI_ALBUM_PATH."small_".$mi['MAIN_IMAGE']);
            }


            return self::delete($id);
        }

        return false;
    }




    public static function getAlbums(){

        $users =self::getList(array(
            'select'  => self::$defaultSelect,
        ))->fetchAll();

        return $users;
    }



    public static function getAlbumsByOwner($id = 0){

        $users =self::getList(array(
            'select' => self::$defaultSelect,
            'filter' => array(
                "OWNER_ID" => $id
            )
        ))->fetchAll();

        return $users;
    }

    public static function getAlbumByOwnerAndId($a_id,$mid){
        if(!$a_id || !$mid) return false;

        $album =self::getRow(array(
            'select' => self::$defaultSelect,
            'filter' => array(
                "OWNER_ID" => $mid,
                "ID"=>$a_id
            )
        ));

        return $album;
    }




    public static function save($album,$album_id = 0){


        $tt = new PhotoalbumTable();
        
        if($album_id){
            
            $result = $tt->update($album_id,$album);
        }else{
            $album['CREATED'] = new \Bitrix\Main\Type\DateTime();
            $album['UPDATED'] = new \Bitrix\Main\Type\DateTime();
            $result = $tt->add($album);
        }

        return $result->isSuccess() ?  $result->getId() : false;

    }



    public static function removeMainImagesFiles($aid){
        $oldAva = self::getRow(array(
                    'select'=>['MAIN_IMAGE'],
                    'filter'=>['ID'=>$aid],
        ));

        if(isset($oldAva['MAIN_IMAGE'])){
            
            $oldAvaFile = ALI_ALBUM_PATH."".$oldAva['MAIN_IMAGE'];
            if(file_exists($oldAvaFile)){
                unlink($oldAvaFile);
            }

            $oldAvaFileMiddle = ALI_ALBUM_PATH."middle_".$oldAva['MAIN_IMAGE'];
            if(file_exists($oldAvaFileMiddle)){
                unlink($oldAvaFileMiddle);
            }
            

            $oldAvaFileSmall = ALI_ALBUM_PATH."small_".$oldAva['MAIN_IMAGE'];
            if(file_exists($oldAvaFileSmall)){
                unlink($oldAvaFileSmall);
            }
        }

    }



    public static function changeMainImage($aid,$ava){
        if(!$aid || !$ava) return false;
        

        $handle = new upload($ava);
        $ava_name = $aid."_album_".uniqid()."_".time();
        if ($handle->uploaded) {
            
             //переименовываем изображение
            $handle->file_new_name_body = $ava_name;
            
            
            $handle->process(ALI_ALBUM_PATH);
            if ($handle->processed) {
               
                
                alimage::createSmallImages($ava,$ava_name,ALI_ALBUM_PATH);
                alimage::createMiddleImages($ava,$ava_name,ALI_ALBUM_PATH);

                $handle->clean();

                $ava_name = $ava_name.".".$handle->file_src_name_ext;
                
                self::removeMainImagesFiles($aid);

                $sql = "UPDATE ".self::getTableName()." SET `MAIN_IMAGE`='{$ava_name}' WHERE `ID`={$aid}";
                Application::getConnection(self::getConnectionName())->queryExecute($sql);
                
                return $ava_name;
            }
        }
        return false;
    }

}