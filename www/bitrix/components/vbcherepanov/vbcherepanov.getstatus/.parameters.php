<?php
use Bitrix\Main\Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!Loader::includeModule("vbcherepanov.bonus")) return false;
$arComponentParameters = Array(
    "PARAMETERS" => Array(
        "CACHE_TIME" => Array("DEFAULT"=>"3600"),
	    "PROFILETYPE" => array(
		    "PARENT" => "BASE",
		    "NAME" => GetMessage("PROFILESTYPEOUT"),
		    "TYPE" => "STRING",
		    "DEFAULT" => 'ORDER,ORDERX',
	    ),
    ),
);

