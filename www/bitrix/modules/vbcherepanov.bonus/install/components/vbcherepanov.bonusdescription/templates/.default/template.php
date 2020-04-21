<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<tr>
				<td><?=$arItem["ACTIVE"]?></td>
				<td><?=$arItem["CREATED"]?></td>
				<td><?=$arItem["ACTIVE_FROM"]?></td>
				<td><?=$arItem["ACTIVE_TO"]?></td>
				<td><?=$arItem["SUMM"]?></td>
				<td><?=$arItem["DESCRIPTION"]?></td>
			</tr>
		<?endforeach;?>
	</tbody>
</table>
