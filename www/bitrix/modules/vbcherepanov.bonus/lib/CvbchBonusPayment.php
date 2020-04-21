<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Entity,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale\Internals\PaySystemActionTable,
    Bitrix\Main\IO\File,
    Bitrix\Main\Application;
Loc::loadMessages(__FILE__);

class CvbchBonusPayment {

    const CACHE_ID = "ITROUND_SALE_BONUSINNER_PS_ID";
    const TTL = 31536000;
    public static function GetBonusPayment(){
    	\Bitrix\Main\Loader::includeModule("sale");
        $id = 0;
        $cacheManager = Application::getInstance()->getManagedCache();

        if($cacheManager->read(self::TTL, self::CACHE_ID))
            $id = $cacheManager->get(self::CACHE_ID);

        if ($id <= 0)
        {
	        $data = \Bitrix\Sale\Internals\PaySystemActionTable::getList(
		        array(
			        'select' => array('ID'),
			        'filter' => array('ACTION_FILE' => 'innerbonus'),
		        )
	        )->fetch();

            if ($data === null)
                $id = self::createBonusPaySystem();
            else
                $id = $data['ID'];

            $cacheManager->set(self::CACHE_ID, $id);
        }
        return $id;
    }
    private static function createBonusPaySystem()
    {
        $paySystemSettings = array(
            'NAME' => Loc::getMessage('SALE_PS_MANAGER_BONUS_NAME'),
            'PSA_NAME' => Loc::getMessage('SALE_PS_MANAGER_BONUS_NAME'),
            'ACTION_FILE' => 'innerbonus',
            'ACTIVE' => 'Y',
            'NEW_WINDOW' => 'N'
        );
        $imagePath = Application::getDocumentRoot().'/local/php_interface/include/sale_payment/innerbonus/bonus-ps.gif';
        if (File::isFileExists($imagePath))
        {
            $paySystemSettings['LOGOTIP'] = \CFile::MakeFileArray($imagePath);
            $paySystemSettings['LOGOTIP']['MODULE_ID'] = "sale";
            \CFile::SaveForDB($paySystemSettings, 'LOGOTIP', 'sale/paysystem/logotip');
        }
        $result = \PaySystemActionTable::add($paySystemSettings);
        if ($result->isSuccess())
            return $result->getId();
        return 0;
    }
    public static function InnerPay($orderID,$summPaid,$pf){
        if($summPaid>0){
            $order=\Bitrix\Sale\Order::load(intval($orderID));
            $InnerpayID=\Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId();
            $paymentCollection=$order->getPaymentCollection();
			$currentPay=$paymentCollection->current();
			$pr=$order->getField('PRICE');
			$sum=floatval($pr)-$pf;
			$currentPay->setField("SUM",$sum);
            $payment=$paymentCollection->createItem(\Bitrix\Sale\PaySystem\Manager::getObjectById($InnerpayID));
            $payment->setField("SUM",$summPaid);
            $payment->setField("CURRENCY",$order->getField("CURRENCY"));
            $payment->setField("PAID","Y");
            $order->save();
        }
    }
    public static function InnerBonusPay($orderID,$summPaid,$pf){
        if($summPaid>0){
            $order=\Bitrix\Sale\Order::load(intval($orderID));
            $bouspayID=self::GetBonusPayment();
            $paymentCollection=$order->getPaymentCollection();
			$currentPay=$paymentCollection->current();
			$pr=$order->getField('PRICE');
			$sum=floatval($pr)-$pf;
			$currentPay->setField("SUM",$sum);
            $payment=$paymentCollection->createItem(\Bitrix\Sale\PaySystem\Manager::getObjectById($bouspayID));
            $payment->setField("SUM",$summPaid);
            $payment->setField("CURRENCY",$order->getField("CURRENCY"));
            $payment->setField("PAID","Y");
            $order->save();
        }
    }
}