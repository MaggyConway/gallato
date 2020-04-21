<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => GetMessage("VBCHBBBONUSPAY_DEFAULT_TEMPLATE_NAME"),
	"DESCRIPTION" => GetMessage("VBCHBBBONUSPAY_DEFAULT_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/sale_account.gif",
    "PATH" => array(
        "ID" => "vbcherepanov",
        "NAME" => Loc::getMessage("VBCHBB_COMP_REFCODE_ROOT"),
    ),
);
?>