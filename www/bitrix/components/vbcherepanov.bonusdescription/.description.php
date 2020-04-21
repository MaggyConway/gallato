<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
    "NAME" => GetMessage("VBCHBB_COMP_DESCR_NAME"),
    "DESCRIPTION" => GetMessage("VBCHBB_COMP_DESCR_DESC"),
    "ICON" => "/images/guide.gif",
    "SORT" => 20,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => "vbchbb",
        "NAME" => GetMessage("VBCHBB_COMP_DESCR_ROOT"),
    ),
);

?>