<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if($arResult['STATUS']){?>
    <h2>Бонусная карта <?=$arResult['STATUS'].'&nbsp;'.$arResult['BONUS'] ?></h2>
    <div class="black"><a href="#">Продлить карту</a><br/>
    Сделайте <?=$arResult['NEXT_LEVEL_ORDER']?> заказа до <?=$arResult['NEXT_DATE']?> для продления действия бонусной карты.<br/>Карта будет продлена автоматически
    </div>
    <div class="black"><a href="#">Хочу больше бонусов!</a><br/>
    <?=$arResult['NEXT_STATUS'].'&nbsp;'.$arResult['NEXT_BONUS'].'&nbsp( осталось заказов:'.$arResult['NEXT_LEVEL_ORDER'].' )'?>
    </div>

<?}