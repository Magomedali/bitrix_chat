<?php


namespace Social\Chat;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;
use Social\Chat\helpers\upload;
use Social\Chat\helpers\alimage;

class PhotoTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'ali_social_photos';
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
            new Entity\ReferenceField(
                'FROM_MEMBER',
                'Social\Chat\MembersTable',
                array('=this.OWNER_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),

            new Entity\IntegerField('ALBUM_ID'),
            new Entity\ReferenceField(
                'ALBUM',
                'Social\Chat\PhotoalbumTable',
                array('=this.ALBUM_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),


            //Название
            new Entity\StringField('TITLE', array(
            )),

            new Entity\StringField('FILE_NAME', array(
            )),

            new Entity\DatetimeField('CREATED'),
        );
    }



    //Сортирует в удобный для обработки стуктуру файлы 
    public static function packCollections($collection){
        
        if(isset($collection['tmp_name']) && is_array($collection['tmp_name']) && count($collection['tmp_name'])){
            $count = count($collection['tmp_name']);
            $packet = array();
            foreach ($collection as $key => $value) {
                
                for ($i=0; $i < $count; $i++) {
                    if (is_array($value) && array_key_exists($i,$value)) {
                        $packet[$i][$key] = $value[$i];
                    }
                }
            }

            return $packet;
        }

        return false;
    } 
    




    public static function uploadPhoto($name,$ava){
        if(!$name || !$ava) return false;
        

        $handle = new upload($ava);
        $ava_name = $name;
        if ($handle->uploaded) {
            
             //переименовываем изображение
            $handle->file_new_name_body = $ava_name;
            

            $handle->process(ALI_ALBUM_PATH);
            if ($handle->processed) {

                alimage::createSmallImages($ava,$ava_name,ALI_ALBUM_PATH);
                alimage::createMiddleImages($ava,$ava_name,ALI_ALBUM_PATH);

                $handle->clean();
                
                $ava_name = $ava_name.".".$handle->file_src_name_ext;
                
                return $ava_name;
            }
        }
        return false;
    }




    public static function save($photo,$p_id = 0){
        $tt = new PhotoTable();
        
        if($p_id){
            $result = $tt->update($p_id,$photo);
        }else{
            $photo['CREATED'] = new \Bitrix\Main\Type\DateTime();
            $result = $tt->add($photo);
        }


        return $result->isSuccess() ?  $result->getId() : false;
    }




    public static function saveCollections($mid,$aid,$photos){
        if(!$mid || !$aid || !is_array($photos)) return false;

        $collections = self::packCollections($photos);

        $updated = false;
        if(is_array($collections) && count($collections)){

            foreach ($collections as $key => $file) {
                $photo = array();
                $photo['OWNER_ID'] = $mid;
                $photo['ALBUM_ID'] = $aid;

                $photo['TITLE'] = "";
                
                //Доб запись в базу, получаем id
                $p_id = self::save($photo);

                if($p_id){
                    $photo_name = $aid."_album_".$p_id."_item_".uniqid()."_".time();
                    //добавляем файл на сервер и получаем имя файла
                    $photo_file = self::uploadPhoto($photo_name,$file);

                    //Проверяем существование файла
                    if($photo_file && file_exists(ALI_ALBUM_PATH."".$photo_file)){
                        $ch = array();
                        $ch['FILE_NAME'] = $photo_file;
                        //Изменяем добавленную запись, изменяем file_name
                        $cp_id = self::save($ch,$p_id);
                        
                        //updatedAlbum
                        $updated = $cp_id ? true : false;

                        //Если изменение не прошло удачно, удаляем файл из сервера
                        if(!$cp_id){
                            unlink(ALI_ALBUM_PATH."".$photo_file);
                            unlink(ALI_ALBUM_PATH."small_".$photo_file);
                            unlink(ALI_ALBUM_PATH."middle_".$photo_file);
                        }
                    }else{
                        //Если файл не существует, удаляем запись
                        self::delete($p_id);
                    }
                }
            }

            return $updated;
        }
       
    }
   

    public static function getPhotosByAlbum($topic = 0){


        $messages = self::getList([
            'select'  => [
                'ID',
                'OWNER_ID',
                'ALBUM_ID',
                'TITLE',
                'FILE_NAME',
                'CREATED'
            ],
            'filter'=>[
                'ALBUM_ID'=>$topic
            ],
            'order'=>[
                'ID'=>'asc'
            ]
        ])->fetchAll();

        return $messages;
    }



    public static function deleteByAlbum($id){
        if(!$id) return false;

        $photos = PhotoTable::getList(array('select'=>array("ID","FILE_NAME"),'filter'=>array("ALBUM_ID"=>$id)))->fetchAll();

        //Удаление всех фото по альбому
        if(is_array($photos)){
            foreach ($photos as $k => $p) {
                if(isset($p['FILE_NAME']) && file_exists(ALI_ALBUM_PATH."".$p['FILE_NAME'])){
                    unlink(ALI_ALBUM_PATH."".$p['FILE_NAME']);
                }


                if(isset($p['FILE_NAME']) && file_exists(ALI_ALBUM_PATH."middle_".$p['FILE_NAME'])){
                    unlink(ALI_ALBUM_PATH."middle_".$p['FILE_NAME']);
                }


                if(isset($p['FILE_NAME']) && file_exists(ALI_ALBUM_PATH."small_".$p['FILE_NAME'])){
                    unlink(ALI_ALBUM_PATH."small_".$p['FILE_NAME']);
                }
            }
        }

        Application::getConnection(self::getConnectionName())->
             queryExecute('DELETE FROM '.self::getTableName()." WHERE ALBUM_ID = ".(int)$id);

    }


    public static function deletePhoto($id){
        if(!$id) return false;

        //Удаляем изображение
        $mi = self::getRow(array('select'=>array("FILE_NAME"),'filter'=>array("ID"=>$id)));
        if(isset($mi['FILE_NAME'])){

            if($mi['FILE_NAME'] && file_exists(ALI_ALBUM_PATH."".$mi['FILE_NAME'])){
                unlink(ALI_ALBUM_PATH."".$mi['FILE_NAME']);
            }


            if($mi['FILE_NAME'] && file_exists(ALI_ALBUM_PATH."middle_".$mi['FILE_NAME'])){
                unlink(ALI_ALBUM_PATH."middle_".$mi['FILE_NAME']);
            }


            if($mi['FILE_NAME'] && file_exists(ALI_ALBUM_PATH."small_".$mi['FILE_NAME'])){
                unlink(ALI_ALBUM_PATH."small_".$mi['FILE_NAME']);
            }
            


            return self::delete($id);
        }

        return false;
    }



    public static function getPhotosByAlbumAndMember($aid,$mbid){
        if(!$aid || !$mbid) return false;

        $photos = self::getList([
            'select'  => [
                'ID',
                'OWNER_ID',
                'ALBUM_ID',
                'TITLE',
                'FILE_NAME',
                'CREATED'
            ],
            'filter'=>[
                'ALBUM_ID'=>$aid,
                'OWNER_ID'=>$mbid
            ],
            'order'=>[
                'ID'=>'asc'
            ]
        ])->fetchAll();

        return $photos;

    }

}