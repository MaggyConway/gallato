<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class AccountTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> USER_ID int mandatory
 * <li> TIMESTAMP_X datetime mandatory default 'CURRENT_TIMESTAMP'
 * <li> CURRENT_BUDGET double mandatory default 0.0000
 * <li> CURRENCY string(20) mandatory
 * <li> NOTES string optional
 * </ul>
 *
 * @package Bitrix\Bonus
 **/

class AccountTable extends Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'vbch_bonus_account';
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
                'title' => Loc::getMessage('ACCOUNT_ENTITY_ID_FIELD'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ACCOUNT_ENTITY_USER_ID_FIELD'),
            ),
            'TIMESTAMP_X' => new Main\Entity\DatetimeField('TIMESTAMP_X', array(
                'default_value' => new Main\Type\DateTime(),
                'title' => Loc::getMessage('ACCOUNT_ENTITY_TIMESTAMP_X_FIELD'),
            )),
            'CURRENT_BUDGET' => array(
                'data_type' => 'float',
                'required' => true,
                'title' => Loc::getMessage('ACCOUNT_ENTITY_CURRENT_BUDGET_FIELD'),
            ),
            'CURRENCY' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateCurrency'),
                'title' => Loc::getMessage('ACCOUNT_ENTITY_CURRENCY_FIELD'),
            ),
            'NOTES' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('ACCOUNT_ENTITY_NOTES_FIELD'),
            ),
            'BONUSACCOUNTSID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('BONUS_ENTITY_BONUSACCOUNTSID_FIELD'),
            ),
        );
    }
    /**
     * Returns validators for CURRENCY field.
     *
     * @return array
     */
    public static function validateCurrency()
    {
        return array(
            new Entity\Validator\Length(null, 20),
        );
    }
    public static function OnBeforeDelete(Entity\Event $event){
	    self::adddouble($event,true);
        $primary = $event->getParameter("id");
        $tmp=self::getById($primary)->fetch();
        $transactionList=\ITRound\Vbchbbonus\BonusTable::getList(
            array(
                'filter'=>array('USERID'=>$tmp['USER_ID'],'BONUSACCOUNTSID'=>$tmp['BONUSACCOUNTSID']),
            )
        )->fetchAll();
        unset($tmp);
        if($transactionList){
            foreach($transactionList as $lst){
                \ITRound\Vbchbbonus\BonusTable::delete($lst['ID']);
            }
        }
    }
    public static function onBeforeUpdate (Entity\Event $event){
	   self::adddouble($event);
    }
    public static function onBeforeAdd (Event $event)
    {
	    self::adddouble($event);
	    parent::onBeforeAdd($event); // TODO: Change the autogenerated stub
    }

	private static function adddouble(Entity\Event $event,$del=false){
	    $fields = $event->getParameter('fields');
		if($del){
			$primary = $event->getParameter("id");
			$fields=self::getById($primary)->fetch();
			$res['CURRENT_BUDGET']=$fields['CURRENT_BUDGET'];

		}else{
			$filter=['USER_ID'=>$fields['USER_ID'],'BONUSACCOUNTSID'=>$fields['BONUSACCOUNTSID']];
			$res=self::getList([
				'filter'=>$filter,
				'select'=>['CURRENT_BUDGET']
			])->fetch();
		}

		if($del)
			$old_budget=0;
		else
			$old_budget=is_array($res) ? $res['CURRENT_BUDGET'] :0;

	    $d_fields=[
	        'TIMESTAMP_X'=>new Main\Type\DateTime(),
			'USER_ID'=>$fields['USER_ID'],
		    'BONUSACCOUNTSID'=>$fields['BONUSACCOUNTSID']
	    ];
		$d_fields0=[
			'TIMESTAMP_X'=>new Main\Type\DateTime(),
			'USER_ID'=>-1,
			'BONUSACCOUNTSID'=>$fields['BONUSACCOUNTSID']
		];
		if($del)
			$amount=floatval($old_budget)-floatval($fields['CURRENT_BUDGET']);
		else
	        $amount=floatval($fields['CURRENT_BUDGET'])-floatval($old_budget);
	    if($amount>0){
	    	$d_fields['CREDIT']=$amount;
		    $d_fields0['DEBIT']=$amount;
	    }else{
		    $d_fields['DEBIT']=$amount*(-1);
		    $d_fields0['CREDIT']=$amount*(-1);
	    }

		Main\Application::getConnection()->startTransaction();
		$r=\ITRound\Vbchbbonus\DoubleTable::add($d_fields);

		if($r->isSuccess()){
			Main\Application::getConnection()->commitTransaction();
		}else{
			Main\Application::getConnection()->rollbackTransaction();
		}
		$r=\ITRound\Vbchbbonus\DoubleTable::add($d_fields0);

		if($r->isSuccess()){
			Main\Application::getConnection()->commitTransaction();
		}else{
			Main\Application::getConnection()->rollbackTransaction();
		}
	}
}