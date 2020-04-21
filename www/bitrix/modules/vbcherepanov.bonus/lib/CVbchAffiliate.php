<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class AffiliateTable
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
 * <li> PROMOCODE string(50) optional
 * <li> DOMAINE string(255) optional
 * <li> URL string optional
 * <li> COMMISIA double optional
 * <li> COMMISIAPROMO double optional
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class CVbchAffiliateTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus_affiliate';
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
                'title' => Loc::getMessage('AFFILIATE_ENTITY_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('AFFILIATE_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'LID' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateLid'),
                'title' => Loc::getMessage('AFFILIATE_ENTITY_LID_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('AFFILIATE_ENTITY_ACTIVE_FIELD'),
            ),
            'BONUS' => array(
                'data_type' => 'float',
                'required' => true,
                'title' => Loc::getMessage('AFFILIATE_ENTITY_BONUS_FIELD'),
            ),
            'USERID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('AFFILIATE_ENTITY_USERID_FIELD'),
            ),
            'ACTIVE_FROM' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('AFFILIATE_ENTITY_ACTIVE_FROM_FIELD'),
            ),
            'ACTIVE_TO' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('AFFILIATE_ENTITY_ACTIVE_TO_FIELD'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('AFFILIATE_ENTITY_NAME_FIELD'),
            ),
            'PROMOCODE' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validatePromocode'),
                'title' => Loc::getMessage('AFFILIATE_ENTITY_PROMOCODE_FIELD'),
            ),
            'DOMAINE' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateDomaine'),
                'title' => Loc::getMessage('AFFILIATE_ENTITY_DOMAINE_FIELD'),
            ),
            'URL' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('AFFILIATE_ENTITY_URL_FIELD'),
            ),
            'COMMISIA' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('AFFILIATE_ENTITY_COMMISIA_FIELD'),
            ),
            'COMMISIAPROMO' => array(
                'data_type' => 'string',
                'title' => Loc::getMessage('AFFILIATE_ENTITY_COMMISIAPROMO_FIELD'),
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
     * Returns validators for PROMOCODE field.
     *
     * @return array
     */
    public static function validatePromocode()
    {
        return array(
            new Main\Entity\Validator\Length(null, 50),
        );
    }
    /**
     * Returns validators for DOMAINE field.
     *
     * @return array
     */
    public static function validateDomaine()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
}