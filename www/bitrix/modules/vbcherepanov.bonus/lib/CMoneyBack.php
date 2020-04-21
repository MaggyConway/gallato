<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class MoneybackTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory
 * <li> LID string(2) mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> BONUS double mandatory default 0.0000
 * <li> USERID int mandatory
 * <li> BACK_DATE datetime mandatory
 * <li> BACK_PERIOD datetime mandatory
 * <li> DESCRIPTION string optional
 * <li> STATUS int mandatory
 * </ul>
 *
 * @package Bitrix\Moneyback
 **/

class MoneybackTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'vbch_moneyback';
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
				'title' => Loc::getMessage('MONEYBACK_ENTITY_ID_FIELD'),
			),
			'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('MONEYBACK_ENTITY_TIMESTAMP_X_FIELD'),
				)
			),
			'LID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateLid'),
				'title' => Loc::getMessage('MONEYBACK_ENTITY_LID_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
				'title' => Loc::getMessage('MONEYBACK_ENTITY_ACTIVE_FIELD'),
			),
			'BONUS' => array(
				'data_type' => 'float',
				'required' => true,
				'title' => Loc::getMessage('MONEYBACK_ENTITY_BONUS_FIELD'),
			),
			'USERID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('MONEYBACK_ENTITY_USERID_FIELD'),
            ),
            'BONUSACCOUNTSID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('MONEYBACK_ENTITY_BONUSACCOUNTSID_FIELD'),
            ),
			'BACK_DATE' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('MONEYBACK_ENTITY_BACK_DATE_FIELD'),
			),
			'BACK_PERIOD' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('MONEYBACK_ENTITY_BACK_PERIOD_FIELD'),
			),
			'DESCRIPTION' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('MONEYBACK_ENTITY_DESCRIPTION_FIELD'),
			),
			'USERREKV'=>array(
                'data_type' => 'string',
                'title' => Loc::getMessage('MONEYBACK_ENTITY_DESCRIPTION_FIELD'),
            ),
			'STATUS' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MONEYBACK_ENTITY_STATUS_FIELD'),
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
}