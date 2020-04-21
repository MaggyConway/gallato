<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(false);
global $APPLICATION;
//$APPLICATION->set_cookie("PriceSort", 'asc', time()+60*60*24*30*12*2, "/");
$arResult["SORTING_ARRAY"] = array(6,12,24,48);
$GLOBALS['sectionTemplate'] = ".default";
if($_COOKIE['BIOKAMIN_SM_SectionView'] == 'list'):
    $GLOBALS['sectionTemplate'] = "list";
else:
    $GLOBALS['sectionTemplate'] = ".default";
endif;

$GLOBALS['sortAlphabet'] = "asc";
if($_COOKIE['BIOKAMIN_SM_AlphabetSort'] == 'desc' || $_COOKIE['BIOKAMIN_SM_PriceSort'] == 'desc'):
    $GLOBALS['sortAlphabet'] = "desc";
else:
    $GLOBALS['sortAlphabet'] = "asc";
endif;
$GLOBALS['sortPrice'] = "asc";
if($_COOKIE['BIOKAMIN_SM_AlphabetSort'] == 'desc' || $_COOKIE['BIOKAMIN_SM_PriceSort'] == 'desc'):
    $GLOBALS['sortAlphabet'] = "desc";
else:
    $GLOBALS['sortAlphabet'] = "asc";
endif;

//$GLOBALS['sortName'] = "NAME";
$GLOBALS['sortName'] = "catalog_PRICE_2";
if($_COOKIE['BIOKAMIN_SM_SortName'] == 'alph'):
    $GLOBALS['sortName'] = "NAME";
else:
    $GLOBALS['sortName'] = "catalog_PRICE_2";
endif;

$GLOBALS['CatalogElementCount'] = 24;
if(isset($_COOKIE['BIOKAMIN_SM_CatalogElementCount'])):
    $GLOBALS['CatalogElementCount'] = $_COOKIE['BIOKAMIN_SM_CatalogElementCount'];
else:
    $GLOBALS['CatalogElementCount'] = 24;
endif;
$this->IncludeComponentTemplate();
?>