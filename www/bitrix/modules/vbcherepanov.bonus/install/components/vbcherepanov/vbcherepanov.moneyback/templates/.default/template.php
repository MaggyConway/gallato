<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if(!empty($arResult['DATA'])){?>
    <div><?=$arResult['DATE']?></div>
    <?foreach($arResult['DATA'] as $dat){?>
        <p><b><?=$dat['PROFILE_DATA']['NAME']?></b></p>
        <div class="balans-available">
            <div class="title">Заработано</div>
            <div class="price-balance"><?=$dat['SUMM_FORMAT']?></div>
        </div>
        <div class="balans-available">
            <div class="title">Выплачено</div>
            <div class="price-balance"><?=$dat['OUT_FORMAT']?></div>
        </div>
        <div class="balans-available">
            <div class="title">В процессе оплаты</div>
            <div class="price-balance"><?=$dat['WAIT_FORMAT']?></div>
        </div>
        <div>
            <div>Максимальная сумма для выплаты</div>
            <div><?=$dat['MAXSUM_FORMATED']?></div>
        </div>
    <?}?>

<?}?>

<div class="clear-both"></div>
<?if($arParams['SHOWBUTTON']=="Y"){?>
    <div class="request-pay">
        <?=$arResult['ERROR']?>
        <form method="POST" id="frmmoneyBack" action="">
            <?	echo bitrix_sessid_post(); ?>
            <input type="hidden" name="waitmoney" value="1"/>
            <input type="text" name="COUNTBACK" value="<?=$_SESSION["COUNTBACK"] ?>">
            <span class="fast-buy">
                <a href="javascript:void(0)" onclick="sbmForm();">Запросить выплату</a>
            </span>
            <?if($arParams['SHOWFIELDSPARAMS']=="Y"){?>
                <textarea name="ACCOUNTS"></textarea>

            <?}?>
            <input type="submit">
        </form>
    </div>
<script type="text/javascript">
    function sbmForm(){
        document.getElementById('frmmoneyBack').submit();
    }
</script>
<?}?>
</div>

