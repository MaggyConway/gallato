<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?><div class="wrapper">
	<div class="checkout_page">
		 <?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax",
	"order",
	Array(
		"ACTION_VARIABLE" => "soa-action",
		"ADDITIONAL_PICT_PROP_13" => "-",
		"ADDITIONAL_PICT_PROP_8" => "-",
		"ALLOW_APPEND_ORDER" => "Y",
		"ALLOW_AUTO_REGISTER" => "N",
		"ALLOW_NEW_PROFILE" => "Y",
		"ALLOW_USER_PROFILES" => "Y",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"BASKET_POSITION" => "before",
		"COMPATIBLE_MODE" => "Y",
		"DELIVERIES_PER_PAGE" => "9",
		"DELIVERY_FADE_EXTRA_SERVICES" => "N",
		"DELIVERY_NO_AJAX" => "H",
		"DELIVERY_NO_SESSION" => "Y",
		"DELIVERY_TO_PAYSYSTEM" => "d2p",
		"DISABLE_BASKET_REDIRECT" => "N",
		"EMPTY_BASKET_HINT_PATH" => "/catalog/",
		"HIDE_ORDER_DESCRIPTION" => "Y",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"PATH_TO_AUTH" => "/login/",
		"PATH_TO_BASKET" => "/cart/",
		"PATH_TO_PAYMENT" => "payment.php",
		"PATH_TO_PERSONAL" => "/personal/",
		"PAY_FROM_ACCOUNT" => "Y",
		"PAY_SYSTEMS_PER_PAGE" => "9",
		"PICKUPS_PER_PAGE" => "5",
		"PICKUP_MAP_TYPE" => "yandex",
		"PRODUCT_COLUMNS_HIDDEN" => array(),
		"PRODUCT_COLUMNS_VISIBLE" => array("PREVIEW_PICTURE","PROPS","PRICE_FORMATED","PROPERTY_OLD_PRICE"),
		"PROPS_FADE_LIST_1" => array("1","2","3","4","5"),
		"PROPS_FADE_LIST_2" => array("6","7","8","9","10","11"),
		"SEND_NEW_USER_NOTIFY" => "N",
		"SERVICES_IMAGES_SCALING" => "adaptive",
		"SET_TITLE" => "N",
		"SHOW_BASKET_HEADERS" => "Y",
		"SHOW_COUPONS" => "N",
		"SHOW_COUPONS_BASKET" => "Y",
		"SHOW_COUPONS_DELIVERY" => "Y",
		"SHOW_COUPONS_PAY_SYSTEM" => "Y",
		"SHOW_DELIVERY_INFO_NAME" => "Y",
		"SHOW_DELIVERY_LIST_NAMES" => "Y",
		"SHOW_DELIVERY_PARENT_NAMES" => "Y",
		"SHOW_MAP_IN_PROPS" => "N",
		"SHOW_NEAREST_PICKUP" => "N",
		"SHOW_NOT_CALCULATED_DELIVERIES" => "L",
		"SHOW_ORDER_BUTTON" => "final_step",
		"SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
		"SHOW_PICKUP_MAP" => "Y",
		"SHOW_STORES_IMAGES" => "Y",
		"SHOW_TOTAL_ORDER_BUTTON" => "N",
		"SHOW_VAT_PRICE" => "N",
		"SKIP_USELESS_BLOCK" => "Y",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"TEMPLATE_LOCATION" => "popup",
		"TEMPLATE_THEME" => "site",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
		"USE_CUSTOM_ERROR_MESSAGES" => "N",
		"USE_CUSTOM_MAIN_MESSAGES" => "N",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_PHONE_NORMALIZATION" => "N",
		"USE_PRELOAD" => "Y",
		"USE_PREPAYMENT" => "N",
		"USE_YM_GOALS" => "N"
	)
);?>
	</div>
</div>
<?global $USER;
	$rsUsers = CUser::GetByID($USER->GetID());
	$arUser = $rsUsers->Fetch();
	$inn = $arUser['UF_INN'];
	$company = $arUser['WORK_COMPANY'];

	//var_dump($arUser['UF_INN']);
?>
<script>
	BX.addCustomEvent('onAjaxSuccess', function () {
		jQuery('#soa-property-11').val("<?=$inn?>");
		jQuery('#soa-property-12').val("<?=$company?>");
	});
	jQuery('#soa-property-11').val("<?=$inn?>");
	jQuery('#soa-property-12').val("<?=$company?>");
</script><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>