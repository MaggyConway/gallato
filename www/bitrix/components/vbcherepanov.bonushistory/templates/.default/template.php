<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h3><?=GetMessage("VBBB_HISTORY_FIRST_TITLE")?></h3>
<ul>
<li><?=GetMessage("VBBB_HISTORY_NOTIF")?>
	<ul>
		<?if($arResult["SENDEMAIL"]) echo '<li>'.GetMessage("VBBB_HISTORY_NOTIFIEMAIL").'</li>';?>
		<?if($arResult["SENDSMS"]) echo '<li>'.GetMessage("VBBB_HISTORY_NOTIFSMS").'</li>';?>
	</ul>
</li>
<li><?=GetMessage("VBBB_HISTORY_BONUSLIVE")?>&nbsp;&nbsp;<?=$arResult["LIVE"]?></li>
<?if($arResult["REGISTR"]){?><li><?=str_replace("#COUNT#",$arResult["REGISTR"],GetMessage("VBBB_HISTORY_BONUSREG"))?></li><?}?>
<?if($arResult["BIRTHDAY"]){?><li><?=str_replace("#COUNT#",$arResult["BIRTHDAY"],GetMessage("VBBB_HISTORY_BIRTHDAY"))?></li><?}?>
<?if($arResult["PAYPART"]){?><li><?=str_replace("#PAYPART#",$arResult["PAYPART"],GetMessage("VBBB_HISTORY_PAYPART"))?></li><?}?>
<?if($arResult["SUBSCRIBE"]){?><li><?=str_replace("#COUNT#",$arResult["SUBSCRIBE"],GetMessage("VBBB_HISTORY_SUBSCRIBE"))?></li><?}?>
<?if($arResult["ORDER"]["DISCOUNT_OFF"]) echo '<li>'.GetMessage("VBBB_HISTORY_DISCOUNTOFF").'</li>';?>
<?if($arResult["ORDER"]["SUMM_TOTAL"]) echo '<li>'.GetMessage("VBBB_HISTORY_SUMMTOTAL").'</li>';?>
<?if($arResult["ORDER"]["BONUS"]) echo '<li>'.GetMessage('VBBB_HISTORY_BONUSORDER').':'.$arResult["ORDER"]["BONUS"].'</li>';?>
<?if($arResult["DELAY"]) echo '<li>'.GetMessage('VBBB_HISTORY_BONUSDELAY').':'.$arResult["DELAY"].'</li>';?>
<?if($arResult["SUMMAS"]) echo '<li>'.GetMessage('VBBB_HISTORY_BONUSSUMMAS').':'.$arResult["SUMMAS"].'</li>';?>
<?if($arResult["POROG"]["ACTIVE"]){?>
<li><?=GetMessage('VBBB_HISTORY_BONUSPOROG')?>
	<ul>
	<?foreach($arResult["POROG"]["TABLE"] as $porog){?>
		<li><?=$porog?></li>
	<?}?>
	</ul>
</li>
<?}?>
<?if(is_array($arResult["USERGROUP"]) && sizeof($arResult["USERGROUP"])>0){?>
	<li><?=GetMessage("VBBB_HISTORY_BONUSUSERGROUP")?>
		<ul>
			<?foreach($arResult["USERGROUP"] as $uspg){?>
				<li><?=$uspg?>
			<?}?>
		</ul>
	</li>
<?}?>
<?if(is_array($arResult["SALEPERSON"]) && sizeof($arResult["SALEPERSON"])>0){?>
	<li><?=GetMessage("VBBB_HISTORY_BONUSSALEPERSON")?>
		<ul>
			<?foreach($arResult["SALEPERSON"] as $uspg){?>
				<li><?=$uspg?>
			<?}?>
		</ul>
	</li>
<?}?>
<?if($arResult["ORDER"]["FIRST"]) echo '<li>'.str_replace("#COUNT#",$arResult["ORDER"]["FIRST"],GetMessage("VBBB_HISTORY_FIRSTORDER")).'</li>';?>
<?if($arResult["REVIEW"]["ACTIVE"]){
	foreach($arResult["REVIEW"]["TABLE_BONUS"] as $rew){?>
	<li><?=GetMessage("VBBB_HISTORY_BONUSOF")." '".$rew["NAME"]."': ".$rew["COUNT"]?> </li>
<?}}?>
<?if($arResult["ORDER"]["ACTIVE"]){?>
<li><?=GetMessage("VBBB_HISTORY_BONUSORDER")?>
	<table border="1" width="100%" style="text-align:center">
		<thead>
		<tr><td><?=GetMessage("VBBB_HISTORY_SUMMFROM")?></td>
		<td><?=GetMessage("VBBB_HISTORY_SUMMTO")?></td>
		<td><?=GetMessage("VBBB_HISTORY_PAY")?></td>
		<td><?=GetMessage("VBBB_HISTORY_DELIVERY")?></td>
		<td><?=GetMessage("VBBB_HISTORY_BONUSORDER")?></td>
		</tr>
		</thead>
		<tbody>
		<?foreach($arResult["ORDER"]["TABLE_BONUS"] as $ordbon){?>
		<tr>
			<td><?=$ordbon["FROM"]?></td>
			<td><?=$ordbon["TO"]?></td>
			<td><?=$ordbon["PAY"]?></td>
			<td><?=$ordbon["DELIVARY"]?></td>
			<td><?=$ordbon["COUNT"]?></td>
		</tr>
		<?}?>
	</tbody>
	</table>
</li>
<?}?>
<?if(is_array($arResult["ORDER"]["OFFER_OFF"]) && sizeof($arResult["ORDER"]["OFFER_OFF"])>0){?>
<li><?=GetMessage("VBBB_HISTORY_OFFEROFF")?>
	<ul>
		<?foreach($arResult["ORDER"]["OFFER_OFF"] as $offer){?>
			<li><?=$offer?></li>
		<?}?>
	</ul>
</li>
<?}?>
<?if(is_array($arResult["SECTION"]) && sizeof($arResult["SECTION"])>0){?>
<li><?=GetMessage("VBBB_HISTORY_SECTION")?>
	<ul>
		<?foreach($arResult["SECTION"] as $offer){?>
			<li><?=$offer?></li>
		<?}?>
	</ul>
</li>
<?}?>
</ul>