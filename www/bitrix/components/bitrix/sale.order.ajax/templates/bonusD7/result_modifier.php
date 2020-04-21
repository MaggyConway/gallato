<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus");

\ITRound\Vbchbbonus\Vbchbbcore::GetBonusPaySystem($arResult);
if(!is_array($arResult['TYPEPAY']))
    $arResult['TYPEPAY']=array();
$arResult['SYSTEMPAY']['DISPLAY']=in_array('SYSTEMPAY',$arResult['TYPEPAY']);
$arResult['BONUSPAY']['DISPLAY']=in_array('BONUSPAY',$arResult['TYPEPAY']);
$arResult['BONUSTOPAY']['DISPLAY']=($arResult['SYSTEMPAY']['BONUSORDERPAY'] || $arResult['BONUSPAY']['BONUSORDERPAY']);
$arResult['BONUSTOPAY']['LOGOTIP_SRC']=$this->GetFolder().'/images/bonus-to-pay.gif';
$arResult['BONUSTOPAY']['LOGOTIP']['SRC']=$arResult['BONUSTOPAY']['LOGOTIP_SRC'];
$arResult['BONUSTOPAY']['NAME']=Loc::getMessage("BONUS_TO_PAY");


if ($_POST["PAY_BONUS_ACCOUNT"] == "Y") {
    $arResult['USER_VALS']["PAY_BONUS_ACCOUNT"] = "Y";
}
if ($_POST["PAY_BONUSORDERPAY"] == "Y") {
    $arResult['USER_VALS']["PAY_BONUSORDERPAY"] = "Y";
}
