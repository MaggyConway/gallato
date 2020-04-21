<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);


$this->setFrameMode(true);
if(!$arResult['ERROR']){
	$arParams["SHOW_INPUT"]='Y';
$INPUT_ID=str_replace(" ","_",$arResult['UNICODE']);
$CONTAINER_ID="CONT_".$INPUT_ID;
if($arParams["SHOW_INPUT"] !== "N"):?>
	<div>
	<label>
		<input id="<?echo $INPUT_ID?>" type="text" name="<?=$arResult['FIELDNAME']?>" value="<?=$arResult['REF_VALUE'] ? $arResult['REF_VALUE'] : ""?>" size="40" maxlength="50" autocomplete="off" <? echo ($arResult['REF_ID']!='' ? 'disabled':'');?>/>
		<div id="<?echo $CONTAINER_ID?>" style="color:red">
			<?
			if($arResult['REF_NAME']){?>
				<span><?echo $arResult['REF_NAME']?></span><br/>
			<?}?>
			<?if($arResult['REF_ID']){?>
				<input type="hidden" name="REFERER" value="<?=$arResult['REF_ID']?>">
			<?}else{?>
                <input type="hidden" name="REFERER" value="212024">
            <?}?>
		</div>
		</label>
	</div>
<?endif?>
<script>
	BX.ready(function(){
		new JVBCHREFREG({
			'AJAX_PAGE' : '<?echo CUtil::JSEscape($arResult['AJAX_PATH'])?>',
			'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
			'INPUT_ID': '<?echo $INPUT_ID?>',
			'MIN_QUERY_LEN': 2,
			'SITE':'<?=$arResult['SITE_ID']?>'
		});
	});
</script>
<?}?>