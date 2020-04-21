<?php
namespace ITRound\Vbchbbonus;


use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class DoubleTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> USER_ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory
 * <li> DEBIT double optional default 0.0000
 * <li> CREDIT double optional default 0.0000
 * <li> BONUSACCOUNTSID int mandatory
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class DoubleTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'vbch_bonus_double';
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
				'title' => Loc::getMessage('DOUBLE_ENTITY_ID_FIELD'),
			),
			'USER_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('DOUBLE_ENTITY_USER_ID_FIELD'),
			),
			'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
				'default_value' => new Main\Type\DateTime(),
				'title' => Loc::getMessage('DOUBLE_ENTITY_TIMESTAMP_X_FIELD'),
			)),
			'DEBIT' => array(
				'data_type' => 'float',
				'title' => Loc::getMessage('DOUBLE_ENTITY_DEBIT_FIELD'),
			),
			'CREDIT' => array(
				'data_type' => 'float',
				'title' => Loc::getMessage('DOUBLE_ENTITY_CREDIT_FIELD'),
			),
			'BONUSACCOUNTSID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('DOUBLE_ENTITY_BONUSACCOUNTSID_FIELD'),
			),
		);
	}
}