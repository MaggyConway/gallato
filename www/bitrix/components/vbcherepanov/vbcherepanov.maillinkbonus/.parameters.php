<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();



$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"USER_ID" => Array(
			"NAME" => GetMessage("SBBS_USER_ID"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => Array(
				"={#USER_ID#}" => "={#USER_ID#}",
				"={#ORDER_USER_ID#}" => "={#ORDER_USER_ID#}",
				"={#ID#}" => "={#ID#}",
			),
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => Array(
				"{#USER_ID#}" => "{#USER_ID#}"
			),
			#"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"BONUS" => Array(
			"NAME" => GetMessage("BONUS_INPUT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "5",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	)
);
?>