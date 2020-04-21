<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
 Bitrix\Main\Entity,
 Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ProfilesTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> SITE string(2) mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> BONUS double mandatory default 0.0000
 * <li> TYPE string(25) mandatory
 * <li> NOTIFICATION string optional
 * <li> FILTER string optional
 * <li> SCOREIN bool optional default 'Y'
 * <li> ISADMIN bool optional default 'Y'
 * <li> BONUSCONFIG string optional
 * <li> SETTINGS string optional
 * <li> COUNTS int mandatory
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class CvbchbonusprofilesTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus_profiles';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('PROFILES_ENTITY_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('PROFILES_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateName'),
                'title' => Loc::getMessage('PROFILES_ENTITY_NAME_FIELD'),
            ),
            'SITE' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateSite'),
                'title' => Loc::getMessage('PROFILES_ENTITY_SITE_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('PROFILES_ENTITY_ACTIVE_FIELD'),
            ),
            'BONUS' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('PROFILES_ENTITY_BONUS_FIELD'),
            ),
            'TYPE' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateType'),
                'title' => Loc::getMessage('PROFILES_ENTITY_TYPE_FIELD'),
            ),
            'NOTIFICATION' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('PROFILES_ENTITY_NOTIFICATION_FIELD'),
            ),
            'FILTER' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('PROFILES_ENTITY_FILTER_FIELD'),
            ),
            'SCOREIN' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'default_value' => 'N',
                'title' => Loc::getMessage('PROFILES_ENTITY_SCOREIN_FIELD'),
            ),
            'ISADMIN' => array(
                'data_type' => 'boolean',
                'default_value' => 'N',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('PROFILES_ENTITY_ISADMIN_FIELD'),
            ),
            'BONUSCONFIG' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('PROFILES_ENTITY_BONUSCONFIG_FIELD'),
            ),
            'SETTINGS' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('PROFILES_ENTITY_SETTINGS_FIELD'),
            ),
            'COUNTS' => array(
                'data_type' => 'integer',
                'default_value' => 0,
                'required' => true,
                'title' => Loc::getMessage('PROFILES_ENTITY_COUNTS_FIELD'),
            ),
            'ACTIVE_FROM' => new Main\Entity\DatetimeField('ACTIVE_FROM', array(
                'title' => Loc::getMessage('PROFILES_ENTITY_ACTIVE_FROM_FIELD'),
            )),
            'ACTIVE_TO' => new Main\Entity\DatetimeField('ACTIVE_TO', array(
                'title' => Loc::getMessage('PROFILES_ENTITY_ACTIVE_TO_FIELD'),
            )),

        );
    }
    /**
     * Returns validators for SITE field.
     *
     * @return array
     */
    public static function ProfileIncrement($ID){
        $tmp=self::getList(array(
           'filter'=>array("ID"=>$ID),
            'select'=>array('COUNTS'),
        ))->fetch();
        $l=intval($tmp['COUNTS']);$l++;
        self::update($ID,array("COUNTS"=>$l));
    }
    public static function ReturnType($ID){
        return self::getList(array(
            'filter'=>array('=ID'=>$ID),
            'select'=>array('TYPE'),
        ))->fetch();
    }
    public static function ReturnID($Type){
        return self::getList(array(
            'filter'=>array('=TYPE'=>$Type),
            'select'=>array('ID'),
        ))->fetch();
    }
    /**
     * Returns validators for NAME field.
     *
     * @return array
     */
    public static function validateName()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }
    public static function validateSite()
    {
        return array(
            new Main\Entity\Validator\Length(null, 2),
        );
    }
    /**
     * Returns validators for TYPE field.
     *
     * @return array
     */
    public static function validateType()
    {
        return array(
            new Main\Entity\Validator\Length(null, 25),
        );
    }

    public static function OnBeforeUpdate(Entity\Event $event){
        $result = new Main\Entity\EventResult();
        $primary = $event->getParameter("id");
        $data = $event->getParameter('fields');
       // self::SetSort($primary['ID'],$data);
        return $result;
    }
    public static function SetSort($primary,$data){
        $typeP=$primary;
        $settings=unserialize($data['SETTINGS']);
        $ind_sort=$settings['SORT'];
        unset($settings);
        $bt=\ITRound\Vbchbbonus\BonusTable::getList(
           array(
               'filter'=>array('ACTIVE'=>'Y','>BONUS'=>0),
               'select'=>array('ID','BONUS','*','SORT'),
           )
        )->fetchAll();
        if(sizeof($bt)>0){
           foreach($bt as $tb){
               $qq=unserialize($tb['OPTIONS']);
               if($qq['PROFILE_ID']==$typeP)
                    \ITRound\Vbchbbonus\BonusTable::update($tb['ID'],array('SORT'=>$ind_sort));
           }
        }
        $bt=\ITRound\Vbchbbonus\TmpTable::getList(
            array(
                'filter'=>array('ACTIVE'=>'Y','>BONUS'=>0),
                'select'=>array('ID','BONUS','DESCRIPTION','SORT'),
            )
        )->fetchAll();
        if(sizeof($bt)>0){
            foreach($bt as $tb){
                $qq=unserialize($tb['OPTIONS']);
                if($qq['PROFILE_ID']==$typeP)
                    \ITRound\Vbchbbonus\TmpTable::update($tb['ID'],array('SORT'=>$ind_sort));
            }
        }

    }

    public static function OnBeforeAdd(Entity\Event $event){
        $result = new Main\Entity\EventResult();
        $primary = $event->getParameter("id");
        $data = $event->getParameter('fields');
        self::SetSort($primary['ID'],$data);
        return $result;
    }

}