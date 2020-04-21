<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("vbcherepanov.bonus"))
{
	ShowError(GetMessage("VBCHBB_MODULE_NOT_INSTALL"));
	return;
}

$not=true;
if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("VBCHBB_ACCESS_DENIED"));
}
$UA=CVBCHBB::GetOptions(SITE_ID,"USED_ACCOUNT");
$cur=CVBCHBB::ReturnCurrency(SITE_ID,"1");
if($cur==1) $cur=GetMessage("VBCHBB_CURRENCY_MOD");
if($UA["OPTION"]=="Y"){
	$dbAccountList = CSaleUserAccount::GetList(
		array("CURRENCY" => "ASC"),
		array("USER_ID" => IntVal($USER->GetID())),
		false,
		false,
		array("ID", "CURRENT_BUDGET", "CURRENCY", "TIMESTAMP_X")
	);
	if($arAccountList = $dbAccountList->GetNext())
	{	$not=false;
		$arResultTmp = Array();
		$arResult["DATE"] = str_replace("#DATE#", date(CDatabase::DateFormatToPHP(CSite::GetDateFormat("SHORT", SITE_ID))), GetMessage("VBCHBB_MY_ACCOUNT"));
		do
		{
			$arResultTmp["CURRENCY"] = CCurrencyLang::GetByID($arAccountList["CURRENCY"], LANGUAGE_ID);
			$arResultTmp["ACCOUNT_LIST"] = $arAccountList;
			$arResultTmp["INFO"] = str_replace("#CURRENCY#", $arResultTmp["CURRENCY"]["CURRENCY"]." (".$arResultTmp["CURRENCY"]["FULL_NAME"].")", str_replace("#SUM#", SaleFormatCurrency($arAccountList["CURRENT_BUDGET"], $arAccountList["CURRENCY"]), GetMessage("VBCHBB_IN_CUR")));
			$arResult["ACCOUNT_LIST"][] = $arResultTmp;
		}
		while($arAccountList = $dbAccountList->GetNext());
		
	}
	else
		$not=true;
}else{
	$dbAccountUser=VBCHBonusAccount::GetByUSER(IntVal($USER->GetID()));
	if($arAccount=$dbAccountUser->Fetch()){
		$not=false;
		$arResult["DATE"] = str_replace("#DATE#", date(CDatabase::DateFormatToPHP(CSite::GetDateFormat("SHORT", SITE_ID))), GetMessage("VBCHBB_MY_ACCOUNT"));
		$arResultTmp["CURRENCY"]=$cur;
		$arResultTmp["ACCOUNT_LIST"] = $arAccount;
		$arResultTmp["INFO"] = str_replace("#CURRENCY#", $arResultTmp["CURRENCY"], str_replace("#SUM#", CVBCHBB::declOfNum(SITE_ID,$arAccount["CURRENT_BUDGET"]), GetMessage("VBCHBB_IN_CUR")));
			$arResult["ACCOUNT_LIST"][] = $arResultTmp;
	}
	else $not=true;
	
		
}
if($not) $arResult["ERROR_MESSAGE"] = GetMessage("VBCHBB_NO_ACCOUNT");
$this->IncludeComponentTemplate();
?>