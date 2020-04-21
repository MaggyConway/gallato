<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($_POST["PAY_BONUS_ACCOUNT"] == "Y") {
    $arResult['USER_VALS']["PAY_BONUS_ACCOUNT"] = "Y";
}
if ($_POST["PAY_BONUSORDERPAY"] == "Y") {
    $arResult['USER_VALS']["PAY_BONUSORDERPAY"] = "Y";
}
