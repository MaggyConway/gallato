<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//------------bigbonuse module code add------------------------------
global $USER;
if(\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus")) {
    if ($_POST["PAY_BONUS_ACCOUNT"] == "Y") {
        $arResult['USER_VALS']["PAY_BONUS_ACCOUNT"] = "Y";
    }
    if ($arParams["PAY_FROM_ACCOUNT"] == "Y") {

        $bb = new \VBCherepanov\Bonus\BMain(SITE_ID);
        if (!$bb->ACCOUNT_BITRIX && $bb->SETTINGS['SITE_ON'][$bb->LID] == "Y") {
            $res = \VBCherepanov\Bonus\AccountTable::getList(
                array(
                    'filter' => array("USER_ID" => $USER->GetID()),
                    'select' => array('CURRENT_BUDGET'),
                )
            )->fetch();
            if ($res['CURRENT_BUDGET']) {
                $arResult["PAY_FROM_BONUS"] = "Y";
            } else {
                $arResult["PAY_FROM_BONUS"] = "N";
            }
        }

    } else {
        $arResult["PAY_FROM_ACCOUNT"] = "N";
    }
}
//------------bigbonuse module code end------------------------------
?>