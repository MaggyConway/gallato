<?php

namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Sale,
    Bitrix\Currency,
    Bitrix\Catalog;
use ITRound\Vbchbbonus;

if (!Loader::includeModule('vbcherepanov.bonus'))
    return false;
if (!Loader::includeModule('sale'))
    return false;
/**
 * Class ProviderAccountPay
 * @package Bitrix\Sale
 */
class ProviderBonusPay implements \IBXSaleProductProvider
{
    /**
     * @param array $fields
     * @return array
     */
    public static function GetProductData($fields)
    {
        $fields["CAN_BUY"] = 'Y';
        $fields["AVAILABLE_QUANTITY"] = 100000000;
        return $fields;

    }

    public static function OrderProduct($fields)
    {
        $fields["AVAILABLE_QUANTITY"] = 'Y';
        return $fields;
    }

    public static function CancelProduct($fields)
    {
    }

    public static function DeliverProduct($fields)
    {
    }

    public static function ViewProduct($fields)
    {
    }

    public static function RecurringOrderProduct($fields)
    {
    }

    public static function GetStoresCount($arParams = array())
    {
    }

    public static function GetProductStores($fields)
    {
    }

    public static function ReserveProduct($fields)
    {
        $fields['QUANTITY_RESERVED'] = $fields['QUANTITY_ADD'];
        $fields['RESULT'] = true;
        return $fields;
    }

    public static function CheckProductBarcode($fields)
    {
    }

    /**
     * @param array $fields
     * @return array
     */
    public static function DeductProduct($fields)
    {
        $use_coupone=false;
        /** @var Sale\BasketItem $basketItem*/
        $basketItem = $fields['BASKET_ITEM'];
        $orderId = (int)$basketItem->getField('ORDER_ID');
        $currency = $basketItem->getField('CURRENCY');

        $propertyCollection = $basketItem->getPropertyCollection();

        $item = $propertyCollection->getPropertyValues();
        if(array_key_exists("SUM_OF_CHARGE",$item))
            $use_coupone=false;
        if(array_key_exists("SUM_OF_COUPONE",$item))
            $use_coupone=true;

        $sum = (float)($item[(!$use_coupone ? 'SUM_OF_CHARGE' : 'SUM_OF_COUPONE')]['VALUE']) * (float)($basketItem->getQuantity());
        if($use_coupone)
            $coupon = $item['COUPONE_CODE']['VALUE'];

        /** @var Basket $basket */
        $basket = $basketItem->getCollection();
        $order = $basket->getOrder();
        $userId = $order->getUserId();

        if(!$use_coupone) {
            $BonusCore = new Vbchbbcore();
            $res = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $BonusCore->SITE_ID),
            ))->fetchAll();
            global $USER;
            if ($BonusCore->CheckArray($res)) {
                foreach ($res as $prof) {
                    $l = Vbchbbcore::CheckSerialize($prof['NOTIFICATION']);
                    $l['TRANSACATIONMESSAGE'] = "Payment to user bonus account";
                    $prof['NOTIFICATION'] = base64_encode(serialize($l));
                    $check = ($prof['ISADMIN'] == 'Y');
                    $check = ($check) ? $USER->isAdmin() : $check;
                    if ($check) {
                        $BonusCore->AddBonus(
                            array('bonus' => ($fields["UNDO_DEDUCTION"] === 'N' ? $sum : -$sum),
                                'ACTIVE' => 'Y',
                                'ACTIVE_FROM' => '',
                                'ACTIVE_TO' => '',
                                'CURRENCY' => $currency),

                            array('SITE_ID' => $BonusCore->SITE_ID,
                                'USER_ID' => $userId,
                                'IDUNITS' => 'PAYMENT_TO_BONUS_ACCOUNT_' . $userId . '_' . ($fields["UNDO_DEDUCTION"] === 'N' ? $sum : -$sum) . '_' . time(),
                                'DESCRIPTION' => "Payment to user bonus account"
                            )
                            , $prof, true);
                        Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
                    }
                }
                $resultUpdateUserAccount = true;
            }
        }else{
            $ps=1;
            $ff = [
                'ACTIVE' => 'Y',
                'ACTIVE_FROM' => '',
                'ACTIVE_TO' => '',
                'COUPON' => $coupon,
                'TYPE' => 2,
                'USER_ID' => $userId,
                'DESCRIPTION' => 'Из заказа '.$order->getID(),
                'BONUS' => $sum,
                'BONUSLIVE' => ['PERIOD'=>'A'],
                'BONUSACTIVE' => '',
                'BONUSACCOUNTSID' => $ps,
                'MAX_USE' => 0,
                'USE_COUNT'=>0,
            ];
            $l=CouponTable::add($ff);
            $resultUpdateUserAccount = true;
        }

        if ($resultUpdateUserAccount)
        {
            $fields['RESULT'] = true;
        }
        else
        {
            return false;
        }

        return $fields;
    }
}