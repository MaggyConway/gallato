<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if($arResult['AFFILIATE']){?>
	<b>Промокод для друзей: <?=$arResult['AFFILIATE']['PROMOCODE']?></b>
<?}?>


