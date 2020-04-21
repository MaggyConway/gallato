<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if($arResult['RESULT']){?>
    <div class="bonus-date-diactive">Бонусы действительны до: <?=$arResult['RESULT']?> <a href="#" class="faq"></a></div>
<?}