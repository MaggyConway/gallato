<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
?>
<h3><?=Loc::getMessage("VBBB_HISTORY_FIRST_TITLE")?></h3>
<ul>
<?
foreach($arResult['ITEMS'] as $prof){?>
<li><b><?=$prof['NAME']?></b>&nbsp;-&nbsp <?=$prof['BONUS']?>
<br/>
<?=$prof['SETTINGS']['DESCRIPTION']?>
</li>

<?}?>
</ul>


