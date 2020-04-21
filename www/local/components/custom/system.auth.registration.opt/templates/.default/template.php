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
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if ($arResult["SHOW_SMS_FIELD"] == true) {
    CJSCore::Init('phone_auth');
}
?>
<div class="bx-auth">
    <?
    ShowMessage($arParams["~AUTH_RESULT"]);
    ?>
    <? if ($arResult["SHOW_EMAIL_SENT_CONFIRMATION"]): ?>
        <p><? echo GetMessage("AUTH_EMAIL_SENT") ?></p>
    <? endif; ?>

    <? if (!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"] && $arResult["USE_EMAIL_CONFIRMATION"] === "Y"): ?>
        <p><? echo GetMessage("AUTH_EMAIL_WILL_BE_SENT") ?></p>
    <? endif ?>
    <noindex>

<? //echo "<pre>"; var_dump($arResult); echo "</pre>"; ?>

        <div class="bx-system-auth-form-register">

            <form class="auth-other-form" method="post" action="<?= $arResult["AUTH_URL"] ?>&opt=yes" name="bform" enctype="multipart/form-data">
                <input type="hidden" name="AUTH_FORM" value="Y"/>
                <input type="hidden" name="TYPE" value="REGISTRATION"/>
                <input type="hidden" name="USER_TYPE" maxlength="50" value="OPT" class="bx-auth-input"/>

              
                    <input required type="text" placeholder="ФИО" name="USER_NAME" 
                    	maxlength="100" value="<?= $arResult["USER_NAME"] ?>"
                           class="field"/>
                  
                    <input required type="text" placeholder="ИНН" name="UF_INN" maxlength="50" value="<?= $arResult["UF_INN"] ?>"
                           class="field"/>
                   
                    <input required type="text" placeholder="Наименование компании" name="WORK_COMPANY" maxlength="50" value="<?= $arResult["WORK_COMPANY"] ?>"
                           class="field"/>
                    
                    <input required type="text" placeholder="Город" name="UF_CITY" maxlength="50" value="<?= $arResult["UF_CITY"] ?>"
                           class="field"/>
                   
                	<input required type="text" placeholder="Телефон" name="PERSONAL_PHONE" maxlength="50" value="<?= $arResult["PERSONAL_PHONE"] ?>"
                	class="field phone_field"/>

                    <input required type="email" placeholder="Email" name="USER_EMAIL" maxlength="50" value="<?= $arResult["USER_EMAIL"] ?>"
                           class="field"/>
                  
                    <input required type="password" placeholder="Придумайте пароль" name="USER_PASSWORD" maxlength="50" value="<?= $arResult["USER_PASSWORD"] ?>" class="field"  autocomplete="off"/>
                   
                    <input required type="password" placeholder="Повторите пароль" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?= $arResult["USER_CONFIRM_PASSWORD"] ?>" class="field"  autocomplete="off"/>
                    


                    <? $APPLICATION->IncludeComponent("bitrix:main.userconsent.request", "",
                        array(
                            "ID" => COption::getOptionString("main", "new_user_agreement", ""),
                            "IS_CHECKED" => "Y",
                            "AUTO_SAVE" => "N",
                            "IS_LOADED" => "Y",
                            "ORIGINATOR_ID" => $arResult["AGREEMENT_ORIGINATOR_ID"],
                            "ORIGIN_ID" => $arResult["AGREEMENT_ORIGIN_ID"],
                            "INPUT_NAME" => $arResult["AGREEMENT_INPUT_NAME"],
                            "REPLACE" => array(
                                "button_caption" => GetMessage("AUTH_REGISTER"),
                                "fields" => array(
                                    rtrim(GetMessage("AUTH_NAME"), ":"),
                                    rtrim(GetMessage("AUTH_LAST_NAME"), ":"),
                                    rtrim(GetMessage("AUTH_LOGIN_MIN"), ":"),
                                    rtrim(GetMessage("AUTH_PASSWORD_REQ"), ":"),
                                    rtrim(GetMessage("AUTH_EMAIL"), ":"),
                                )
                            ),
                        )
                    ); ?>




<p class="allow">Даю согласие на&nbsp;обработку <a href="/policy/">персональных данных</a></p>

<div class="captcha">МЕСТО ПОД КАПЧУ ГУГЛ</div>

<div class="auth--flex">

    <button type="submit" name="Register" class="btn">Регистрация</button>
    
    <div class="login">
        <p>Уже зарегистрированы?</p>
        <p><a href="/login/">Войдите в личный кабинет</a></p>
    </div>

</div>


</div>
</form>
</div>
</noindex>

<script type="text/javascript">
    document.bform.USER_NAME.focus();
</script>
</div>