<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("vbcherepanov.bonus"))
    return;

$bonus=new \ITRound\Vbchbbonus\Vbchbbcore();

$profile=array();

$arProf=array();
foreach($bonus->PROFILES as $prof){
    if($prof['SITE']==$bonus->SITE_ID){
        $profile[]=$prof;
        $arProf[$prof['ID']]='['.$prof['ID'].']'.$prof['NAME'];
    }
}
unset($bonus);
$proffield=array(
    'ID'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_ID'),
    'NAME'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_NAME'),
    'SITE'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_SITE'),
    'BONUS'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_BONUS'),
    'SCOREIN'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_SCOREIN'),
    'ACTIVE_TO'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_ACTIVE_TO'),
    'ACTIVE_FROM'=>\Bitrix\Main\Localization\Loc::getMessage('VBCH_SHOWBONUSFIELD_ACTIVE_FROM'),

);

$arComponentParameters = array(
	"GROUPS" => array(

	),
	"PARAMETERS" => array(
		"PROFILE"=>Array(
			"PARENT" => "BASE",
			"NAME"   => GetMessage("VBCH_SHOWBONUSFIELD_PROFILE"),
            "TYPE" => "LIST",
            "VALUES" => $arProf,
            "DEFAULT" => '',
    	),
		"PROFILEFIELDS"=>Array(
			"PARENT" => "BASE",
			"NAME"   => GetMessage("VBCH_SHOWBONUSFIELD_PROFILEFIELDS"),
			"TYPE"   => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $proffield,
            "DEFAULT" => '',
		),
	)
);
?>