<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!\Bitrix\MAin\Loader::includeModule("vbcherepanov.bonus"))
{
	ShowError(GetMessage("VBCHBBBONUS_MODULE_NOT_INSTALL"));
	return;
}

$USER_ID = intval($arParams["USER_ID"]);
$BONUS = intval($arParams["BONUS"]);
if($USER_ID <= 0 || $BONUS <=0)
{
	return;
}
$arResult['BONUS'] = $BONUS;
$arResult['LINK'] = $this->CreateHashLink();
$this->IncludeComponentTemplate();
?>