<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бонусная программа");
?>

<!-- <div class="empty_content">
	страница в разработке
</div>
 -->
<div class="wrapper"><section class="bonus_page">

<?$APPLICATION->IncludeComponent(
	"custom:vbcherepanov.bonusaccount",
	"main",
	Array(
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "N",
		"SHOW_BONUS_ACCOUNT" => "Y",
		"SHOW_INNER_ACCOUNT" => "N"
	)
);?>

<?$APPLICATION->IncludeComponent(
	"custom:vbcherepanov.bonusdescription",
	"main",
	Array(
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "N",
		"NOTACTIVE" => "N",
		"ORDER" => "BONUS",
		"ORDERDEC" => "ASC",
		"SHOW_BONUS_ACCOUNT" => "Y",
		"SHOW_INNER_ACCOUNT" => "N"
	)
);?>

</section></div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>