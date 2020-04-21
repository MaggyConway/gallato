<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("VBCH_REFREG_COMPONENT_NAME"), //component name lang
	"DESCRIPTION" => GetMessage("VBCH_REFREG_COMPONENT_DESCRIPTION"), //component description lang
	"ICON" => "", // component image path like "/images/cat_detail.gif"
	"CACHE_PATH" => "Y", // button for clear cache
	"SORT" => 10,
	"PATH" => array(
		"ID" => "vbcherepanov", //main group name
		"NAME" => GetMessage("VBCH_REFREG_COMPONENT_MAIN_GROUP_NAME"), //main group name
	),
);

?>