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


<div class="wrapper">
<div class="promo_page">
	<?foreach($arResult["ITEMS"] as $arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
		<? $photo = CFile::GetPath($arItem["PROPERTIES"]["PHOTO"]["VALUE"]);
			//echo "<pre>"; var_dump($arItem); echo "</pre>"; ?>
		<div class="item" style="background: url(<?=$photo?>) no-repeat center center;">
			<span class="promo_logo"></span>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="inner-wrap"></a>
		</div>
	<?endforeach;?>
</div>
</div>