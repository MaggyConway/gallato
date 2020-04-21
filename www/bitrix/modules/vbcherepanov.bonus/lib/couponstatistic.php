<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class BonucCouponTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> COUPON string(32) mandatory
 * <li> USER_ID int mandatory
 * <li> TIMESTAMP_X datetime optional
 * <li> CLIENT_IP string(17) optional
 * <li> CLIENT_BROWSER string(20) optional
 * <li> CLIENT_REFERER string(255) optional
 * <li> CLIENT_UTM string(255) optional
 * </ul>
 *
 * @package Bitrix\Statistic
 **/

class BonucCouponTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'itr_statistic_bonuc_coupon';
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
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_ID_FIELD'),
            ),
            'COUPON' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateCoupon'),
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_COUPON_FIELD'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_USER_ID_FIELD'),
            ),
            'TIMESTAMP_X' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_TIMESTAMP_X_FIELD'),
            ),
            'CLIENT_IP' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateClientIp'),
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_CLIENT_IP_FIELD'),
            ),
            'CLIENT_BROWSER' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateClientBrowser'),
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_CLIENT_BROWSER_FIELD'),
            ),
            'CLIENT_REFERER' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateClientReferer'),
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_CLIENT_REFERER_FIELD'),
            ),
            'CLIENT_UTM' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateClientUtm'),
                'title' => Loc::getMessage('BONUC_COUPON_ENTITY_CLIENT_UTM_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for COUPON field.
     *
     * @return array
     */
    public static function validateCoupon()
    {
        return array(
            new Main\Entity\Validator\Length(null, 32),
        );
    }
    /**
     * Returns validators for CLIENT_IP field.
     *
     * @return array
     */
    public static function validateClientIp()
    {
        return array(
            new Main\Entity\Validator\Length(null, 17),
        );
    }
    /**
     * Returns validators for CLIENT_BROWSER field.
     *
     * @return array
     */
    public static function validateClientBrowser()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for CLIENT_REFERER field.
     *
     * @return array
     */
    public static function validateClientReferer()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
    /**
     * Returns validators for CLIENT_UTM field.
     *
     * @return array
     */
    public static function validateClientUtm()
    {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }
}