<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();
?>

<footer><div class="wrapper">
	<a href="/" class="footer_logo"></a>
	<div class="footer_bottom">

		<? $APPLICATION->IncludeComponent(
			"bitrix:catalog.section.list", 
			"footer_categories_list", 
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
				"TOP_DEPTH" => "1",
				"VIEW_MODE" => "LIST",
				"COMPONENT_TEMPLATE" => "outer_categories_list"
			),
			false
		); ?>

		<?$APPLICATION->IncludeComponent( //class="footer__menu"
			"bitrix:menu", 
			"footer_menu", 
			array(
				"ALLOW_MULTI_SELECT" => "N",
				"CHILD_MENU_TYPE" => "sub-menu",
				"DELAY" => "N",
				"MAX_LEVEL" => "1",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MENU_CACHE_TIME" => "0",
				"MENU_CACHE_TYPE" => "N",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"ROOT_MENU_TYPE" => "footer",
				"USE_EXT" => "N",
				"COMPONENT_TEMPLATE" => "footer_menu"
			),
			false
		);?>

		<div class="footer_contacts">
			<a href="/contacts/" class="column_title">контакты</a>
				<a href="tel:83812700103">8 (3812) 700 103</a>
				<a href="tel:89139740984">8 (913) 974 09 84</a>
				<a href="" class="request">Заказать звонок</a>
				<a href="mailto:liderbag@mail.ru">liderbag@mail.ru</a>
				<a href="">Реквизиты компании</a>
		</div>
		<div class="footer_pay_policy">
			<a href="/pay-and-delivery/" class="column_title">принимаем платежи</a>
			<div class="pay_image"></div>
			<a href="/policy/">Политика конфиденциальности</a>
		</div>
	</div>
</div></footer>





</div><!-- /scroller-inner -->
		</div><!-- /scroller -->

	</div><!-- /pusher -->
</div><!-- /container -->



<script src="/local/templates/gallato/dist/slick-1.8.1/slick.min.js"></script>
<script src="/local/templates/gallato/dist/UItoTop-jQuery-Plugin/js/easing.js"></script>
<script src="/local/templates/gallato/dist/UItoTop-jQuery-Plugin/js/jquery.ui.totop.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$().UItoTop({ easingType: 'easeOutQuart' }); 
	});
</script>
<script src="/local/templates/gallato/dist/jquery.maskedinput-master/jquery.maskedinput.min.js"></script>

<script src="/local/templates/gallato/dist/MultiLevelPushMenu/js/classie.js"></script>
<script src="/local/templates/gallato/dist/MultiLevelPushMenu/js/mlpushmenu.js"></script>
<script>
	new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'trigger' ) );
</script>


<script src="/local/templates/gallato/js/app.js"></script>
</body>
</html>