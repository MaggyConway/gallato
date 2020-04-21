<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("VBCHBB_BONUSMAIL_DEFAULT_TEMPLATE_NAME"),
	"TYPE" => "mail",
	"DESCRIPTION" => GetMessage("VBCHBB_BONUSMAIL_TEMPLATE_DESCRIPTION"),
	"ICON" => "/images/sale_basket.gif",
	"PATH" => array(
		"ID" => "e-store",
		"CHILD" => array(
			"ID" => "vbchbnus_mail",
			"NAME" => GetMessage("VBCHBB_BONUSMAIL_NAME")
		)
	),
);
?>