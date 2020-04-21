<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?><div class="wrapper">
 <section class="checkout_page">
	<div class="packs">
		<?

			// $arID = array();

			// $arBasketItems = array();

			// $dbBasketItems = CSaleBasket::GetList(
			// 	array(
			// 		"NAME" => "ASC",
			// 		"ID" => "ASC"
			// 	),
			// 	array(
			// 		"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			// 		"LID" => SITE_ID,
			// 		"ORDER_ID" => "NULL"
			// 	),
			// 	false,
			// 	false,
			// 	array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "PRODUCT_PROVIDER_CLASS")
			// );
			// while ($arItems = $dbBasketItems->Fetch())
			// {
			// 	if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"])
			// 	{
			// 		CSaleBasket::UpdatePrice($arItems["ID"],
			// 			$arItems["CALLBACK_FUNC"],
			// 			$arItems["MODULE"],
			// 			$arItems["PRODUCT_ID"],
			// 			$arItems["QUANTITY"],
			// 			"N",
			// 			$arItems["PRODUCT_PROVIDER_CLASS"]
			// 		);
			// 		$arID[] = $arItems["ID"];
			// 	}
			// }
			// if (!empty($arID))
			// {
			// 	$dbBasketItems = CSaleBasket::GetList(
			// 		array(
			// 			"NAME" => "ASC",
			// 			"ID" => "ASC"
			// 		),
			// 		array(
			// 			"ID" => $arID,
			// 			"ORDER_ID" => "NULL"
			// 		),
			// 		false,
			// 		false,
			// 		array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "PRODUCT_PROVIDER_CLASS", "NAME")
			// 	);
			// 	while ($arItems = $dbBasketItems->Fetch())
			// 	{
			// 		$arBasketItems[] = $arItems;
			// 	}
			// }
			// Печатаем массив, содержащий актуальную на текущий момент корзину
			// echo "<pre>";
			// print_r($arBasketItems);
			// echo "</pre>";

			/*?>
			<ul>
				 <? foreach ($arBasketItems as $item) { ?>
				<li>
				<div class="name">
					 <?=$item[NAME]?>
				</div>
				<div class="quantity">
					 <?=$item[QUANTITY]?>шт.
				</div>
				<div class="price">
					 <?=$item[PRICE]?> Р
				</div>
 </li>
				 <? } 
			</ul>*/?>


			<?$APPLICATION->IncludeComponent(
				"bitrix:sale.basket.basket.line",
				"main",
				Array(
					"HIDE_ON_BASKET_PAGES" => "Y",
					"PATH_TO_AUTHORIZE" => SITE_DIR."/login/",
					"PATH_TO_BASKET" => SITE_DIR."/cart/",
					"PATH_TO_ORDER" => "",
					"PATH_TO_PERSONAL" => SITE_DIR."/personal/",
					"PATH_TO_PROFILE" => SITE_DIR."/personal/",
					"PATH_TO_REGISTER" => SITE_DIR."/registr/",
					"POSITION_FIXED" => "N",
					"SHOW_AUTHOR" => "N",
					"SHOW_DELAY" => "N",
					"SHOW_EMPTY_VALUES" => "Y",
					"SHOW_IMAGE" => "Y",
					"SHOW_NOTAVAIL" => "N",
					"SHOW_NUM_PRODUCTS" => "Y",
					"SHOW_PERSONAL_LINK" => "Y",
					"SHOW_PRICE" => "Y",
					"SHOW_PRODUCTS" => "Y",
					"SHOW_REGISTRATION" => "N",
					"SHOW_SUMMARY" => "Y",
					"SHOW_TOTAL_PRICE" => "Y"
				)
			);?>


		</div>
	
	<div class="info">
		<div class="title">
			 Информация к заказу
		</div>
		<form action="/include/order.php" class="order_form">
 			<input type="text" name="adress" placeholder="Адрес доставки" class="field" required>
 			<textarea name="comment" rows="7" placeholder="Комментарии к заказу" class="comment"></textarea>
 			<input type="hidden" name="event" value="ORDER_FORM">
 			<!-- <input type="submit" value="Оформить заказ" class="btn"> -->
 			<a href="/checkout/order-ready.php" class="btn">Оформить заказ</a>
		</form>
	</div>
</section>
</div>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>