<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if($arResult['COUPON']){?>
    <b><?=Loc::getMessage('VBCHBB_BONUS_COUPON_LIST')?></b>
    <ul>
    <?foreach($arResult['COUPON'] as $coupon){?>
        <li><?=$coupon['COUPON']?> - <?=$coupon['BONUS']?></li>
    <?}?>
    </ul>
<?}?>
<form id="activate_bonus_coupon">
    <?=Loc::getMessage('VBCHBB_BONUS_COUPON_ACTIVATE')?>
    <input type="text" id="COUPON" value=""/>
    <input type="button" name="ACTIVATE" id="ACTIVATE" value="<?=Loc::getMessage('VBCHBB_BONUS_COUPON_ACTIVATE_BTN')?>"/>
    <div id="result"></div>
</form>

<script>
    BX.ready(function() {

        BX.bind(BX('ACTIVATE'), 'click', function() {

            var params =<?=\Bitrix\Main\Web\Json::encode(['signedParameters' => $this->getComponent()->getSignedParameters()])?>;
            BX.ajax.runComponentAction("vbcherepanov:vbcherepanov.bonuscoupon", "getActivateCoupon",
                {
                    mode: 'class',
                    signedParameters: params.signedParameters,
                    data: {post: {coupon: BX('COUPON').value,user_id:<?=$USER->GetID()?>}}
                }
            ).then(function (response) {
                var text;
                if(response.data.SUCCES==true)
                    text = +response.data.BONUS_OLD + ' >>> '+ response.data.BONUS_NOW;
                else
                    text= response.data.ERROR;
                BX('result').innerHTML=text;
            });
        });


    });
</script>
