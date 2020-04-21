<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("VBCH_BONUSFORORDER_COMPONENT_NAME"),
	"DESCRIPTION" => Loc::getMessage("VBCH_BONUSFORORDER_COMPONENT_DESCRIPTION"),
	"ICON" => "",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "vbcherepanov",
		"NAME" => Loc::getMessage("VBCH_BONUSFORORDER_COMP_ACCOUNT_ROOT"),
	),
);
?>