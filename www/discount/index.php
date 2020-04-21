<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Скидки и бонусы");
?>

<section class="discount"><div class="wrapper">
	<h2>Скидка постоянного покупателя</h2>

	<div class="bonus_program">
		<div class="item">
			
		</div>
		<div class="item">
			
			<? // включаемая область для раздела
				$APPLICATION->IncludeFile(
				    SITE_DIR . "/include/bonus_program_txt.php",
				    Array(),
				    Array(
				        "MODE" => "html")
				);
			?>

		</div>
	</div>

	<div class="discount_text">
		
		<? // включаемая область для раздела
			$APPLICATION->IncludeFile(
			    SITE_DIR . "/include/discount_text.php",
			    Array(),
			    Array(
			        "MODE" => "html")
			);
		?>

	</div>
</div></section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>