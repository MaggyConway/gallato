<?php
if(!CModule::IncludeModule('vbcherepanov.bonus'))
    ShowError(GetMessage("VBCHBB_NOT_MODULE"));
if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("VBCHBB_ACCESS_DENIED"));
}
$arResult["ITEMS"]=array();
if ($this->StartResultCache())
{
	$res=VBCHBonus::GetByUSER($USER->GetID());
	while($arItem=$res->GetNext())
	{
		if($arItem["ACTIVE"]=="Y")
		$arResult["ITEMS"][]=array(
			"CREATED"=>date("d.m.Y h:m:s",strtotime($arItem["TIMESTAMP_X"])),
			"ACTIVE"=>$arItem["ACTIVE"]=="Y" ? GetMessage("VBCHBB_YES") : GetMessage("VBCHBB_NO"),
			"ACTIVE_FROM"=>date("d.m.Y",strtotime($arItem["ACTIVE_FROM"])),
			"ACTIVE_TO"=>$arItem["ACTIVE_TO"] ? date("d.m.Y",strtotime($arItem["ACTIVE_TO"])) : "",
			"SUMM"=>CVBCHBB::declOfNum(SITE_ID,$arItem["BONUS"]),
			"DESCRIPTION"=>$arItem["DESCRIPTION"],
		);
	}
	$this->IncludeComponentTemplate();
}
