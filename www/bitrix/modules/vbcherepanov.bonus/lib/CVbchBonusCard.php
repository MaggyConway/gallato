<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class CardTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> LID string(2) mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> USERID int optional
 * <li> NUM string(50) mandatory
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class BonusCardTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'vbch_bonus_card';
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
				'title' => Loc::getMessage('CARD_ENTITY_ID_FIELD'),
			),
			'TIMESTAMP_X' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('CARD_ENTITY_TIMESTAMP_X_FIELD'),
			),
			'LID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateLid'),
				'title' => Loc::getMessage('CARD_ENTITY_LID_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'boolean',
				'values' => array('N', 'Y'),
				'title' => Loc::getMessage('CARD_ENTITY_ACTIVE_FIELD'),
			),
			'USERID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('CARD_ENTITY_USERID_FIELD'),
			),
			'DEFAULTBONUS' => array(
				'data_type' => 'float',
				'required' => true,
				'title' => Loc::getMessage('CARD_ENTITY_DEFAULTBONUS_FIELD'),
			),
			'BONUSACCOUNTS' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('CARD_ENTITY_BONUSACCOUNTS_FIELD'),
			),
			'NUM' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateNum'),
				'title' => Loc::getMessage('CARD_ENTITY_NUM_FIELD'),
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
	 * Returns validators for NUM field.
	 *
	 * @return array
	 */
	public static function validateNum()
	{
		return array(
			new Main\Entity\Validator\Length(null, 50),
		);
	}
}