<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("vbcherepanov.bonus"))
    return;
$arComponentParameters = array(
	"GROUPS" => array(

	),
	"PARAMETERS" => array(
		"NAME"=>Array(
			"PARENT" => "BASE",
			"NAME"   => GetMessage("VBCH_BONUSLIKE_NAME"),
			"TYPE"   => "STRING",
			"DEFAULT" => '',
		),
		"DESCRIPTION"=>Array(
			"PARENT" => "BASE",
			"NAME"   => GetMessage("VBCH_BONUSLIKE_DESCR"),
			"TYPE"   => "STRING",
			"DEFAULT" => '',
		),
	)
);
?>