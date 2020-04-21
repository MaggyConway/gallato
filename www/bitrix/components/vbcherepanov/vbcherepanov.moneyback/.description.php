<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("VBCHBB_COMP_MONEYBACK_NAME"),
	"DESCRIPTION" => Loc::getMessage("VBCHBB_COMP_MONEYBACK_DESCRIPTION"),
	"ICON" => "/images/sale_account.gif",
	"SORT" => 20,
    "CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "vbcherepanov",
        "NAME" => Loc::getMessage("VBCHBB_COMP_MONEYBACK_ROOT"),
    ),
);
?>