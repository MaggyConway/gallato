<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if(!$arResult['ERROR']){?>
	<?=Loc::getMessage('VBCHBB_MY_REFCODE').$arResult['REFERER']?><br/>
	<?=Loc::getMessage('VBCHBB_REFERAL_EXAMPLES').$arResult['EXAMPLES']?>
<?}?>


