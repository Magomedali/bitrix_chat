<?php

namespace Social\Chat;


use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;

class TopicTable extends Entity\DataManager
{

    public static $defaultSelect = array(
                'ID',
                'TITLE',
                'OWNER_ID',
                'MEMBER_'=>'MEMBER',
                'MEMBER_USER_'=>'MEMBER.LUSER',
                'ORDER',
                'LAST_MSG'
    );

    public static function getTableName()
    {
        return 'ali_social_topics';
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

            new Entity\ReferenceField(
                'MEMBER',
                'Social\Chat\MembersTable',
                array('=this.OWNER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),

            new Entity\IntegerField('ORDER',array(
                'default_value' => 0,
            )),

            new Entity\ExpressionField('LAST_MSG',
                'get_last_msg(%s)', array('ID')
            )

            
        );
    }




    public static function deleteWithMessages($id){

        Application::getConnection(self::getConnectionName())->
             queryExecute('DELETE FROM '.Base::getInstance('\Social\Chat\MessagesTable')->getDBTableName()." WHERE TOPIC_ID = ".(int)$id);

        return self::delete($id);
    }




    public static function getThemes($filter = null){

        $params = array(
            'select'  => self::$defaultSelect,
        );

        if($filter){
            $FILTER = array();
            $ORDER = array();
            if(isset($filter['TITLE']) && strip_tags(trim($filter['TITLE'])) !=""){
                $title = strip_tags(trim($filter['TITLE']));
                $title = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent($title);
                $FILTER['%TITLE'] = $title;
            }

            if(isset($filter['ORDER']) && ($filter['ORDER'] === "ASC" || $filter['ORDER'] === "DESC")){
                $o = $filter['ORDER'];
                $ORDER['ORDER'] = $o;
            }

            if(count($FILTER)){
                $params['filter'] = $FILTER;
            }

            if(count($ORDER)){
                $params['order'] = $ORDER;
            }
        }

        $users =self::getList($params)->fetchAll();

        return $users;
    }



    public static function getTopicsByKey($key){

        $key = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent($key);

        $select = self::$defaultSelect;
        
        $topics =self::getList(array(
            'select'  => $select,
            'group'   => array("ID"),
            'filter'  => array("%TITLE"=>$key)
        ))->fetchAll();


        return $topics;
    }




    public static function getThemesbyOwner($id = 0){

        $topics =self::getList(array(
            'select' => self::$defaultSelect,
            'filter' => array(
                "OWNER_ID" => $id
            )
        ))->fetchAll();

        

        return $topics;
    }


    public static function getTheme($id){

        if(!$id) return null;

        $topic =self::getRow(array(
            'select' => self::$defaultSelect,
            'filter' => array(
                "ID" => $id
            )
        ));
        return $topic;
    }


    public static function getThemeLastMsg($id = 0){


        $last = Application::getConnection(self::getConnectionName())->
             queryScalar("SELECT get_last_msg(".(int)$id.") as last_msg");


        return $last;
    }




    public static function save($topic,$topic_id = 0){


        $tt = new TopicTable();
        
        if($topic_id){
            $result = $tt->update($topic_id,$topic);
        }else{
            $result = $tt->add($topic);
        }

        return $result->isSuccess() ? true : false;

    }

}