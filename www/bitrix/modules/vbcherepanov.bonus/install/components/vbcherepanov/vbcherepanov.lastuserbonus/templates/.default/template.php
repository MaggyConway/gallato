<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
?>
<p><b><?=str_replace("#DATE#",$arResult['DATE'],Loc::getMessage("VBCHBB_MY_ACCOUNT"))?></b></p>
<ul>
	<?if($arParams['SHOW_INNER_ACCOUNT']=='Y'){
		if($arResult['INNER']['SUMMA']){?>
			<li><?=Loc::GetMessage("VBCHBONUS_ACCOUNT_INNER").$arResult['INNER']['SUMMA']?></li>
		<?}else{
			echo Loc::getMessage("VBCHBB_NO_ACCOUNT");
		}
	}?>
	<?if($arParams['SHOW_BONUS_ACCOUNT']=='Y') {
		if ($arResult['BONUS']['SUMMA']) {
			?>
			<li><?= Loc::GetMessage("VBCHBONUS_ACCROUNT_BONUS") . $arResult['BONUS']['SUMMA'] ?></li>
		<?
		} else {
			echo Loc::getMessage("VBCHBB_NO_ACCOUNT");
		}
	}
	?>
</ul>

