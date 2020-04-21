<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

if(!\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus"))
	return;
$arComponentParameters = array(
	"PARAMETERS" => Array(
		"ELEMENT" => Array(
			"NAME" => Loc::getMessage("VBCH_BONUSELEMENT_EL"),
			"TYPE"=>"STRING",
			"MULTIPLE"=>"N",
			"VALUES" => "",
		),
		"OFFERS_ID" => Array(
			"NAME" => Loc::getMessage("VBCH_BONUSELEMENT_OFFERS_ID"),
			"TYPE"=>"STRING",
			"MULTIPLE"=>"N",
			"VALUES" => "",
			"DEFAULT"=>"asasda",
		),
		"OFFERS_AR" => Array(
			"NAME" => Loc::getMessage("VBCH_BONUSELEMENT_OFFERS_AR"),
			"TYPE"=>"STRING",
			"MULTIPLE"=>"N",
			"VALUES" => "",
		),
        'ONLY_NUM'=>Array(
            "NAME" => Loc::getMessage("VBCH_BONUSELEMENT_ONLY_NUM"),
            "TYPE"=>"CHECKBOX",
            "DEFAULT" => "N",
        ),
		"CACHE_TIME" => Array("DEFAULT"=>"3600"),
	)
);