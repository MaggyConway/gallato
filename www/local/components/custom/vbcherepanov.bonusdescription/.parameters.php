<?php
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
Loc::loadMessages(__FILE__);
if(!\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus"))
    return;
$sort=array(
        "TIMESTAMP_X"=>Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_TIMESTAMP"),
        "BONUS"=>Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_BONUS"),
        "TYPE"=>Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_TYPE")
    );
$levsort=array(
        "ASC"=>Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_ASC"),
        "DESC"=>Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_DESC")
    );
$arComponentParameters = Array(
    "PARAMETERS" => Array(
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
        "ORDER" => Array(
            "NAME" => Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_NAME"),
            "TYPE"=>"LIST",
            "MULTIPLE"=>"N",
            "VALUES" => $sort,
        ),
        "ORDERDEC" => Array(
            "NAME" => Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_LABEL"),
            "TYPE"=>"LIST",
            "MULTIPLE"=>"N",
            "VALUES" => $levsort,
        ),
        "NOTACTIVE" => Array(
            "NAME" => Loc::getMessage("VBCH_BONUSDESCRIPTION_SORT_NOACTIVE"),
            "TYPE" => "CHECKBOX",
            "MULTIPLE" => "N",
            "DEFAULT" => "N",
        ),
        "CACHE_TIME" => Array("DEFAULT"=>"3600"),
    )
);
?>