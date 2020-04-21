<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);
?>
<h3><?=GetMessage("VBCHBB_DESC_TITLE")?></h3>
<table style="text-align:center;width:100%">
	<thead>
		<tr>
			<td><?=GetMessage("VBCHBB_DESC_ACTIVE")?></td>
			<td><?=GetMessage("VBCHBB_DESC_CREATED")?></td>
			<td><?=GetMessage("VBCHBB_DESC_ACTIVE_FROM")?></td>
			<td><?=GetMessage("VBCHBB_DESC_ACTIVE_TO")?></td>
			<td><?=GetMessage("VBCHBB_DESC_SUMM")?></td>
			<td><?=GetMessage("VBCHBB_DESC_DESCR")?></td>
		</tr>
	</thead>
	<tbody>
		<?if($arParams['SHOW_INNER_ACCOUNT']=="Y" && !empty($arResult["ACCOUNTUSER"])){?>
			<tr><td colspan="7"><b><?=Loc::getMessage('VBCHBB_COMP_INNER')?></b></td></tr>
			<?foreach($arResult["ACCOUNTUSER"] as $arItem):?>
				<tr>
					<td><?=$arItem["ACTIVE"]?></td>
					<td><?=$arItem["DATE"]?></td>
					<td><?=$arItem["ACTIVE_FROM"]?></td>
					<td><?=$arItem["ACTIVE_TO"]?></td>
					<td><?=$arItem["SUMMA"]?></td>
					<td><?=$arItem["DESCRIPTION"]?></td>
				</tr>
			<?endforeach;?>
		<?}?>
		<?if($arParams['SHOW_BONUS_ACCOUNT']=="Y" && !empty($arResult["DATA"])){?>
			<tr><td colspan="7"><b><?=Loc::getMessage('VBCHBB_COMP_BONUS')?></b></td></tr>
			<?foreach($arResult["DATA"] as $arItem):?>
				<tr>
					<td><?=$arItem["ACTIVE"]?></td>
					<td><?=$arItem["DATE"]?></td>
					<td><?=$arItem["ACTIVE_FROM"]?></td>
					<td><?=$arItem["ACTIVE_TO"]?></td>
					<td><?=$arItem["SUMMA"]?></td>
					<td><?=$arItem["DESCRIPTION"]?></td>
				</tr>
			<?endforeach;?>
		<?}?>
	</tbody>
</table>

