<!-- список товаров в корзине -->
<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$this->IncludeLangFile('template.php');

$cartId = $arParams['cartId'];

require(realpath(dirname(__FILE__)).'/top_template.php');

if ($arParams["SHOW_PRODUCTS"] == "Y" && ($arResult['NUM_PRODUCTS'] > 0 || !empty($arResult['CATEGORIES']['DELAY'])))
{
?>

<div data-role="basket-item-list" class="bx-basket-item-list">

<div class="title">Товары в заказе</div>

	<table id="<?=$cartId?>products">
		<?foreach ($arResult["CATEGORIES"] as $category => $items):
			if (empty($items))
				continue;
			?>

			<?foreach ($items as $v):?>

				<tr class="item">
					<td>
						<a class="img" href="<?=$v["DETAIL_PAGE_URL"]?>" style="background: url(<?=$v["PICTURE_SRC"]?>) no-repeat center center;">

							<!-- <div class="bx-basket-item-list-item-remove" onclick="<?//=$cartId?>.removeItemFromCart(<?//=$v['ID']?>)" title="<?//=GetMessage("TSB1_DELETE")?>">
							</div> -->
						</a>
					
						<a class="name" href="<?=$v["DETAIL_PAGE_URL"]?>">
							<?=$v["NAME"]?>
						</a>
					</td>
					<td>
						<div class="quantity">
							<?=$v["QUANTITY"]?>шт.
						</div>
					</td>
					<td>
						<div class="price">
							<strong><?=$v["PRICE_FMT"]?></strong>
						</div>
					</td>
				</tr>
			<?endforeach?>
		<?endforeach?>
	</table>

	<a href="/cart/" class="back_to_cart">Вернуться в корзину</a>
</div>

	<script>
		BX.ready(function(){
			<?=$cartId?>.fixCart();
		});
	</script>
<?
}