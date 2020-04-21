<?php
use Bitrix\Main\Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!Loader::includeModule("vbcherepanov.bonus")) return false;
$arbonusOut=array(
	'BONUS'=>GetMessage('LAST_TRNSACTION_BONUS'),
	'DATE'=>GetMessage('LAST_BONUS_ACTIVETO'),
);
$arComponentParameters = Array(
    "PARAMETERS" => Array(
        "CACHE_TIME" => Array("DEFAULT"=>"3600"),

	    "MONEYBACKACCOUNT" => array(
		    "PARENT" => "BASE",
		    "NAME" => GetMessage("MPNEYBACKACCOUNT"),
		    "TYPE" => "STRING",
		    "DEFAULT" => '0',
	    ),
	    "PROFILETYPE" => array(
		    "PARENT" => "BASE",
		    "NAME" => GetMessage("PROFILESTYPEOUT"),
		    "TYPE" => "STRING",
		    "DEFAULT" => 'ORDER,ORDERX',
	    ),
    ),
);

