<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Sale\Internals\PaySystemActionTable;
Loc::loadMessages(__FILE__);
Main\Loader::includeModule("sale");
/**
 * Class BonusaccountsTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> LID string(2) mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> NAME string(50) mandatory
 * <li> PAYSYSTEMID int mandatory
 * </ul>
 *
 * @package Bitrix\Bonusaccounts
 **/

class CVbchBonusaccountsTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonusaccounts';
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
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'LID' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLid'),
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_LID_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_ACTIVE_FIELD'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateName'),
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_NAME_FIELD'),
            ),
            'PAYSYSTEMID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_PAYSYSTEMID_FIELD'),
            ),
            'SETTINGS' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('BONUSACCOUNTS_ENTITY_SETTINGS_FIELD'),
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
            new Main\Entity\Validator\Length(null, 2),
        );
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

    public static function OnBeforeAdd(Entity\Event $event){
        //перед добавление добавляем платежную систему innerBonus
        $result = new Main\Entity\EventResult;
        $f = $event->getParameter('fields');

        $file=$_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/sale_payment/innerbonus/bonus-ps.gif";

        $arFields = array(
            "NAME" => $f['NAME'],
            "PSA_NAME" => $f['NAME'],
            "ACTIVE" => $f['ACTIVE'],
            "CAN_PRINT_CHECK" => 'N',
            "CODE" => 'inner_bonus_code',
            "NEW_WINDOW" => 'N',
            "ALLOW_EDIT_PAYMENT" => 'N',
            "IS_CASH" => 'N',
            "ENTITY_REGISTRY_TYPE" => \Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER,
            "SORT" => 9999,
            "ENCODING" => '',
            "DESCRIPTION" => '',
            "ACTION_FILE" =>'innerbonus',
            'PS_MODE' => '',
            'XML_ID' => \Bitrix\Sale\PaySystem\Manager::generateXmlId()
        );
        if(file_exists($file)){
            $l=\CFile::MakeFileArray($file);
            $arFields['LOGOTIP']=\CFile::SaveFile($l, 'sale/paysystem/logotip');
        }

        $ps=PaySystemActionTable::add($arFields);

        $modifyFieldList['PAYSYSTEMID']=$ps->getId();

        $result->modifyFields($modifyFieldList);
        return $result;

    }
    public static function OnBeforeUpdate(Entity\Event $event){
        //перед обновлением обновляем платежную систему innerBonus
        $primary = $event->getParameter("id");
        $f = $event->getParameter('fields');
        $tmp=self::getById($primary)->fetch();

        $fields=array(
            'NAME'=>$f['NAME'],
            'ACTIVE'=>$f['ACTIVE']
        );
        PaySystemActionTable::update($tmp['PAYSYSTEMID'],$fields);
    }

    public static function OnBeforeDelete(Entity\Event $event){
        //перед удалением удаляем платежную систему innerBonus
        $primary = $event->getParameter("id");
        $tmp=self::getById($primary)->fetch();
        PaySystemActionTable::delete($tmp['PAYSYSTEMID']);

    }
}