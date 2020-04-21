<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
 
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:search.tags.cloud", 
	"main", 
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "N",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => "C0C0C0",
		"COLOR_TYPE" => "Y",
		"FILTER_NAME" => "",
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"PAGE_ELEMENTS" => "150",
		"PERIOD" => "",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "N",
		"SORT" => "NAME",
		"TAGS_INHERIT" => "Y",
		"URL_SEARCH" => "/search/index.php",
		"WIDTH" => "100%",
		"arrFILTER" => array(
			0 => "iblock_content",
		),
		"arrFILTER_iblock_content" => array(
			0 => "5",
		),
		"COMPONENT_TEMPLATE" => "main"
	),
	false
);?>