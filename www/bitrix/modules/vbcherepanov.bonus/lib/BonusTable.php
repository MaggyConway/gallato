<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class BonusTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> LID string(2) mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> BONUS double mandatory default 0.0000
 * <li> USERID int mandatory
 * <li> ACTIVE_FROM datetime optional
 * <li> ACTIVE_TO datetime optional
 * <li> DESCRIPTION string optional
 * <li> TYPE int mandatory
 * <li> OPTIONS string optional
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class BonusTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus';
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
                'title' => Loc::getMessage('BONUS_ENTITY_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('BONUS_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'LID' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLid'),
                'title' => Loc::getMessage('BONUS_ENTITY_LID_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('BONUS_ENTITY_ACTIVE_FIELD'),
            ),
            'BONUS' => array(
                'data_type' => 'float',
                'required' => true,
                'title' => Loc::getMessage('BONUS_ENTITY_BONUS_FIELD'),
            ),
            'USERID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('BONUS_ENTITY_USERID_FIELD'),
            ),
            'ACTIVE_FROM' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('BONUS_ENTITY_ACTIVE_FROM_FIELD'),
            ),
            'ACTIVE_TO' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('BONUS_ENTITY_ACTIVE_TO_FIELD'),
            ),
            'DESCRIPTION' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('BONUS_ENTITY_DESCRIPTION_FIELD'),
            ),
            'TYPES' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('BONUS_ENTITY_TYPE_FIELD'),
            ),
            'OPTIONS' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('BONUS_ENTITY_OPTIONS_FIELD'),
            ),
            'UPDATE_1C' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('BONUS_ENTITY_UPDATE_FIELD'),
            ),
            'UPDATE_DATE' =>  new Main\Entity\DatetimeField('UPDATE_DATE', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('BONUS_ENTITY_UPDATE_DATE_FIELD'),
            )),
            'SORT' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('BONUS_ENTITY_SORT_FIELD'),
            ),
            'PARTPAY' => array(
                'data_type' => 'float',
                'required' => true,
                'title' => Loc::getMessage('BONUS_ENTITY_PARTPAY_FIELD'),
            ),
            'BONUSACCOUNTSID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('BONUS_ENTITY_BONUSACCOUNTSID_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for LID field.
     *
     * @return array
     */
    public static function validateLid()
    {
        return array(
            new Entity\Validator\Length(null, 2),
        );
    }
    public static function OnBeforeUpdate(Entity\Event $event){
        $result = new Main\Entity\EventResult();
        $primary = $event->getParameter("id");
        $data = $event->getParameter('fields');
        $data['UPDATE_1C']='Y';
        $result->modifyFields($data);
        return $result;
    }
    public static function OnBeforeAdd(Entity\Event $event){
        $result = new Main\Entity\EventResult();
        $data = $event->getParameter('fields');
        $data['UPDATE_1C']='Y';
        $result->modifyFields($data);
        return $result;
    }

    public static function OnBeforeDelete(Entity\Event $event)
    {
        $primary = $event->getParameter("id");
        $tmp = self::getById($primary)->fetch();
        if($tmp['ACTIVE']=='Y' && floatval($tmp['BONUS']>0)){
            $account=\ITRound\Vbchbbonus\AccountTable::getList(array(
                'filter'=>array('USER_ID'=>$tmp['USERID'])
            ));
            while($l=$account->fetch()){
                $summa=floatval($l['CURRENT_BUDGET'])-floatval($tmp['BONUS']);
                \ITRound\Vbchbbonus\AccountTable::update($l['ID'],array('CURRENT_BUDGET'=>$summa));
            }

        }
        unset($tmp);
    }
}