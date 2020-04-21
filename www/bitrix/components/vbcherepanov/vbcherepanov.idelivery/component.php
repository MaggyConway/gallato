<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$module_id="vbcherepanov.idelivery";
CJSCore::Init(array("jquery"));
if(!CModule::IncludeModule($module_id))
{
	ShowError(GetMessage("IDELIVERY_MODULE_NOT_INSTALLED"));
	return;
}
$arResult=CVBCHiDelivery::GetShopCID($arParams['ORDER_ID']);
$APPLICATION->AddHeadScript("http://online.dcl24.ru/script/tracker.js");
$this->IncludeComponentTemplate();
?>