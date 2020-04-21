<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!Loader::includeModule("vbcherepanov.bonus")) return false;
$arComponentParameters = Array(
    "PARAMETERS" => Array(
        'ONLY_MAIN'=>Array(
            "NAME" => Loc::getMessage("VBCH_BONUSELEMENT_ONLY_MAIN"),
            "TYPE"=>"CHECKBOX",
            "DEFAULT" => "N",
        ),
        "CACHE_TIME" => Array("DEFAULT"=>"3600"),
    ),
);

