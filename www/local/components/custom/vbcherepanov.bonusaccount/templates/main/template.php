<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
?>

<h2>Баланс счета</h2>

<?

//echo "<pre>"; var_dump($arResult['BONUS']['SUMMA']); echo "</pre>";

if ($arResult['BONUS']['SUMMA'] !== NULL) { ?>
<div class="bonus_account">
	У вас на счету <span><?=substr($arResult['BONUS']['SUMMA'], 0, -5);?></span>&nbsp;Р
</div>
<?} else {?>
	<div class="bonus_account">
	У вас на счету <span>0</span>&nbsp;Р
</div>
<?}?>