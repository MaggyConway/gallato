<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
if($arResult['BONUS']){?>
    <b><?=Loc::getMessage("VBCH_BONUS_FRO_ORDER_TITLE",array("#BONUS#"=>$arResult['BONUS']))?></b>
    <?if($arParams['TYPE']=="CART"){?>
        <i style="font-size:9px"><?=Loc::getMessage("VBCH_BONUS_FRO_ORDER_ONLY_CART")?></i>
    <?}?>
<?}?>
<?
 $arJSParams=array(
	'siteid'=>SITE_ID,
	'TYPE'=>$arParams['TYPE'],
 );
?>
<script>
	var bonusCartUp= new ITRElementBonus(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
</script>