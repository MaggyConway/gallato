<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оптовикам");
?>

<div class="wholesalers_page"><div class="wrapper">
	<h1>Оптовые поставки сумок и аксессуаров Gallato по самым выгодным условиям</h1>

	<div class="txt">
		Не&nbsp;следует, однако забывать, что новая модель организационной деятельности требуют от&nbsp;нас анализа модели развития. Значимость этих проблем настолько очевидна, что консультация с&nbsp;широким активом позволяет выполнять важные задания по&nbsp;разработке системы обучения кадров, соответствует насущным потребностям.
	</div>
</div>
	<div class="flexbox">
		<form action="/include/send.php" method="POST" class="wholesalers_form">
			<div class="form_title">Оставить заявку</div>
			<input type="text" name="name" placeholder="Имя" required> 
			<input type="text" name="phone" placeholder="Телефон" required> 
			<input type="hidden" name="event" value="WHOLESALERS_FORM" />
			<p class="allow">Даю согласие на обработку <a href="/policy/">персональных данных</a></p>
			<button type="submit" class="btn">ОТПРАВИТЬ</button>
		</form>
		<div class="image"></div>
	</div>
</div>

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