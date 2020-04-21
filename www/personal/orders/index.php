<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Текущие заказы");
?>

<div class="wrapper">

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.list",
	"",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ALLOW_INNER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "A",
		"DEFAULT_SORT" => "STATUS",
		"DISALLOW_CANCEL" => "N",
		"HISTORIC_STATUSES" => array("F"),
		"ID" => $ID,
		"NAV_TEMPLATE" => "",
		"ONLY_INNER_FULL" => "Y",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_BASKET" => "/cart/",
		"PATH_TO_CANCEL" => "",
		"PATH_TO_CATALOG" => "/catalog/",
		"PATH_TO_COPY" => "",
		"PATH_TO_DETAIL" => "",
		"PATH_TO_PAYMENT" => "payment.php",
		"REFRESH_PRICES" => "N",
		"RESTRICT_CHANGE_PAYSYSTEM" => array("0"),
		"SAVE_IN_SESSION" => "Y",
		"SET_TITLE" => "N",
		"STATUS_COLOR_F" => "green",
		"STATUS_COLOR_N" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red"
	)
);?>

</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>