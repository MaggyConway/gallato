<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?><div class="wrapper">
 <section class="registr">
	<div class="btns">
		<div id="rozn" class="btn <?=(isset($_GET["opt"])) ? "" : "active" ?>">
			 Розничный покупатель
		</div>
		<div id="opt" class="btn <?=(isset($_GET["opt"])) ? "active" : "" ?>">
			 Оптовый покупатель
		</div>
	</div>
	<div class="areas">
		<div class="rozn--area <?=(isset($_GET["opt"])) ? "" : "show" ?>">

			<?
			$APPLICATION->IncludeComponent("custom:system.auth.form", "main", Array(
				"FORGOT_PASSWORD_URL" => "",	// Страница забытого пароля
				"PROFILE_URL" => "/login/",	// Страница профиля
				"REGISTER_URL" => "/registr/",	// Страница регистрации
				"SHOW_ERRORS" => "Y",	// Показывать ошибки
			),
				false
			);

			$APPLICATION->IncludeComponent(
				"custom:system.auth.registration.rozn",
				"",
				Array(
					"PROFILE_URL" => "/login/",	// Страница профиля
					"REGISTER_URL" => "/registr/",	// Страница регистрации
					"SHOW_ERRORS" => "Y",	// Показывать ошибки
				)
			);
			?>
		</div>
		<div class="opt--area <?=(isset($_GET["opt"])) ? "show" : "" ?>">

			<?
			$APPLICATION->IncludeComponent("custom:system.auth.form", "main", Array(
				"FORGOT_PASSWORD_URL" => "",	// Страница забытого пароля
				"PROFILE_URL" => "/login/",	// Страница профиля
				"REGISTER_URL" => "/registr/",	// Страница регистрации
				"SHOW_ERRORS" => "Y",	// Показывать ошибки
			),
				false
			);


			$APPLICATION->IncludeComponent(
				"custom:system.auth.registration.opt",
				"",
				Array(
					"PROFILE_URL" => "/login/",	// Страница профиля
					"REGISTER_URL" => "/registr/",	// Страница регистрации
					"SHOW_ERRORS" => "Y",	// Показывать ошибки
				)
			);
			?>
		</div>
	</div>
 </section>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>