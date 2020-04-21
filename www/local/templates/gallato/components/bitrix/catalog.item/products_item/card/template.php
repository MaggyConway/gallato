<!-- КАРТОЧКА ТОВАРА В СПИСКЕ ТОВАРОВ -->

<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var CatalogSectionComponent $component
 */
?>
<?
	// echo '<pre>'; var_dump($price); echo '</pre>' ;
?>

<div class="product_item">
	<div class="item__top_block">
		<a href="<?=$arResult["ITEM"]["DETAIL_PAGE_URL"]?>" class="photo" style="background: #fff url(<?=$arResult["ITEM"]["PREVIEW_PICTURE"]["SRC"]?>) no-repeat center center;"></a>

<? if ($USER->IsAuthorized()) { 

	global $fav_products;
	$in_wish_list = false;

	if (in_array($arResult["ITEM"]["ID"], $fav_products)) {
	    $in_wish_list = true;
	}

?>

	<div class="item__like <?=($in_wish_list) ? "active" : ""  ?>" data-product-id="<?=$arResult["ITEM"]["ID"]?>"></div>

<?}?>




		<a href="<?=$arResult["ITEM"]["DETAIL_PAGE_URL"]?>" class="item__btn">открыть</a>

		<? if ($arResult["ITEM"]["PROPERTIES"]["SALE"]["VALUE"] == "Y") { ?>

			<div class="item_sale_sticker">SALE</div>

		<? } ?>
	</div>

	<div class="prices">
		
		<a href="<?=$arResult["ITEM"]["DETAIL_PAGE_URL"]?>" class="item__price"><?=$price["BASE_PRICE"]?>&nbsp;Р</a>

<? if(!empty($arResult["ITEM"]["PROPERTIES"]["OLD_PRICE"]["VALUE"])) {?>
		<div class="old_price">
			<p><?=$arResult["ITEM"]["PROPERTIES"]["OLD_PRICE"]["VALUE"]?></p><span>Р</span>
		</div>
<?}?>


	</div>
	
	<a href="<?=$arResult["ITEM"]["DETAIL_PAGE_URL"]?>" class="item__label"><?=$arResult["ITEM"]["NAME"]?></a>
</div>