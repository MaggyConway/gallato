<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Политика конфиденциальности");
?>

<div class="wrapper">
<? // включаемая область для раздела
	$APPLICATION->IncludeFile(
	    SITE_DIR . "/include/policy_txt.php",
	    Array(),
	    Array(
	        "MODE" => "html")
	);
?>
</div>




<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>