<?php
use Bitrix\Main\Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!Loader::includeModule("vbcherepanov.bonus")) return false;
$arComponentParameters = Array(
    "PARAMETERS" => Array(
        "CACHE_TIME" => Array("DEFAULT"=>"3600"),
        "SHOW_INNER_ACCOUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SHOW_INNER_ACCOUNT"),
            "TYPE" => "CHECKBOX",
            "VALUES" => "Y",
        ),
        "SHOW_BONUS_ACCOUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SHOW_BONUS_ACCOUNT"),
            "TYPE" => "CHECKBOX",
            "VALUES" => "Y",
        ),
    ),
);

