<?
use Bitrix\Main\Localization\Loc;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
Loc::loadMessages(__FILE__);


//echo "<pre>"; var_dump($arResult); echo "</pre>";


if (!empty($arResult)) { ?>

	<div class="bonus_history">

		<h2>История операций</h2>

		<table>
			<thead>
				<tr>
					<td>Дата операции</td>
					<td>Сумма</td>
					<td>Описание</td>
				</tr>
			</thead>
			<tbody>
				<?if($arParams['SHOW_BONUS_ACCOUNT']=="Y" && !empty($arResult["DATA"])){?>
					<?foreach($arResult["DATA"] as $arItem):?>
					<tr>
						<td><?=$arItem["DATE"]?></td>
						<td><?=substr($arItem["SUMMA"], 0, -5)?>&nbsp;Р</td>
						<td><?=$arItem["DESCRIPTION"]?></td>
					</tr>
					<?endforeach;?>
					<?}?>
				</tbody>
			</table>

		</div>

<?}?>


