<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>

<div class="wrapper">
<section class="contacts">
	
	<div class="order">
		
		<div>
			<h2>По&nbsp;вопросам заказа</h2>
			<div class="links">
				<a href="tel:83812700103">8 (3812) 700 103</a>
				<a href="tel:89139740984">8 (913) 974 09 84</a>
				<a href="mailto:liderbag@mail.ru">liderbag@mail.ru</a>
			</div>
			<div class="request">Заказать звонок</div>
			<div class="btn_requisites">Реквизиты компании</div>
		</div>
		<div>
			<h2>Оптовые заказы и&nbsp;франшиза</h2>
			<div class="links">
				<a href="tel:83812700103">8 (3812) 700 103</a>
				<a href="mailto:gallatofr@mail.ru">gallatofr@mail.ru</a>
			</div>
			<div class="request">Заказать звонок</div>
		</div>

	</div>
	
	<div class="shops_with_map">
		
		<h2>Наши магазины</h2>

		<div class="map_box">
			<div class="accordeon"></div>
			<div id="map"></div>
		</div>

	</div>



</section>
</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>