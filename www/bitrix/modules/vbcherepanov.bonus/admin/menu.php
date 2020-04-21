<?

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$aMenu[] = array(
    "parent_menu" => "global_menu_acrit",
    "section" => "vbchbonus",
    "sort" => 150,
    "more_url" => array(
        "vbchbb.php?lang=" . LANGUAGE_ID,
        "vbchbb_account.php?lang=" . LANGUAGE_ID,
    ),
    "text" => Loc::getMessage('VBCHBONUS_TITLE'),
    "title" => Loc::getMessage('VBCHBONUS_TITLE'),
    "icon" => "vbchbonus_menu_icon",
    "page_icon" => "vbchbonus_menu_icon",
    "module_id" => "vbcherepanov.bonus",
    "items_id" => "menu_bigbonus",
    "items" => array(
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_PROFILES"),
            "title" => Loc::getMessage("VBCHBB_BONUS_PROFILES"),
            "url" => "vbchbb_profiles.php?lang=" . LANGUAGE_ID,
            "more_url" => array(
                "vbchbb_profiles.php?lang=" . LANGUAGE_ID,
                "vbchbb_profiles_edit.php?lang=" . LANGUAGE_ID,
            ),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS"),
            "title" => Loc::getMessage("VBCHBB_BONUS"),
            "url" => "vbchbb.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_ACCOUNT"),
            "title" => Loc::getMessage("VBCHBB_BONUS_ACCOUNT"),
            "url" => "vbchbb_account.php?lang=" . LANGUAGE_ID,
            "more_url" => array(
                "vbchbb_account.php?lang=" . LANGUAGE_ID,
                "vbchbb_bonus_accountedit.php?lang=" . LANGUAGE_ID,
            ),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_REFERAL"),
            "title" => Loc::getMessage("VBCHBB_BONUS_REFERAL"),
            "url" => "vbchbb_referal.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_referal.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_ADDFUNCTION"),
            "title" => Loc::getMessage("VBCHBB_BONUS_ADDFUNCTION"),
            "url" => "vbchbb_function.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_function.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_BONUSACCOUNTS"),
            "title" => Loc::getMessage("VBCHBB_BONUS_BONUSACCOUNTS"),
            "url" => "vbchbb_bonusaccounts.php?lang=" . LANGUAGE_ID,
            "more_url" => array(
                "vbchbb_bonusaccounts.php?lang=" . LANGUAGE_ID,
                "vbchbb_bonusaccountsedit.php?lang=" . LANGUAGE_ID,
            ),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_DOUBLE"),
            "title" => Loc::getMessage("VBCHBB_BONUS_DOUBLE"),
            "url" => "vbchbb_double.php?lang=" . LANGUAGE_ID,
            "more_url" => array(
                "vbchbb_double.php?lang=" . LANGUAGE_ID,
                "vbchbb_double.php?lang=" . LANGUAGE_ID,
            ),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_CHECK_BALANCE"),
            "title" => Loc::getMessage("VBCHBB_CHECK_BALANCE"),
            "url" => "vbchbb_checkbalance.php?lang=" . LANGUAGE_ID,
            "more_url" => array(
                "vbchbb_checkbalance.php?lang=" . LANGUAGE_ID,
                "vbchbb_checkbalance.php?lang=" . LANGUAGE_ID,
            ),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_AFFILIATES"),
            "title" => Loc::getMessage("VBCHBB_BONUS_AFFILIATES"),
            "url" => "vbchbb_affiliate.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_affiliate.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_MONEYBACK"),
            "title" => Loc::getMessage("VBCHBB_BONUS_MONEYBACK"),
            "url" => "vbchbb_moneyback.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_moneyback.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_CARD"),
            "title" => Loc::getMessage("VBCHBB_BONUS_CARD"),
            "url" => "vbchbb_bonuscard.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_bonuscard.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_COUPONE"),
            "title" => Loc::getMessage("VBCHBB_BONUS_COUPONE"),
            "url" => "vbchbb_coupone.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_coupone.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_COUPONE_STATISTIC"),
            "title" => Loc::getMessage("VBCHBB_BONUS_COUPONE_STATISTIC"),
            "url" => "vbchbb_coupone_statistic.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_coupone_statistic.php?lang=" . LANGUAGE_ID),
        ),
        array(
            "text" => Loc::getMessage("VBCHBB_BONUS_SUPPORT"),
            "title" => Loc::getMessage("VBCHBB_BONUS_SUPPORT"),
            "url" => "vbchbb_support.php?lang=" . LANGUAGE_ID,
            "more_url" => array("vbchbb_support.php?lang=" . LANGUAGE_ID),
        ),
    )
);
return $aMenu;
