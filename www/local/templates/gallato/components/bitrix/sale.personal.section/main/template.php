<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;


if (strlen($arParams["MAIN_CHAIN_NAME"]) > 0) {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams["MAIN_CHAIN_NAME"]), $arResult['SEF_FOLDER']);
}

$availablePages = array();

if ($arParams['SHOW_ORDER_PAGE'] === 'Y') {
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_ORDERS'],
		"name" => Loc::getMessage("SPS_ORDER_PAGE_NAME"),
	);
}

$availablePages[] = array(
	"path" => '/personal/bonus/',
	"name" => 'Бонусная программа',
);

$availablePages[] = array(
	"path" => '/personal/settings/',
	"name" => 'Настройки',
);

// if ($arParams['SHOW_PRIVATE_PAGE'] === 'Y') {
// 	$availablePages[] = array(
// 		"path" => $arResult['PATH_TO_PRIVATE'],
// 		"name" => Loc::getMessage("SPS_PERSONAL_PAGE_NAME"),
// 		"icon" => '<i class="fa fa-user-secret"></i>'
// 	);
// }

// if ($arParams['SHOW_ORDER_PAGE'] === 'Y') {

// 	$delimeter = ($arParams['SEF_MODE'] === 'Y') ? "?" : "&";
// 	$availablePages[] = array(
// 		"path" => $arResult['PATH_TO_ORDERS'].$delimeter."filter_history=Y",
// 		"name" => Loc::getMessage("SPS_ORDER_PAGE_HISTORY"),
// 		"icon" => '<i class="fa fa-list-alt"></i>'
// 	);
// }

// if ($arParams['SHOW_PROFILE_PAGE'] === 'Y') {
// 	$availablePages[] = array(
// 		"path" => $arResult['PATH_TO_PROFILE'],
// 		"name" => Loc::getMessage("SPS_PROFILE_PAGE_NAME"),
// 		"icon" => '<i class="fa fa-list-ol"></i>'
// 	);
// }

// if ($arParams['SHOW_BASKET_PAGE'] === 'Y') {
// 	$availablePages[] = array(
// 		"path" => $arParams['PATH_TO_BASKET'],
// 		"name" => Loc::getMessage("SPS_BASKET_PAGE_NAME"),
// 		"icon" => '<i class="fa fa-shopping-cart"></i>'
// 	);
// }

// if ($arParams['SHOW_SUBSCRIBE_PAGE'] === 'Y') {
// 	$availablePages[] = array(
// 		"path" => $arResult['PATH_TO_SUBSCRIBE'],
// 		"name" => Loc::getMessage("SPS_SUBSCRIBE_PAGE_NAME"),
// 		"icon" => '<i class="fa fa-envelope"></i>'
// 	);
// }

// if ($arParams['SHOW_CONTACT_PAGE'] === 'Y') {
// 	$availablePages[] = array(
// 		"path" => $arParams['PATH_TO_CONTACT'],
// 		"name" => Loc::getMessage("SPS_CONTACT_PAGE_NAME"),
// 		"icon" => '<i class="fa fa-info-circle"></i>'
// 	);
// }

$customPagesList = CUtil::JsObjectToPhp($arParams['~CUSTOM_PAGES']);

if ($customPagesList) {
	foreach ($customPagesList as $page) {
		$availablePages[] = array(
			"path" => $page[0],
			"name" => $page[1],
			"icon" => (strlen($page[2])) ? '<i class="fa '.htmlspecialcharsbx($page[2]).'"></i>' : ""
		);
	}
}

if (empty($availablePages)) {

	ShowError(Loc::getMessage("SPS_ERROR_NOT_CHOSEN_ELEMENT"));

} else {

foreach ($availablePages as $blockElement) { ?>

	<a class="sale-personal-section-index-block-link" href="<?=htmlspecialcharsbx($blockElement['path'])?>">
		<h2 class="sale-personal-section-index-block-name">
			<?=htmlspecialcharsbx($blockElement['name'])?>
		</h2>
	</a>

<? } } ?>