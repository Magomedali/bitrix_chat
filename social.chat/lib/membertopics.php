<?php

namespace Social\Chat;


use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Application;
use \Bitrix\Main\Entity\Base;

class MembertopicsTable extends Entity\DataManager
{

    public static $defaultSelect = array(
                'ID',
                'MEMBER_ID',
                'TOPIC_ID',
                'T_'=>'TOPIC',
                'M_'=>'MEMBER',
                'SHOW_TOPIC',
                'SORT',
    );

    public static function getTableName()
    {
        return 'ali_social_user_topics';
    }




    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            
            new Entity\IntegerField('MEMBER_ID',array(
                'required' => true,
            )),

            //Название
            new Entity\IntegerField('TOPIC_ID', array(
                'required' => true,
            )),

            new Entity\ReferenceField(
                'MEMBER',
                'Social\Chat\MembersTable',
                array('=this.MEMBER_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),

            new Entity\ReferenceField(
                'TOPIC',
                'Social\Chat\TopicTable',
                array('=this.TOPIC_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),


            new Entity\IntegerField('SHOW_TOPIC',array(
                'default_value' => 0,
            )),


            new Entity\IntegerField('SORT',array(
                'default_value' => 0,
            )),
            
        );
    }



    public static function save($mbid,$topic_id){

        if(!self::existsWish($mbid,$topic_id)){
            $tt = new MembertopicsTable();
        
            $result = $tt->add(array(
                'MEMBER_ID'=>(int)$mbid,
                'TOPIC_ID'=>(int)$topic_id,
                'SHOW_TOPIC'=>0,
                'SORT'=>0,
            ));
            

            return $result->isSuccess() ? true : false;
        }else{
            return false;
        }
        
    }

    //check exists wish by member id and topic id
    public static function existsWish($mbid,$topic_id){
        
        if(!$mbid || !$topic_id) return false;

        $w = MembertopicsTable::getRow(array(
            'select'=>array('ID'),
            'filter'=>array('MEMBER_ID'=>(int)$mbid,'TOPIC_ID'=>(int)$topic_id)
        ));
        
        return isset($w['ID']) ? true : false;
    }




    //check exists wish by member id and wish id
    public static function existsMemberWish($mbid,$wish_id){
        
        if(!$mbid || !$wish_id) return false;

        $w = MembertopicsTable::getRow(array(
            'select'=>array('ID'),
            'filter'=>array('MEMBER_ID'=>(int)$mbid,'ID'=>(int)$wish_id)
        ));
        
        return isset($w['ID']) ? true : false;
    }





    // Remove member topic from wish
    public static function removeWish($mbid,$wish_id){

        if(!$mbid || !$wish_id) return false;


        if(self::existsMemberWish($mbid,$wish_id)){
            return self::delete($wish_id);
        }

    }



    // change show_topic state for wish value 1
    public static function show($mbid,$wish_id){

        if(!$mbid || !$wish_id) return false;


        if(self::existsMemberWish($mbid,$wish_id)){

            $result = self::update($wish_id,array("SHOW_TOPIC"=>1));
            return $result->isSuccess() ? true : false;
        }

        return false;

    }



    // change show_topic state for wish  value 0
    public static function hide($mbid,$wish_id){

        if(!$mbid || !$wish_id) return false;


        if(self::existsMemberWish($mbid,$wish_id)){
            $result = self::update($wish_id,array("SHOW_TOPIC"=>0));
            return $result->isSuccess() ? true : false;
        }

        return false;

    }



    public static function getMemberWish($mbid){

        if(!$mbid) return array();


        $topic =self::getList(array(
            'select' => self::$defaultSelect,
            'filter' => array(
                "MEMBER_ID" => (int)$mbid
            ),
            'order'=>array('SORT'=>"ASC")
        ))->fetchAll();

        return $topic;
    
    }



    public static function getMemberShowTopics($mbid){

        if(!$mbid) return array();

        $select = self::$defaultSelect;
        $select['LAST_MSG'] = "TOPIC.LAST_MSG";

        $topic =self::getList(array(
            'select' => $select,
            'filter' => array(
                "MEMBER_ID" => (int)$mbid,
                'SHOW_TOPIC'=> 1
            ),
            'order'=>array('SORT'=>"ASC")
        ))->fetchAll();

        return $topic;

    }




}