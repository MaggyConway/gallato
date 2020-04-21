<? // для уже авторизованных
//define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход на сайт");


// $userName = CUser::GetFullName();
// if (!$userName)
// 	$userName = CUser::GetLogin();
/*?>

<script>
	<?if ($userName):?>
	BX.localStorage.set("eshop_user_name", "<?=CUtil::JSEscape($userName)?>", 604800);
	<?else:?>
	BX.localStorage.remove("eshop_user_name");
	<?endif?>
</script> 


<?*/ 

global $USER;
if ($USER->IsAuthorized()) {
	LocalRedirect("/personal/");
}

?>


<div class="wrapper">
	
	<?

	$APPLICATION->IncludeComponent("custom:system.auth.form", "main", Array(
		"FORGOT_PASSWORD_URL" => "",	// Страница забытого пароля
		"PROFILE_URL" => "/personal/",	// Страница профиля
		"REGISTER_URL" => "/registr/",	// Страница регистрации
		"SHOW_ERRORS" => "Y",	// Показывать ошибки
	),
		false
	);

	// $APPLICATION->IncludeComponent(
	// 	"bitrix:system.auth.authorize",
	// 	"login",
	// 	Array(
	// 		"FORGOT_PASSWORD_URL" => "",
	// 		"PROFILE_URL" => "/personal/",
	// 		"REGISTER_URL" => "/registr/",
	// 		"SHOW_ERRORS" => "Y"
	// 	)
	// );
?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>