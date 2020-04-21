<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);?>
<div class='element_bonus'>
<?if($arResult['BONUS']){?>
<b><?=Loc::getMessage("VBCH_BONUS_ELEMENT_BONUSNAME",array("#BONUS#"=>$arResult['PREFIX'].$arResult['BONUS']))?></b>
<?}else{?>
<b><?=Loc::getMessage("VBCH_BONUS_ELEMENT_NONE")?></b>
<?}?>
</div>
<?
 $arJSParams=array(
		 'productID'=>$arResult['DATA']['ID'],
		 'IBLOCKID'=>$arResult['DATA']['IBLOCK_ID'],
		 'MIN_PRICE'=>base64_encode(serialize($arResult['BONUS_PRICE'])),
		 'siteid'=>SITE_ID,
	     'TYPE'=>$arParams['ONLY_NUM']
		 );
?>
<script>
	var bonusElemUp= new ITRElementBonus(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
</script>