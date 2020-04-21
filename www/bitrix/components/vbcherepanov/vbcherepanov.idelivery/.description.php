<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("VBCH_MOBIDEL_COMP_NAME"),
	"DESCRIPTION" => GetMessage("VBCH_IDELIVERY_COMP_DESC"),
	"ICON" => "/images/news_detail.gif",
	"SORT" => 30,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "vbcherepanov",
		"CHILD" => array(
			"ID" => "vbch_idelivery",
			"NAME" => GetMessage("VBCH_IDELIVERY_COMP_NAME"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "vbchidelivery",
			),
		),
	),
);

?>