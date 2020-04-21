<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<ul class="footer_menu">

<?
$previousLevel = 0;
foreach($arResult as $arItem):?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>

		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li class="menu__item">	

				<? //echo '<pre>'; var_dump($arItem["TEXT"]); echo '<pre>'; ?>

				<a href="<?=$arItem["LINK"]?>" <?if($arItem["TEXT"] == "Ispring" || $arItem["TEXT"] == "Omobus" || $arItem["TEXT"] == "Sislink" ):?>  target="_blank" <?endif?> class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a>

				<ul>
		<?else:?>
			<li<?if ($arItem["SELECTED"]):?> class="menu__item item-selected"<?endif?>>
			<a href="<?=$arItem["LINK"]?>" <?if($arItem["TEXT"] == "Ispring" || $arItem["TEXT"] == "Omobus" || $arItem["TEXT"] == "Sislink" ):?>  target="_blank" <?endif?> class="parent"><?=$arItem["TEXT"]?></a>
				<ul>
		<?endif?>

	<?else:?>

		<?if ($arItem["PERMISSION"] > "D"):?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="menu__item">
					<a href="<?=$arItem["LINK"]?>" <?if($arItem["TEXT"] == "Ispring" || $arItem["TEXT"] == "Omobus" || $arItem["TEXT"] == "Sislink" ):?>  target="_blank" <?endif?> class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li<?if ($arItem["SELECTED"]):?> class="menu__item item-selected"<?endif?>>
				<a href="<?=$arItem["LINK"]?>" <?if($arItem["TEXT"] == "Ispring" || $arItem["TEXT"] == "Omobus" || $arItem["TEXT"] == "Sislink" ):?>  target="_blank" <?endif?> ><?=$arItem["TEXT"]?></a></li>
			<?endif?>

		<?else:?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="menu__item">
					<a href="<?=$arItem["LINK"]?>" <?if($arItem["TEXT"] == "Ispring" || $arItem["TEXT"] == "Omobus" || $arItem["TEXT"] == "Sislink" ):?>  target="_blank" <?endif?> class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li class="menu__item">
					<a href="<?=$arItem["LINK"]?>" <?if($arItem["TEXT"] == "Ispring" || $arItem["TEXT"] == "Omobus" || $arItem["TEXT"] == "Sislink" ):?>  target="_blank" <?endif?> class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
			<?endif?>

		<?endif?>

	<?endif?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>
</ul>
<?endif?>