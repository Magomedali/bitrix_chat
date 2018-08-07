<?php


namespace Social\Chat\helpers;


class alimage{




	public static function createSmallImages($file,$name,$path){

        $handle = new upload($file);

        if ($handle->uploaded) {
            $handle->file_new_name_body = "small_".$name;
            //разрешаем изменять размер изображения
            $handle->image_resize = true;
            //ширина изображения будет в px
            $handle->image_x = ALI_SMALL_IMAGE_SIZE;
            //сохраняем соотношение сторон в зависимости от ширины
            $handle->image_ratio_y = true;

            //указываем путь к водяному знаку для изображения
            //$handle->image_watermark = $_SERVER['DOCUMENT_ROOT'].'/path/to/watermark/watermark.png';

            $handle->process($path);
            if ($handle->processed) {
            }
            
        }
        
    }




    public static function createMiddleImages($file,$name,$path){

        $handle = new upload($file);

        if ($handle->uploaded) {
            $handle->file_new_name_body = "middle_".$name;
            //разрешаем изменять размер изображения
            $handle->image_resize = true;
            //ширина изображения будет в px
            $handle->image_x = ALI_MIDDLE_IMAGE_SIZE;
            //сохраняем соотношение сторон в зависимости от ширины
            $handle->image_ratio_y = true;

            //указываем путь к водяному знаку для изображения
            //$handle->image_watermark = $_SERVER['DOCUMENT_ROOT'].'/path/to/watermark/watermark.png';

            $handle->process($path);
            if ($handle->processed) {
            }
            
        }
    }
}
?>