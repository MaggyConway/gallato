<!-- СТРАНИЦА ДЕТАЛЬНОЙ СТАТЬИ -->

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var stripcslashes(str)ng $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true); ?>

<? //echo "<pre>"; var_dump($arResult); echo "</pre>"; ?>

<div class="news_detail_page promo_detail_page"><div class="wrapper">

	<a href="/stocks/" class="back">Назад</a>

	<h1><?=$arResult["NAME"]?></h1>

	<? $photo = CFile::GetPath($arResult["PROPERTIES"]["PHOTO"]["VALUE"]);

	if ($arResult["PROPERTIES"]["PHOTO"]["VALUE"]) { ?>

		<div class="photo" style="background: url(<?=$photo?>) no-repeat center center;"></div>

	<? } elseif ($arResult["DETAIL_PICTURE"]["SRC"]) { ?>
		
		<div class="photo" style="background: url(<?=$arResult["DETAIL_PICTURE"]["SRC"]?>) no-repeat center center;"></div>

	<? } elseif ($arResult["PREVIEW_PICTURE"]["SRC"]) { ?>

		<div class="photo" style="background: url(<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>) no-repeat center center;"></div>

	<? } ?>


	<div class="text"><?=$arResult["DETAIL_TEXT"]?></div>


	<!-- другие новости -->

</div></div>