<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
$stat=array(
  'Принят'=>'created',
  'Доставлен'=>'delivered',
  'Оплачен'=>'paid',
);
?>
<table class="colored bw head-color <?//rgba(235, 236, 236, 0.5)?>">
    <thead>
    <tr>
        <th>Заказ</th>
        <th>Сумма заказа</th>
        <th>Комиссия</th>
        <th>%</th>
        <th>Промокод</th>
        <th>Статус</th>
    </tr>
    </thead>
    <tbody>
<?if(!empty($arResult['TRANSACTION'])){
    foreach($arResult['TRANSACTION'] as $trans){
    ?>
<tr>
    <td>
        <div class="number">№<?=$trans['ORDER_ID']?></div>
        <span class="t_date"><?=$trans['ORDER_DATE'][0]?></span>
        <span class="t_clock"><?=$trans['ORDER_DATE'][1]?></span></td>
    <td>
        <div class="price"><?=$trans['PRICE']?> Р</div></td>
    <td>
        <div class="price"><?=$trans['BONUS']?> Р</div></td>
    <td><?=$trans['PERCENT']?></td>
    <td>
        <span class="price"><?=$trans['COUPON']?></span></td>
    <td>
        <div class="labels">

            <!--<span class="created">создан</span>-->
            <span class="<?=$stat[$trans['STATUS']]?>"><?=$trans['STATUS']?></span>
            <!--<span class="delivered">доставляется</span>-->
        </div></td></tr>
<?}}?>
    </tbody>
</table>
