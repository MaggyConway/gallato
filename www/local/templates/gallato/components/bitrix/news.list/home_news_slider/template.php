<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
//echo "<pre>"; var_dump(); echo "</pre>"; ?>


<div class="home_news_slider">
	<?foreach($arResult["ITEMS"] as $arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
		<? //echo "<pre>"; var_dump($arItem); echo "</pre>"; ?>
		<div class="item">
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
				<div class="photo-wrap"><div class="photo" style="background: url(<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>) no-repeat center center;"></div></div>
				<div class="title"><?=$arItem["NAME"]?></div>
				<div class="date"><?=$arItem["PROPERTIES"]["DATE"]["VALUE"]?></div>
			</a>
		</div>
	<?endforeach;?>
</div>