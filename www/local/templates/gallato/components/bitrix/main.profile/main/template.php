<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)	die();

// $photo = CFile::GetPath($arResult["arUser"]["PERSONAL_PHOTO"]);

// $sth = CFile::ShowImage($arResult["arUser"]["PERSONAL_PHOTO"], 150, 150, "border=0", "", true);

//echo $sth;
//echo '<pre>'; var_dump($arResult["arUser"]["PERSONAL_BIRTHDAY"]); echo '</pre>';


?>

<div class="wrapper">
	<?ShowError($arResult["strProfileError"]);?>


<form method="post" action="<?=$arResult["FORM_TARGET"]?>" 
	enctype="multipart/form-data" class="profile_form">

	<?
		if ($arResult['DATA_SAVED'] == 'Y') {
			$APPLICATION->IncludeFile(
				SITE_DIR . "/include/profile_save_success.php",
				Array(),
				Array(
					"MODE" => "html")
			);
			//echo "данные сохранены";

		} elseif ($arResult['DATA_SAVED'] == 'N') {
			$APPLICATION->IncludeFile(
				SITE_DIR . "/include/profile_save_error.php",
				Array(),
				Array(
					"MODE" => "html")
			);
			//echo "данные НЕ сохранены. возникла ошибка!";
		}
	?>

	<?=$arResult["BX_SESSION_CHECK"];
// echo "<pre>";
// var_dump($arResult);
// echo "</pre>";
	?>

	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />

	<input type="text" name="NAME" value="<?=$arResult["arUser"]["NAME"]?>" class="field" placeholder="Введите ФИО">

	<input type="email" name="EMAIL" value="<?=$arResult["arUser"]["EMAIL"]?>" class="field" placeholder="Введите Email">


	<input type="password" name="NEW_PASSWORD" autocomplete="off" class="field" maxlength="50" placeholder="Введите пароль">
	<input type="password" name="NEW_PASSWORD_CONFIRM" autocomplete="off" maxlength="50" class="field" placeholder="Повторите пароль">

	<input type="hidden" name="event" value="PROFILE_FORM">
	<input type="submit" name="save" value="Сохранить" class="btn btn_save">

</form>
</div>