<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
if(array_key_exists("VK",$arResult['BUTTON'])){?>
    <script type="text/javascript" src="http://userapi.com/js/api/openapi.js?49"></script>
    <script type="text/javascript">VK.init({ apiId: '<?=$arResult['SOCIAL']['VK']['APPID']?>', onlyWidgets: true });</script>
<?}?>
<?if(is_array($arResult['BUTTON']) && sizeof($arResult['BUTTON'])>0){?>
<ul class="social_button">
    <? foreach($arResult['BUTTON'] as $ss=>$btn){ ?>
        <?if($ss=="TW"){?>
            <li class="twitter_wrap">
                <?=$btn?>
            </li>
        <?}else{?>
            <li class="<?=$ss?>cls">
                <?=$btn?>
            </li>
        <?}?>
    <?}?>
</ul>
<?}
$arJSParams=array(
    'DESCRIPTION'=>$arResult['DESCRIPTION'],
    'SOCIAL'=>$arResult['SOCIAL'],
    'SERVERNAME'=>$arResult['SERVER_NAME'],
    'SITENAME'=>$arResult['SITE_NAME'],
    'URL'=>$arResult['URL'],
    'ADDBONUS'=>$arResult['ADDBONUS'],
);
?>
<script type="text/javascript">
    <?if(array_key_exists("FB",$arResult['SOCIAL'])){?>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?=$arResult['SOCIAL']['FB']['APPID']?>',
            channelUrl : '',
            status     : true,
            xfbml      : true
        });
    };
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    <?}
    if(array_key_exists("TW",$arResult['SOCIAL'])){?>
    $.getScript("https://platform.twitter.com/widgets.js", function(){
        $(".twitter_wrap iframe").css('opacity','0.0');
        twttr.events.bind('tweet', function(intent_event) {
            <?echo $arResult['JSCLASS'];?>.inittwitter(intent_event);
        });
    });
    <?}?>
</script>
<script type="text/javascript">
    var <? echo $arResult['JSCLASS']; ?> = new VBCHBBLIKE(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
    BX.message({
        ADD_BONUS_MESSAGE: '<? echo Loc::getMessage('VBCH_BONUS_LIKE_ADD'); ?>',
        ADD_BONUS_ERROR : '<? echo Loc::getMessage('VBCH_BONUS_LIKE_ADD_ERROR'); ?>',
        ADD_PUBLIC:'<? echo Loc::getMessage('VBCH_BONUS_LIKE_ADD_PUBLIC'); ?>',
        ERROR_PUBLIC:'<? echo Loc::getMessage('VBCH_BONUS_LIKE_ERROR_PUBLIC'); ?>',
        SITE_ID: '<? echo SITE_ID; ?>'
    });
</script>
