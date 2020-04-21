<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();?>


<div class="bx-auth-reg">

<? if($USER->IsAuthorized()): ?>

	<p>Вы зарегистрированы на сервере и успешно авторизованы.</p>

<?else:?>



<? if (count($arResult["ERRORS"]) > 0):
	foreach ($arResult["ERRORS"] as $key => $error)
		if (intval($key) == 0 && $key !== 0) 
			$arResult["ERRORS"][$key] = str_replace("#FIELD_NAME#", "&quot;".GetMessage("REGISTER_FIELD_".$key)."&quot;", $error);

	ShowError(implode("<br />", $arResult["ERRORS"]));

elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"): ?>

	<p>На указанный в форме email придет запрос на подтверждение регистрации.</p>

<?endif?>





<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">

<? 	//echo "<pre>"; var_dump($arResult); echo "</pre>";
	//echo "<pre>"; var_dump($arResult["USER_PROPERTIES"]["DATA"]["UF_INN"]); echo "</pre>"; ?>


	<input size="30" type="text" name="REGISTER[NAME]" value="<?=$arResult["VALUES"]["NAME"]?>" 
			placeholder="<?=GetMessage("REGISTER_FIELD_NAME")?>" class="field" />

	<input size="30" type="text" value="" 
			placeholder="ИНН" class="field" />

	<input size="30" type="text" value="" 
			placeholder="Наименование организации" class="field" />

	<input size="30" type="text" value="" 
			placeholder="Город" class="field" />

	<input size="30" type="text" name="REGISTER[PERSONAL_PHONE]" value="<?=$arResult["VALUES"]["PERSONAL_PHONE"]?>" 
			placeholder="<?=GetMessage("REGISTER_FIELD_PERSONAL_PHONE")?>" class="field" />


	<input size="30" type="text" name="REGISTER[EMAIL]" value="<?=$arResult["VALUES"]["EMAIL"]?>" 
			placeholder="<?=GetMessage("REGISTER_FIELD_EMAIL")?>" class="field" />


	<input size="30" type="password" name="REGISTER[PASSWORD]" value="<?=$arResult["VALUES"]["PASSWORD"]?>" autocomplete="off" class="bx-auth-input field" minlength="6" placeholder="<?=GetMessage("REGISTER_FIELD_PASSWORD")?>" />


	<input size="30" type="password" name="REGISTER[CONFIRM_PASSWORD]" value="<?=$arResult["VALUES"]["CONFIRM_PASSWORD"]?>" autocomplete="off" minlength="6" placeholder="<?=GetMessage("REGISTER_FIELD_CONFIRM_PASSWORD")?>" class="field" />


<?/*if($arResult["SECURE_AUTH"]):?>

	<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
		<div class="bx-auth-secure-icon"></div>
	</span>
	<noscript>
	<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
		<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
	</span>
	</noscript>


	<script type="text/javascript">
	document.getElementById('bx_auth_secure').style.display = 'inline-block';
	</script>

<?endif */?>



<?// ********************* User properties ***************************************************?>
<? /*
if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>

	<?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?>


	<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>

		<?=$arUserField["EDIT_FORM_LABEL"]?>:

				<?if ($arUserField["MANDATORY"]=="Y"):?>
					<span class="starrequired">*</span>
				<?endif;?>

				<?$APPLICATION->IncludeComponent(
					"bitrix:system.field.edit",
					$arUserField["USER_TYPE"]["USER_TYPE_ID"],
					array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "regform"), null, array("HIDE_ICONS"=>"Y"));?>
						
	<?endforeach;?>
<?endif;
*/?>




<?// ******************** /User properties ***************************************************?>
<?
/* CAPTCHA */

/*if ($arResult["USE_CAPTCHA"] == "Y") { ?>
		
		<b><?=GetMessage("REGISTER_CAPTCHA_TITLE")?></b>

		<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
		
		<?=GetMessage("REGISTER_CAPTCHA_PROMT")?>:

		<input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />

<? } */?>

<p class="allow">Даю согласие на&nbsp;обработку <a href="/policy/">персональных данных</a></p>

<div class="captcha">МЕСТО ПОД КАПЧУ ГУГЛ</div>

<div class="auth--flex">

	<input type="submit" name="register_submit_button" value="Регистрация" class="btn" />
	
	<div class="login">
		<p>Уже зарегистрированы?</p>
		<p><a href="/login/">Войдите в личный кабинет</a></p>
	</div>

</div>
</form>

<?endif?>
</div>