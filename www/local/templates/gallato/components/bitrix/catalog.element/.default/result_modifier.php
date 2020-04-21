<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$comments = array();
$arSelect = Array("PROPERTY_FIO", "PROPERTY_DATE", "PROPERTY_COMMENT", "PROPERTY_ANSWER");
$arFilter = Array("IBLOCK_ID"=>16, "ACTIVE"=>"Y", "PROPERTY_PRODUCT"=>$arResult["ID"]);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);

//var_dump($arResult["ID"]);

while($ob = $res->GetNextElement()) {
	$comments[] = array(

		"FIO" => $ob->fields["PROPERTY_FIO_VALUE"],
		"DATE" => $ob->fields["PROPERTY_DATE_VALUE"],
		"COMMENT" => $ob->fields["PROPERTY_COMMENT_VALUE"]["TEXT"],
		"ANSWER" => $ob->fields["PROPERTY_ANSWER_VALUE"]["TEXT"],
	);
}
$arResult["COMMENTS"] = $comments;
//echo "<pre>"; var_dump($arResult["COMMENTS"]); echo "</pre>";