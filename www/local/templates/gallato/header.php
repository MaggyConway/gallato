<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>
<!DOCTYPE html>
<html>
<head>
	<script src="/local/templates/gallato/dist/jquery-3.4.1.min.js"></script>
	<?$APPLICATION->ShowHead();?>
	<title><?$APPLICATION->ShowTitle();?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />

	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700&display=swap&subset=cyrillic" rel="stylesheet">
	<link rel="stylesheet" href="/local/templates/gallato/dist/UItoTop-jQuery-Plugin/css/ui.totop.css">
	<link rel="stylesheet" href="/local/templates/gallato/dist/slick-1.8.1/slick.css">
	<link rel="stylesheet" href="/local/templates/gallato/css/modals.css" />
	

	<link rel="stylesheet" href="/local/templates/gallato/dist/MultiLevelPushMenu/css/normalize.css" />
	<!-- <link rel="stylesheet" href="/local/templates/gallato/dist/MultiLevelPushMenu/css/demo.css" /> -->
	<link rel="stylesheet" href="/local/templates/gallato/dist/MultiLevelPushMenu/css/icons.css" />
	<link rel="stylesheet" href="/local/templates/gallato/dist/MultiLevelPushMenu/css/component.css" />

	<link rel="stylesheet" href="/local/templates/gallato/css/app.css" />
	
	<script src="/local/templates/gallato/dist/MultiLevelPushMenu/js/modernizr.custom.js"></script>

	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=cc9911f4-ceec-4056-9fcb-5b6dcf9ec3d0" type="text/javascript"></script>
	<script src="/local/templates/gallato/js/groups.js"></script>
</head>
<body>
<div id="panel">
	<?$APPLICATION->ShowPanel();?>
</div>

<div class="container">
	<!-- Push Wrapper -->
	<div class="mp-pusher" id="mp-pusher">

		


<? $APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list", 
	"mobile_menu", 
	array(
		"ADD_SECTIONS_CHAIN" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "N",
		"COUNT_ELEMENTS" => "Y",
		"FILTER_NAME" => "",
		"IBLOCK_ID" => "8",
		"IBLOCK_TYPE" => "catalog",
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_PARENT_NAME" => "Y",
		"TOP_DEPTH" => "2",
		"VIEW_MODE" => "LIST",
		"COMPONENT_TEMPLATE" => "outer_categories_list"
	),
	false
); ?>



		<div class="scroller"><!-- this is for emulating position fixed of the nav -->
			<div class="scroller-inner">



<? //считаем кол-во товара и общую сумму в корзине - БЕЗ AJAX-A!

// if (CModule::IncludeModule("sale")) {

//    $arBasketItems = array();
//    $dbBasketItems = CSaleBasket::GetList(
//       array("NAME" => "ASC","ID" => "ASC"),
//       array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
//       false,
//       false,
//       array("ID","MODULE","PRODUCT_ID","QUANTITY","CAN_BUY","PRICE"));
   
//    while ($arItems=$dbBasketItems->Fetch()) {

//       $arItems=CSaleBasket::GetByID($arItems["ID"]);
//       $arBasketItems[]=$arItems;   
//       $cart_num+=$arItems['QUANTITY'];
//       $cart_sum+=$arItems['PRICE']*$arItems['QUANTITY'];
//    }
//    if (empty($cart_num))
//       $cart_num="0"; //кол-во товаров

//    if (empty($cart_sum))
//       $cart_sum="0"; // на сумму
// }


?>
<header>
	<div class="header__top">
		<div class="wrapper">

			<? global $USER;
			if ($USER->IsAuthorized()) { 

				//GetID() - ид юзера 
				//GetLogin() - логин юзера 
				//GetFullName() - ФИО юзера (если было задано юзером)
				
			?>
				<a href="/personal/"><? echo $USER->GetFullName(); ?></a>&nbsp;&nbsp;&nbsp;

				<a href="?logout=yes">Выйти</a>


			<? } else { ?>

				<a href="/login/">Вход</a>
				<span>/</span>
				<a href="/registr/">Регистрация</a>
			<?}?>
		</div>
	</div>
	<div class="header__main">
		<div class="wrapper">
			<a href="/" class="logo"></a>
			<div class="phones">
				<a href="tel:89139740984">8 (913) 974 09 84</a>
				<a href="tel:83812700103">8 (3812) 700 103</a>
				<div class="request">Заказать звонок</div>
			</div>

			<?$APPLICATION->IncludeComponent(
				"bitrix:search.title",
				"main",
				Array(
					"CATEGORY_0" => array("iblock_catalog"),
					"CATEGORY_0_TITLE" => "",
					"CATEGORY_0_iblock_catalog" => array("8"),
					"CHECK_DATES" => "N",
					"CONTAINER_ID" => "title-search",
					"INPUT_ID" => "title-search-input",
					"NUM_CATEGORIES" => "1",
					"ORDER" => "date",
					"PAGE" => "#SITE_DIR#search/",
					"SHOW_INPUT" => "Y",
					"SHOW_OTHERS" => "N",
					"TOP_COUNT" => "5",
					"USE_LANGUAGE_GUESS" => "Y"
				)
			);?>
			

			<div class="user_links">
				<a href="/personal/" class="profile">
					

<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
	<path d="M10.8431 10.5975C12.299 10.5975 13.5595 10.0753 14.5898 9.04507C15.6197 8.015 16.142 6.75464 16.142 5.29857C16.142 3.84301 15.6198 2.58248 14.5896 1.55208C13.5594 0.52217 12.2988 0 10.8431 0C9.38703 0 8.12667 0.52217 7.0966 1.55224C6.06653 2.58232 5.54419 3.84284 5.54419 5.29857C5.54419 6.75464 6.06653 8.01517 7.0966 9.04524C8.12701 10.0751 9.38754 10.5975 10.8431 10.5975ZM8.00834 2.46382C8.79873 1.67343 9.72591 1.28923 10.8431 1.28923C11.9601 1.28923 12.8875 1.67343 13.678 2.46382C14.4684 3.25437 14.8528 4.18172 14.8528 5.29857C14.8528 6.41576 14.4684 7.34294 13.678 8.1335C12.8875 8.92406 11.9601 9.30826 10.8431 9.30826C9.72625 9.30826 8.79907 8.92389 8.00834 8.1335C7.21779 7.34311 6.83342 6.41576 6.83342 5.29857C6.83342 4.18172 7.21779 3.25437 8.00834 2.46382Z" fill="#252525"/>
	<path d="M20.1149 16.917C20.0852 16.4883 20.0251 16.0207 19.9366 15.5269C19.8473 15.0294 19.7324 14.5591 19.5947 14.1292C19.4524 13.6849 19.2592 13.2462 19.02 12.8257C18.7721 12.3893 18.4807 12.0093 18.1538 11.6966C17.8119 11.3695 17.3932 11.1065 16.9092 10.9146C16.4268 10.7238 15.8922 10.6271 15.3203 10.6271C15.0958 10.6271 14.8786 10.7193 14.4591 10.9924C14.201 11.1607 13.899 11.3554 13.562 11.5708C13.2738 11.7544 12.8834 11.9264 12.4012 12.0822C11.9307 12.2344 11.453 12.3116 10.9814 12.3116C10.51 12.3116 10.0323 12.2344 9.56154 12.0822C9.07982 11.9266 8.68924 11.7545 8.40155 11.5709C8.06771 11.3576 7.76558 11.1629 7.50357 10.9922C7.08446 10.7191 6.86727 10.627 6.64269 10.627C6.07067 10.627 5.53625 10.7238 5.05402 10.9148C4.57029 11.1063 4.15152 11.3693 3.80928 11.6968C3.48231 12.0097 3.19093 12.3895 2.94319 12.8257C2.70434 13.2462 2.51098 13.6848 2.36865 14.1294C2.23119 14.5593 2.11621 15.0294 2.02692 15.5269C1.93829 16.02 1.87837 16.4878 1.84866 16.9175C1.81946 17.3376 1.80469 17.7749 1.80469 18.2166C1.80469 19.365 2.16975 20.2947 2.88965 20.9804C3.60064 21.657 4.54125 22.0001 5.68546 22.0001H16.2786C17.4225 22.0001 18.3631 21.657 19.0742 20.9804C19.7943 20.2952 20.1594 19.3652 20.1594 18.2165C20.1592 17.7732 20.1443 17.3359 20.1149 16.917ZM18.1853 20.0463C17.7155 20.4935 17.0918 20.7108 16.2784 20.7108H5.68546C4.87191 20.7108 4.24819 20.4935 3.77856 20.0465C3.31782 19.6079 3.09392 19.0092 3.09392 18.2166C3.09392 17.8044 3.10751 17.3974 3.1347 17.0066C3.16122 16.6233 3.21544 16.2021 3.29583 15.7547C3.37523 15.3127 3.47627 14.898 3.59645 14.5225C3.71176 14.1625 3.86903 13.806 4.06407 13.4625C4.25021 13.1352 4.46438 12.8544 4.70071 12.6282C4.92176 12.4165 5.20039 12.2433 5.52869 12.1134C5.83233 11.9932 6.17356 11.9274 6.544 11.9175C6.58915 11.9415 6.66955 11.9873 6.79979 12.0723C7.06482 12.245 7.3703 12.442 7.70801 12.6577C8.08869 12.9004 8.57913 13.1196 9.16509 13.3088C9.76413 13.5025 10.3751 13.6009 10.9815 13.6009C11.5879 13.6009 12.1991 13.5025 12.7978 13.309C13.3842 13.1195 13.8745 12.9004 14.2557 12.6574C14.6013 12.4365 14.8982 12.2452 15.1632 12.0723C15.2935 11.9875 15.3739 11.9415 15.419 11.9175C15.7896 11.9274 16.1309 11.9932 16.4347 12.1134C16.7628 12.2433 17.0414 12.4167 17.2625 12.6282C17.4988 12.8543 17.713 13.1351 17.8991 13.4627C18.0943 13.806 18.2518 14.1626 18.3669 14.5223C18.4873 14.8983 18.5885 15.3129 18.6677 15.7545C18.7479 16.2028 18.8023 16.6241 18.8288 17.0068V17.0071C18.8562 17.3964 18.87 17.8032 18.8701 18.2166C18.87 19.0094 18.6461 19.6079 18.1853 20.0463Z" fill="#252525"/>
</svg>

				</a>

				<?
				global $USER;
				global $fav_products;
				$rsUsers = CUser::GetByID($USER->GetID());
				$arUser = $rsUsers->Fetch();
				//var_dump($arUser["UF_FAVORITES"]);
				$fav_products = ($arUser["UF_WISHLIST"] !== false) ? $arUser["UF_WISHLIST"] : array();
				?>
				<a href="/wish-list/" class="love_list">


<svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
	<path d="M11 19.7761C10.6868 19.7761 10.3848 19.6626 10.1495 19.4565C9.26077 18.6794 8.40392 17.9491 7.64793 17.3049L7.64407 17.3015C5.42766 15.4127 3.5137 13.7816 2.18201 12.1748C0.693375 10.3785 0 8.67535 0 6.81477C0 5.00706 0.619858 3.33934 1.74527 2.11859C2.88411 0.883408 4.44676 0.203125 6.14587 0.203125C7.4158 0.203125 8.57881 0.604614 9.60251 1.39635C10.1191 1.79599 10.5874 2.2851 11 2.85561C11.4127 2.2851 11.8809 1.79599 12.3977 1.39635C13.4214 0.604614 14.5844 0.203125 15.8543 0.203125C17.5532 0.203125 19.1161 0.883408 20.2549 2.11859C21.3803 3.33934 22 5.00706 22 6.81477C22 8.67535 21.3068 10.3785 19.8182 12.1746C18.4865 13.7816 16.5727 15.4126 14.3566 17.3012C13.5993 17.9464 12.7411 18.6778 11.8503 19.4568C11.6152 19.6626 11.313 19.7761 11 19.7761ZM6.14587 1.49185C4.81099 1.49185 3.5847 2.0246 2.6926 2.99207C1.78723 3.97414 1.28856 5.33168 1.28856 6.81477C1.28856 8.37961 1.87015 9.77911 3.17415 11.3525C4.43451 12.8734 6.30919 14.4709 8.47978 16.3208L8.48381 16.3241C9.24265 16.9708 10.1029 17.704 10.9982 18.4868C11.8988 17.7025 12.7604 16.9682 13.5207 16.3204C15.6911 14.4706 17.5657 12.8734 18.826 11.3525C20.1299 9.77911 20.7114 8.37961 20.7114 6.81477C20.7114 5.33168 20.2128 3.97414 19.3074 2.99207C18.4155 2.0246 17.189 1.49185 15.8543 1.49185C14.8764 1.49185 13.9786 1.8027 13.1859 2.41568C12.4794 2.96219 11.9873 3.65305 11.6987 4.13644C11.5504 4.38503 11.2892 4.5334 11 4.5334C10.7108 4.5334 10.4496 4.38503 10.3013 4.13644C10.0129 3.65305 9.52077 2.96219 8.81413 2.41568C8.02139 1.8027 7.12358 1.49185 6.14587 1.49185Z" fill="#252525"/>
</svg>





<? if ($USER->IsAuthorized()) { ?>
					<span id="wish_count"><?=count($fav_products)?></span>
<?}?>

				</a>


				<?$APPLICATION->IncludeComponent(
					"bitrix:sale.basket.basket.line",
					"mini-basket",
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
		</div>
	</div>
	<div class="header__menu">
		<div class="wrapper">

			<? $APPLICATION->IncludeComponent(
				"bitrix:catalog.section.list", 
				"outer_categories_list", 
				array(
					"ADD_SECTIONS_CHAIN" => "Y",
					"CACHE_FILTER" => "N",
					"CACHE_GROUPS" => "Y",
					"CACHE_TIME" => "0",
					"CACHE_TYPE" => "N",
					"COUNT_ELEMENTS" => "Y",
					"FILTER_NAME" => "",
					"IBLOCK_ID" => "8",
					"IBLOCK_TYPE" => "catalog",
					"SECTION_CODE" => "",
					"SECTION_FIELDS" => array(
						0 => "",
						1 => "",
					),
					"SECTION_ID" => $_REQUEST["SECTION_ID"],
					"SECTION_URL" => "",
					"SECTION_USER_FIELDS" => array(
						0 => "",
						1 => "",
					),
					"SHOW_PARENT_NAME" => "Y",
					"TOP_DEPTH" => "2",
					"VIEW_MODE" => "LIST",
					"COMPONENT_TEMPLATE" => "outer_categories_list"
				),
				false
			); ?>	
		</div>
	</div>
	<div class="mobile_icon_box" id="trigger">
		<div class="mobile_icon">
			<span class="line line-top"></span>
			<span class="line line-middle"></span>
			<span class="line line-bottom"></span>
		</div>
	</div>
</header>

<? // включаемая область для раздела
	$APPLICATION->IncludeFile(
	    SITE_DIR . "/include/burger_menu.php",
	    Array(),
	    Array(
	        "MODE" => "html")
	);
?>
<? // включаемая область для раздела
	$APPLICATION->IncludeFile(
	    SITE_DIR . "/include/modal_form.php",
	    Array(),
	    Array(
	        "MODE" => "html")
	);
?>
<? // включаемая область для раздела
	$APPLICATION->IncludeFile(
	    SITE_DIR . "/include/success_modal.php",
	    Array(),
	    Array(
	        "MODE" => "html")
	);
?>
<? // включаемая область для раздела
	$APPLICATION->IncludeFile(
	    SITE_DIR . "/include/success_comment.php",
	    Array(),
	    Array(
	        "MODE" => "html")
	);
?>

<? if ($APPLICATION->GetCurPage(false) !== '/') { ?>

	<div class="section_top_block">
		<div class="wrapper">
			<h1><?$APPLICATION->ShowTitle();?></h1>

			<?$APPLICATION->IncludeComponent(
				"bitrix:breadcrumb",
				"main",
				Array(
					"PATH" => "",
					"SITE_ID" => "s1",
					"START_FROM" => "0"
				)
			);?>
		</div>
	</div>

<? } ?>