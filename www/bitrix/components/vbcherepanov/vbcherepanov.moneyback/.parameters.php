<?php
use Bitrix\Main\Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!Loader::includeModule("vbcherepanov.bonus")) return false;
$arbonusOut=array(
	'BONUS'=>GetMessage('LAST_TRNSACTION_BONUS'),
	'DATE'=>GetMessage('LAST_BONUS_ACTIVETO'),
);
$site = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arFilter = Array("TYPE_ID" => "VBCHBONUS_SENDMONEY", "ACTIVE" => "Y");
if($site !== false)
	$arFilter["LID"] = $site;

$arEvent = Array();
$dbType = CEventMessage::GetList($by="ID", $order="DESC", $arFilter);
while($arType = $dbType->GetNext())
	$arEvent[$arType["ID"]] = "[".$arType["ID"]."] ".$arType["SUBJECT"];

$bb=new \ITRound\Vbchbbonus\Vbchbbcore();
$pr=$bb->GetCurrentProfiles();
if(sizeof($pr)>0){
    foreach($pr as $p){
        $profile[$p["ID"]] = "[".$p["ID"]."] ".$p["NAME"];
    }
}

$arComponentParameters = Array(
    "PARAMETERS" => Array(
        "CACHE_TIME" => Array("DEFAULT"=>"3600"),

	    "PROFILEOUT"=>Array(
            "NAME" => GetMessage("SENDMONYE_PROFILEOUT"),
            "TYPE"=>"LIST",
            "VALUES" => $profile,
            "DEFAULT"=>"",
            "MULTIPLE"=>"Y",
            "COLS"=>25,
            "PARENT" => "BASE",
        ),
        "SHOWBUTTON" => array(
		    "PARENT" => "BASE",
		    "NAME" => GetMessage("SHOWBUTTONSEND"),
		    "TYPE" => "CHECKBOX",
		    "DEFAULT" => "N"
	    ),
        "SHOWFIELDSPARAMS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("SHOWFIELDSPARAMS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
        ),
	    "EVENT_MESSAGE_ID" => Array(
		    "NAME" => GetMessage("SENDMONYE_EMAIL_TEMPLATES"),
		    "TYPE"=>"LIST",
		    "VALUES" => $arEvent,
		    "DEFAULT"=>"",
		    "MULTIPLE"=>"Y",
		    "COLS"=>25,
		    "PARENT" => "BASE",
	    ),

    ),
);

