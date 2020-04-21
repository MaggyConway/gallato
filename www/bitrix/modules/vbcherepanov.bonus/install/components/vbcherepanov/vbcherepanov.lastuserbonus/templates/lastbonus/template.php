<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
?>
<?if($arResult['RESULT']){?>

<div class="gray">Начислено за последнюю операцию: <b><?=$arResult['RESULT']?></b></div>
<?}?>

