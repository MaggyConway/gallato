<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class LinkmailTable
 *
 * Fields:
 * <ul>
 * <li> BONUS double mandatory default 0.0000
 * <li> USER_ID int mandatory
 * <li> HASH string(32) mandatory
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class LinkmailTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus_linkmail';
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
                'title' => Loc::getMessage('LINKMAIL_ENTITY_ID_FIELD'),
            ),
            'BONUS' => array(
                'data_type' => 'float',
                'required' => true,
                'title' => Loc::getMessage('LINKMAIL_ENTITY_BONUS_FIELD'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('LINKMAIL_ENTITY_USER_ID_FIELD'),
            ),
            'HASH' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateHash'),
                'title' => Loc::getMessage('LINKMAIL_ENTITY_HASH_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for HASH field.
     *
     * @return array
     */
    public static function validateHash()
    {
        return array(
            new Main\Entity\Validator\Length(null, 32),
        );
    }
}