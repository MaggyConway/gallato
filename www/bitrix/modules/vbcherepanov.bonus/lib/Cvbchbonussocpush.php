<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Entity,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class SocialpushTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> SOCIAL string(25) optional
 * <li> SOCIALTEXT string optional
 * <li> SOCIALUNIQ string(100) optional
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class CvbchbonussocpushTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus_socialpush';
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
                'title' => Loc::getMessage('SOCIALPUSH_ENTITY_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('SOCIALPUSH_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'SOCIAL' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateSocial'),
                'title' => Loc::getMessage('SOCIALPUSH_ENTITY_SOCIAL_FIELD'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('SOCIALPUSH_ENTITY_USER_ID_FIELD'),
            ),
            'SOCIALTEXT' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('SOCIALPUSH_ENTITY_SOCIALTEXT_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for SOCIAL field.
     *
     * @return array
     */
    public static function validateSocial()
    {
        return array(
            new Main\Entity\Validator\Length(null, 25),
        );
    }
}