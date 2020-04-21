<!-- кол-во товаров на сумму * руб. -->
<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */
$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
?>


<div class="basket_area">

	<? if (!$compositeStub) {
			if ($arParams['SHOW_NUM_PRODUCTS'] == 'Y' && ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] == 'Y')) {
				echo $arResult['NUM_PRODUCTS'].' '.$arResult['PRODUCT(S)'];

				if ($arParams['SHOW_TOTAL_PRICE'] == 'Y') {
					?>
					<br <? if ($arParams['POSITION_FIXED'] == 'Y'): ?>class="hidden-xs"<? endif; ?>/>
					<span>
						<?=GetMessage('TSB1_TOTAL_PRICE')?> <strong><?=$arResult['TOTAL_PRICE']?></strong>
					</span>
					<?
				}
			}
		} ?>
</div>