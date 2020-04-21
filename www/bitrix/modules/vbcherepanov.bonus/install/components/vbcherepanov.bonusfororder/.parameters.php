<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
if(!\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus"))
	return;
$type=array(
	"CART"=>Loc::getMessage("VBCH_BONUSFORORDER_CART"),
	"ORDER"=>Loc::getMessage("VBCH_BONUSFORORDER_ORDER"),
);
$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"TYPE" => Array(
			"NAME" => Loc::getMessage("VBCH_BONUSFORORDER_TYPE_OUT"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"N",
			"VALUES" => $type,
		),
		"RESULT" => Array(
			"NAME" => Loc::getMessage("VBCH_BONUSFORORDER_OFFERS_ARRAY"),
			"TYPE"=>"STRING",
			"MULTIPLE"=>"N",
			"VALUES" => "",
		),
		"CACHE_TIME" => Array("DEFAULT"=>"3600"),
	)
);
?>