<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if(!$arResult['ERROR']){?>
	<?=Loc::getMessage('VBCHBB_MY_REFUSER')?>
	<ul>
	<?foreach($arResult['REFERALS'] as $refusers){?>
		<li>
			[<?=$refusers['EMAIL']?>] <?=$refusers['LAST_NAME'].' '.$refusers['NAME']?>
		</li>
	<?}?>
	</ul>
<?}?>


