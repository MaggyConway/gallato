<?php

namespace Sale\Handlers\PaySystem;


use Bitrix\Main\Request;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PriceMaths;
use Bitrix\Sale\Payment;
use Bitrix\Sale\Result;
use Bitrix\Sale\Internals\UserBudgetPool;
use Bitrix\Main\Entity\EntityError;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\ResultError;

Loc::loadMessages(__FILE__);

class InnerBonusHandler extends PaySystem\BaseServiceHandler implements PaySystem\IRefund
{
	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return PaySystem\ServiceResult
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 */
	public function initiatePay(Payment $payment, Request $request = null)
	{
		$result = new PaySystem\ServiceResult();

		/** @var \Bitrix\Sale\PaymentCollection $paymentCollection */
		$paymentCollection = $payment->getCollection();

		if ($paymentCollection)
		{
			/** @var \Bitrix\Sale\Order $order */
			$order = $paymentCollection->getOrder();
			if ($order)
			{
				$res = $payment->setPaid('Y');
				if ($res->isSuccess())
				{
					$res = $order->save();
					if ($res)
						$result->addErrors($res->getErrors());
				}
				else
				{
					$result->addErrors($res->getErrors());
				}
			}
		}

		return $result;
	}

	/**
	 * @return array
	 */
	public function getCurrencyList()
	{
		return array();
	}

	/**
	 * @param Payment $payment
	 * @param int $refundableSum
	 * @return PaySystem\ServiceResult
	 */
	public function refund(Payment $payment, $refundableSum)
	{
		$result = new PaySystem\ServiceResult();
		/** @var \Bitrix\Sale\PaymentCollection $paymentCollection */
		$paymentCollection = $payment->getCollection();

		/** @var \Bitrix\Sale\Order $order */
		$order = $paymentCollection->getOrder();
		$key = $order->getUserId();
		$SID=$order->getSiteId();
		\Bitrix\Main\Loader::includeModule('vbcherepanov.bonus');
		$BBCORE=new \ITRound\Vbchbbonus\Vbchbbcore();
		$BBCORE->SITE_ID=$SID;
		$prof = \ITRound\Vbchbbonus\CvbchbonusprofilesTable::getList(array(
			'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $BBCORE->SITE_ID),
		))->fetch();
		$BBCORE->AddBonus(array('bonus'=>$refundableSum,
			'ACTIVE'=>'Y',
			'ACTIVE_FROM'=>'',
			'ACTIVE_TO'=>'',
			'CURRENCY'=>''),
			array('SITE_ID'=>$BBCORE->SITE_ID,'USER_ID'=>$key,'IDUNITS'=>'RETURN_P_'.$order->getId(),'DESCRIPTION'=>'Return Pay part order'),$prof,true);


		return $result;
	}

	/**
	 * @param Payment $payment
	 * @return Result
	 */
	public function creditNoDemand(Payment $payment)
	{
		$result = new Result();
		/** @var \Bitrix\Sale\PaymentCollection $collection */
		$collection = $payment->getCollection();

		/** @var \Bitrix\Sale\Order $order */
		$order = $collection->getOrder();

		if ($this->isUserBudgetLock($order))
		{
			$result->addError(new EntityError(Loc::getMessage('ORDER_PSH_BONUS_ERROR_USER_BUDGET_LOCK')));
			return $result;
		}
		$key = $order->getUserId();
		$SID=$order->getSiteId();
		\Bitrix\Main\Loader::includeModule('vbcherepanov.bonus');
		$BBCORE=new \ITRound\Vbchbbonus\Vbchbbcore();
		$BBCORE->SITE_ID=$SID;
		$paymentSum = PriceMaths::roundByFormatCurrency($payment->getSum(), $order->getCurrency());
		$dbAccountUser=\ITRound\Vbchbbonus\AccountTable::getList(array(
			'filter'=>array("USER_ID"=>$key),
		))->fetch();
		if($BBCORE->CheckArray($dbAccountUser)) {
			$userBudget = $dbAccountUser["CURRENT_BUDGET"];
		}else $userBudget=0;

		if($userBudget >= $paymentSum){
			$prof = \ITRound\Vbchbbonus\CvbchbonusprofilesTable::getList(array(
				'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $BBCORE->SITE_ID),
			))->fetch();
			$BBCORE->AddBonus(array('bonus'=>( $paymentSum * -1 ),
				'ACTIVE'=>'Y',
				'ACTIVE_FROM'=>'',
				'ACTIVE_TO'=>'',
				'CURRENCY'=>''),
				array('SITE_ID'=>$BBCORE->SITE_ID,'USER_ID'=>$key,'IDUNITS'=>'P_'.$order->getId(),'DESCRIPTION'=>'Pay part order'),$prof,true);
			\ITRound\Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
		}
		else
			$result->addError(new EntityError(Loc::getMessage('ORDER_PSH_BONUS_ERROR_INSUFFICIENT_MONEY')));

		return $result;
	}

	/**
	 * @param Payment $payment
	 * @return Result
	 */
	public function debitNoDemand(Payment $payment)
	{
		return $this->refund($payment, $payment->getSum());
	}

	/**
	 * @param Order $order
	 * @return bool
	 */
	private function isUserBudgetLock(Order $order)
	{
		return false;
	}
}