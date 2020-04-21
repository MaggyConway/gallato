<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Франшиза");
?>

<section class="franchise_page">
	<div class="photo"></div>
	<div class="wrapper page_title">
		<h1>Прибыльная франшиза уникальных сумок и&nbsp;аксессуаров Gallato</h1>
		<p class="notice">Зарабатывайте от&nbsp;100&nbsp;000&nbsp;руб. в&nbsp;месяц</p>
	</div>

<div class="wrapper">

		<div class="main_text">
			Не&nbsp;следует, однако забывать, что новая модель организационной деятельности требуют от&nbsp;нас анализа модели развития. Значимость этих проблем настолько очевидна, что консультация с&nbsp;широким активом позволяет выполнять важные задания по&nbsp;разработке системы обучения кадров, соответствует насущным потребностям. Не&nbsp;следует, однако забывать, что новая модель организационной деятельности требуют от&nbsp;нас анализа модели развития. Значимость этих проблем настолько очевидна, что консультация с&nbsp;широким активом позволяет выполнять важные задания по&nbsp;разработке системы обучения кадров, соответствует насущным потребностям.
		</div>
		<div class="statistics">
			<div class="item">
				<div class="number">65</div>
				<div class="txt">Торговых точек</div>
			</div>
			<div class="item">
				<div class="number">430&nbsp;000</div>
				<div class="txt">Продаж за год</div>
			</div>
			<div class="item">
				<div class="number">38</div>
				<div class="txt">Франчази</div>
			</div>
			<div class="item">
				<div class="number">9</div>
				<div class="txt">Городов</div>
			</div>
		</div>

		<a href="https://gallam.ru" class="btn" target="_blank">перейти на сайт</a>

</div>

		<div class="flexbox" method="POST">
			<form action="/include/send.php" class="franchise_form">
				<div class="form_title">Оставить заявку</div>
				<input type="text" name="name" placeholder="Имя" required> 
				<input type="text" name="phone" placeholder="Телефон" required> 
				<input type="hidden" name="event" value="FRANCHISE_FORM" />
				<p class="allow">Даю согласие на обработку <a href="/policy/">персональных данных</a></p>
				<button type="submit" class="btn">ОТПРАВИТЬ</button>
			</form>
			<div class="image"></div>
		</div>
</section>

<div class="wrapper">
<? // включаемая область для раздела
	$APPLICATION->IncludeFile(
	    SITE_DIR . "/include/main_text.php",
	    Array(),
	    Array(
	        "MODE" => "html")
	);
?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>