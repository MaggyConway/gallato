<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ReferalTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> LID string(2) mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> REFFROM int mandatory
 * <li> REFERER string(50) mandatory
 * <li> REFBONUS bool optional default 'N'
 * <li> USERID int optional
 * <li> COOKIE string(50) mandatory
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

define('REF_ADD_COUPONE', 0);
define('REF_ADD_BROWSER_REF', 1);
define('REF_ADD_MANUAL_ADMIN', 2);
define('REF_ADD_COMPONENT', 3);

class CVbchRefTable extends Main\Entity\DataManager
{

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus_referal';
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
                'title' => Loc::getMessage('REFERAL_ENTITY_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('REFERAL_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'LID' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLid'),
                'title' => Loc::getMessage('REFERAL_ENTITY_LID_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('REFERAL_ENTITY_ACTIVE_FIELD'),
            ),
            'REFFROM' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('REFERAL_ENTITY_REFFROM_FIELD'),
            ),
            'REFERER' => array(
                'data_type' => 'string',
                'required' => false,
                'validation' => array(__CLASS__, 'validateReferer'),
                'title' => Loc::getMessage('REFERAL_ENTITY_REFERER_FIELD'),
            ),
            'REFBONUS' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('REFERAL_ENTITY_REFBONUS_FIELD'),
            ),
            'USERID' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('REFERAL_ENTITY_USERID_FIELD'),
            ),
            'COOKIE' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateCookie'),
                'title' => Loc::getMessage('REFERAL_ENTITY_COOKIE_FIELD'),
            ),
            'FROMUSER'  => array(
                'data_type' => 'Bitrix\Main\User',
                'reference' => array(
                    '=this.REFFROM' => 'ref.ID'
                )
            ),
            'USER_ID'  => array(
                'data_type' => 'Bitrix\Main\User',
                'reference' => array(
                    '=this.USERID' => 'ref.ID'
                )
            ),
            'ADDRECORDTYPE' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('REFERAL_ENTITY_ADDRECORDTYPE_FIELD'),
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
     * Returns validators for REFERER field.
     *
     * @return array
     */
    public static function validateReferer()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }
    /**
     * Returns validators for COOKIE field.
     *
     * @return array
     */
    public static function validateCookie()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }
}