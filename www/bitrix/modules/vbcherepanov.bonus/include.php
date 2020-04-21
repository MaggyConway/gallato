<?php

use \Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);
$module_id = "vbcherepanov.bonus";
$arClassesList = array(
    "ITRound\\Vbchbbonus\\Vbchbbcore" => "lib/CVbchbbcore.php",
    "ITRound\\Vbchbbonus\\BonusTable" => "lib/BonusTable.php",
    "ITRound\\Vbchbbonus\\AccountTable" => "lib/AccountTable.php",
    "ITRound\\Vbchbbonus\\SetOption" => "lib/SetOption.php",
    "ITRound\\Vbchbbonus\\TmpTable" => "lib/TmpTable.php",
    "ITRound\\Vbchbbonus\\DoubleTable" => "lib/CDouble.php",
    "ITRound\\Vbchbbonus\\Vbchbbprofiles" => "lib/Cvbchbbprofiles.php",
    "ITRound\\Vbchbbonus\\CvbchbonusprofilesTable" => "lib/Cvbchbonusprofiles.php",
    "ITRound\\Vbchbbonus\\CvbchbonussocpushTable" => "lib/Cvbchbonussocpush.php",
    "ITRound\\Vbchbbonus\\Vbchreferal" => "lib/CVbchreferal.php",
    "ITRound\\Vbchbbonus\\CVbchRefTable" => "lib/CVbchRef.php",
    "ITRound\\Vbchbbonus\\Vbchbbwidget" => "lib/widget/Cvbchbbwidget.php",
    "ITRound\\Vbchbbonus\\ProfileRender" => "lib/widget/ProfileRender.php",
    "ITRound\\Vbchbbonus\\CvbchBonusPayment" => "lib/CvbchBonusPayment.php",
    "ITRound\\Vbchbbonus\\LinkmailTable" => "lib/linkmail.php",
    "ITRound\\Vbchbbonus\\CVbchBonusaccountsTable" => "lib/CVbchBonusaccountsTable.php",
    "ITRound\\Vbchbbonus\\MoneybackTable" => "lib/CMoneyBack.php",
    'ITround\\Vbchbbonus\\CVbchAffiliateTable' => 'lib/CVbchAffiliate.php',
    "ITRound\\Vbchbbonus\\BonusAccountsWidget" => "lib/widget/BonusAccountsWidget.php",
    "ITRound\\Vbchbbonus\\TextWidget" => "lib/widget/TextWidget.php",
    "ITRound\\Vbchbbonus\\HtmleditorWidget" => "lib/widget/HtmlEditorWidget.php",
    "ITRound\\Vbchbbonus\\CheckboxWidget" => "lib/widget/CheckboxWidget.php",
    "ITRound\\Vbchbbonus\\LabelWidget" => "lib/widget/LabelWidget.php",
    "ITRound\\Vbchbbonus\\SiteWidget" => "lib/widget/SiteWidget.php",
    "ITRound\\Vbchbbonus\\HeaderWidget" => "lib/widget/HeaderWidget.php",
    "ITRound\\Vbchbbonus\\NoteWidget" => "lib/widget/NoteWidget.php",
    "ITRound\\Vbchbbonus\\UsergroupWidget" => "lib/widget/UsergroupWidget.php",
    "ITRound\\Vbchbbonus\\DelayWidget" => "lib/widget/DelayWidget.php",
    "ITRound\\Vbchbbonus\\TimelifeWidget" => "lib/widget/TimelifeWidget.php",
    "ITRound\\Vbchbbonus\\RoundWidget" => "lib/widget/RoundWidget.php",
    "ITRound\\Vbchbbonus\\TextboxWidget" => "lib/widget/TextboxWidget.php",
    "ITRound\\Vbchbbonus\\ReviewsourceWidget" => "lib/widget/ReviewsourceWidget.php",
    "ITRound\\Vbchbbonus\\SocialsourceWidget" => "lib/widget/SocialsourceWidget.php",
    "ITRound\\Vbchbbonus\\ModulesourceWidget" => "lib/widget/ModulesourceWidget.php",
    "ITRound\\Vbchbbonus\\SubscribeWidget" => "lib/widget/SubscribeWidget.php",
    "ITRound\\Vbchbbonus\\UserfieldsWidget" => "lib/widget/UserfieldsWidget.php",
    "ITRound\\Vbchbbonus\\BonuscheckWidget" => "lib/widget/BonuscheckWidget.php",
    "ITRound\\Vbchbbonus\\PersontypeWidget" => "lib/widget/PersontypeWidget.php",
    "ITRound\\Vbchbbonus\\DeliveryWidget" => "lib/widget/DeliveryWidget.php",
    "ITRound\\Vbchbbonus\\PaymentWidget" => "lib/widget/PaymentWidget.php",
    "ITRound\\Vbchbbonus\\PricefilterWidget" => "lib/widget/PricefilterWidget.php",
    "ITRound\\Vbchbbonus\\IbpropertyWidget" => "lib/widget/IbpropertyWidget.php",
    "ITRound\\Vbchbbonus\\ComboboxWidget" => "lib/widget/ComboboxWidget.php",
    "ITRound\\Vbchbbonus\\BonusNameWidget" => "lib/widget/BonusNameWidget.php",
    "ITRound\\Vbchbbonus\\ActionWidget" => "lib/widget/ActionWidget.php",
    "ITRound\\Vbchbbonus\\BonusInnerWidget" => "lib/widget/BonusInnerWidget.php",
    "ITRound\\Vbchbbonus\\MailTemplateWidget" => "lib/widget/MailTemplateWidget.php",
    "ITRound\\Vbchbbonus\\AgentSetupWidget" => "lib/widget/AgentSetupWidget.php",
    "ITRound\\Vbchbbonus\\EventsSetupWidget" => "lib/widget/EventsSetupWidget.php",
    "ITRound\\Vbchbbonus\\SocialSetupWidget" => "lib/widget/SocialSetupWidget.php",
    "ITRound\\Vbchbbonus\\BonusPayWidget" => "lib/widget/BonusPayWidget.php",
    "ITRound\\Vbchbbonus\\DateFormatWidget" => "lib/widget/DateFormatWidget.php",
    "ITRound\\Vbchbbonus\\BigFilterWidget" => "lib/widget/BigFilterWidget.php",
    "ITRound\\Vbchbbonus\\OrderPeriodWidget" => "lib/widget/OrderPeriodWidget.php",
    "ITRound\\Vbchbbonus\\DiscountWidget" => "lib/widget/DiscountWidget.php",
    "ITRound\\Vbchbbonus\\PorogWidget" => "lib/widget/PorogWidget.php",
    "ITRound\\Vbchbbonus\\ReferalWidget" => "lib/widget/ReferalWidget.php",
    "ITRound\\Vbchbbonus\\MaxPayPropWidget" => "lib/widget/MaxPayPropWidget.php",
    "ITRound\\Vbchbbonus\\OrderPropWidget" => "lib/widget/OrderPropWidget.php",
    "ITRound\\Vbchbbonus\\EventSendEMailWidget" => "lib/widget/EventSendEMailWidget.php",
    "ITRound\\Vbchbbonus\\DatetimeWidget" => "lib/widget/DateTimeWidget.php",
    'ITRound\\Vbchbbonus\\CITRBBFilterCatalogCondTree' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrl' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlBasketProductFields' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlBasketProductProps' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlCatalogSettings' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlComplex' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlGroup' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlIBlockFields' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterCondCtrlIBlockProps' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterGlobalCondCtrl' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterGlobalCondCtrlAtoms' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterGlobalCondCtrlComplex' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterGlobalCondCtrlGroup' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CITRBBFilterGlobalCondTree' => 'lib/CVbchbbfilter.php',
    'ITRound\\Vbchbbonus\\CBonusRestAPI' => 'lib/CBonusExport.php',
    'ITRound\\Vbchbbonus\\BonusCardTable' => 'lib/CVbchBonusCard.php',
    'ITRound\\Vbchbbonus\\CouponTable' => 'lib/coupone.php',
    'ITRound\\Vbchbbonus\\BonucCouponTable' => 'lib/couponstatistic.php',
    'ITRound\\Vbchbbonus\\ProviderBonusPay' => 'lib/ProviderBonusPay.php',
    'ITRound\\Vbchbbonus\\BonusAdminOrder' => 'lib/CustomAdminOrder.php',
    'ITRound\\Vbchbbonus\\EditAdminOrderBonusClass' => 'lib/CustomAdminOrder.php',
    'ITRound\\Vbchbbonus\\ITROrderAdminHeader' => 'lib/CustomAdminOrder.php',
);
Loader::registerAutoLoadClasses($module_id, $arClassesList);
if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/sale_payment/innerbonus")) {
   CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/vbcherepanov.bonus/install/local", $_SERVER["DOCUMENT_ROOT"] . "/", true, true);
}

class CVbchbbEvents {

   protected static $OrderCreated = 0;
   protected static $OrderProcess = 0;
   protected static $OrderComplete = 0;

   public static function disableHandler($param) {
      self::${$param}--;
   }

   public static function enableHandler($param) {
      self::${$param}++;
   }

   public static function isEnabledHandler($param) {
      return (self::${$param} >= 0);
   }

   public static function OnBeforeEventAdd(&$event, &$lid, &$arFields) {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $l = $BBCORE->GetOptions($lid, 'NEW_USER_EMAIL');
      $l = $l['OPTION'];
      if ($l != '' & $event == $l) {
         $k = \ITRound\Vbchbbonus\CVbchRefTable::getList(
                         array(
                             'select' => array('USERID', 'REFERER'),
                             'filter' => array('ACTIVE' => "Y", 'LID' => $lid, 'USERID' => $arFields['USER_ID']),
                         )
                 )->fetch();
         $arFields['REFCODE'] = $k['REFERER'];
      }
      unset($l, $k, $BBCORE);
   }

   public static function OnBeforePrologHandler() {
      if (strlen($_REQUEST['mailbonus']) > 0) {
         global $USER;
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $hash = $GLOBALS['DB']->ForSQL(substr($_REQUEST['mailbonus'], 0, 32));
         $UID = intval(substr($_REQUEST['mailbonus'], 32));
         if ($UID > 0 && $l = \ITRound\Vbchbbonus\LinkmailTable::getList(array(
                     'filter' => array('HASH' => $hash, 'USER_ID' => $UID)
                 ))->fetch()) {
            $res = \ITRound\Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                        'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $BBCORE->SITE_ID),
                    ))->fetchAll();
            if ($BBCORE->CheckArray($res)) {
               foreach ($res as $prof) {
                  $check = ($prof['ISADMIN'] == 'Y');
                  $check = ($check) ? $USER->isAdmin() : $check;
                  if ($check) {
                     $BBCORE->AddBonus(array('bonus' => $l['BONUS'],
                         'ACTIVE' => 'Y',
                         'ACTIVE_FROM' => '',
                         'ACTIVE_TO' => '',
                         'CURRENCY' => ''),
                             array('SITE_ID' => $BBCORE->SITE_ID,
                                 'USER_ID' => $l['USER_ID'], 'IDUNITS' => 'EDIT_ACCOUNT' . $l['USER_ID'] . '_' . $l['BONUS'] . '_' . time()), $prof, true);
                     \ITRound\Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
                  }
               }
            }
            \ITRound\Vbchbbonus\LinkmailTable::delete($l['ID']);
         }
         LocalRedirect($GLOBALS['APPLICATION']->GetCurPageParam('', array('mailbonus')));
      }
   }

   static function OnRegUser($arFields) {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      if (!array_key_exists('ID', $arFields)) {
         if (array_key_exists('RESULT_MESSAGE', $arFields) && $arFields['RESULT_MESSAGE']['TYPE'] == 'OK') {
            $arFields['ID'] = $arFields['RESULT_MESSAGE']['ID'];
         }
      }
      $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
      $arFields['USERID'] = $arFields['ID'] ? $arFields['ID'] : $arFields['USER_ID'];
      if ($arFields['USERID'] == '') {
         if (array_key_exists("RESULT_MESSAGE", $arFields) && $arFields['RESULT_MESSAGE']['TYPE'] == 'OK') {
            $arFields['USERID'] = $arFields['RESULT_MESSAGE']['ID'];
         }
      }

      if ($BBCORE->CheckSiteOn() && $arFields['USERID'] != '') {
         $option = $BBCORE->GetAllOptions();
         $f = \ITRound\Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                     'filter' => array('LID' => $BBCORE->SITE_ID),
                     'select' => array('ID', 'SETTINGS'),
         ));
         while ($g = $f->fetch()) {
            $Flds = [];
            Application::getConnection()->startTransaction();
            $g['SETTINGS'] = $BBCORE->CheckSerialize($g['SETTINGS']);
            if ($g['SETTINGS']['SUFIX'] == 'NAME') {
               $cur = $g['SETTINGS']['NAME'][1];
            } elseif ($g['SETTINGS']['SUFIX'] == 'CURRENCY') {
               $cur = $g['SETTINGS']['CURRENCY'];
            }
            $Flds = array(
                'USER_ID' => $arFields['ID'],
                'CURRENT_BUDGET' => 0,
                'CURRENCY' => $cur,
                'BONUSACCOUNTSID' => $g['ID']
            );
            $res = \ITRound\Vbchbbonus\AccountTable::add($Flds);
            if ($res->isSuccess()) {
               Application::getConnection()->commitTransaction();
            } else {
               Application::getConnection()->rollbackTransaction();
            }
         }
         if (!array_key_exists('PERSONAL_PHONE', $arFields) || $arFields['PERSONAL_PHONE'] == '') {
            $us = \Bitrix\Main\UserTable::getList(
                            [
                                'filter' => ['=ID' => intval($arFields['USERID'])],
                                'select' => ['PERSONAL_PHONE']
                            ]
                    )->fetch();
            if ($us)
               $arFields['PERSONAL_PHONE'] = $us['PERSONAL_PHONE'];
            unset($us);
         }
         $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($arFields['USERID']);
         if ($option['AFFILIATE_AUTO_GENERATE_COUPON'] == 'Y') {
            self::AffiliateCreate($arFields, $BBCORE, $option);
         }
         if ($option['BONUSCARD_REG_CREATE'] == 'Y') {
            self::CreateBonusCard($arFields, $BBCORE, $option);
         }
         if ($option['REFACTIVE'] == 'Y') {
            $REF_BONUS = trim(\ITRound\Vbchbbonus\Vbchreferal::GetCookie("REFEREFBONUS"));
            $referal = [];
            if (array_key_exists("REFERER", $_POST) && !empty($_POST['REFERER'])) {
               $referal['REFERER'] = htmlspecialchars(intval($_POST['REFERER']));
            }
            if (array_key_exists("REF_FROM", $arFields) && !empty($arFields['REF_FROM'])) {
               $ures = ITRound\Vbchbbonus\CVbchRefTable::getList(array(
                           'filter' => array('ACTIVE' => 'Y', 'LID' => $BBCORE->SITE_ID, 'REFERER' => $arFields['REF_FROM'])
                       ))->fetch();
               if (sizeof($ures) > 0) {
                  $referal['REFERER'] = $ures['USERID'];
               }
            }

            if ($REF_BONUS != '') {
               $ures = ITRound\Vbchbbonus\CVbchRefTable::getList(array(
                           'filter' => array('ACTIVE' => 'Y', 'LID' => $BBCORE->SITE_ID, 'COOKIE' => $REF_BONUS)
                       ))->fetch();
               if (sizeof($ures) > 0) {
                  $referal['REFERER'] = $ures['REFFROM'];
               }
            }
            $referal['REFKA'] = $arFields['USERID'];
            $res = $BBCORE->GetLineFromUserID($arFields['USERID'], $BBCORE->SITE_ID);
            if ($res['REF'] && $res['REF'] != $arFields['USERID']) {
               $referal['USERID'] = $res['REF'];
               $referal['IDUNITS'] = array('REGREF_' . $arFields['USERID']);
               $referal['GROUP_ID'] = $BBCORE->GetUserGroupByUser($res['REF']);
               $arFields['REFERALS'] = $referal;
            }
            $arFields['REFERALS'] = $referal;
            \ITRound\Vbchbbonus\Vbchreferal::AddRef($arFields);

            $arFields['REFKA'] = $arFields['USERID'];
            $res = $BBCORE->GetLineFromUserID($arFields['USERID'], $BBCORE->SITE_ID);
            $arFields['USERID'] = $res['REF'];
            $arFields['USERGROUP'] = CUser::GetUserGroup($res['REF']);
            unset($res);
            $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
         }
         unset($BBCORE);
      }
   }

   static function CreateBonusCard($arFields, $BBCORE, $option) {
      $bonuscard_create = $option['BONUSCARD_REG_CREATE'] == 'Y';
      $bonuscard_phone = $option['BONUCARD_PHONE'] == 'Y';
      $bonuscard_deactive = $option['BONUSCARD_DEACTIVE'] == 'Y';
      $bonuscard_acc = $BBCORE->CheckSerialize($option['BONUSCARD_ACCOUNTS']);
      $startbonus = $option['BONUSCARD_STARTBONUS'];
      if ($bonuscard_create) {
         $num = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
         if ($bonuscard_phone && strlen($arFields['PERSONAL_PHONE']) > 0)
            $num = str_replace(['+', '-', '_', '(', ')', ' ', '  '], "", $arFields['PERSONAL_PHONE']);
         $fields = [
             'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
             'LID' => $BBCORE->SITE_ID,
             'ACTIVE' => $bonuscard_deactive ? 'N' : 'Y',
             'USERID' => $arFields['USERID'],
             'DEFAULTBONUS' => $startbonus,
             'BONUSACCOUNTS' => $bonuscard_acc['BONUSINNER'],
             'NUM' => $num,
         ];
         \Bitrix\Main\Application::getConnection()->startTransaction();
         if (!\ITRound\Vbchbbonus\BonusCardTable::add($fields))
            \Bitrix\Main\Application::getConnection()->rollbackTransaction();

         \Bitrix\Main\Application::getConnection()->commitTransaction();
      }
   }

   static function AffiliateCreate($arFields, $BBCORE, $option) {
      $affil_create = $option["AFFILIATE_AUTO_GENERATE_AFF"] == 'Y';
      $aff_coupon_create = $option["AFFILIATE_AUTO_GENERATE_COUPON"] == 'Y';
      $coupon_id = $BBCORE->CheckSerialize($option["AFFILIATE_AUTO_GENERATE_COUPON_DISC"]);
      $coupon_phone = $option['AFFILIATE_COUPON_PHONE'] == 'Y';
      $coupon = false;
      if ($coupon_id['ACTIVE'] == 'Y') {
         $coupon = $coupon_id['DISCOUNT'];
      }
      $aff_user_group = $BBCORE->CheckSerialize($option["AFFILIATE_AUTO_GENERATE_AFF_GROUP"]);
      if ($affil_create) {
         $check = false;
         if (array_key_exists("GROUP_ID", $arFields) && sizeof($arFields['GROUP_ID']) > 0) {
            if (sizeof($aff_user_group) > 0) {
               $check = sizeof(array_intersect($arFields['GROUP_ID'], $aff_user_group)) > 0;
            }
         }
         $rs = 0;
         $rs = \ITRound\Vbchbbonus\CVbchAffiliateTable::getList(
                         [
                             'filter' => ['=USERID' => intval($arFields['USERID'])],
                         ]
                 )->getSelectedRowsCount();
         if ($check && $rs == 0) {

            $cop = '';
            if ($aff_coupon_create && $coupon) {
               \Bitrix\Main\Loader::includeModule("sale");
               $cop = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
               if ($coupon_phone && strlen($arFields['PERSONAL_PHONE']) > 0)
                  $cop = str_replace(['+', '-', '_', '(', ')', ' ', '  '], "", $arFields['PERSONAL_PHONE']);

               \Bitrix\Sale\Internals\DiscountCouponTable::add(array(
                   'DISCOUNT_ID' => $coupon,
                   'COUPON' => $cop,
                   'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_MULTI_ORDER,
                   'MAX_USE' => 0,
                   'USER_ID' => 0,
                   'DESCRIPTION' => Loc::getMessage('VBCH_AFF_COUPON_DESC') . $arFields['USERID'],
               ));
            }

            $arAff = array(
                'LID' => $BBCORE->SITE_ID,
                'NAME' => Loc::getMessage('VBCH_AFF_AD_NAME') . $arFields['USERID'],
                'ACTIVE' => 'Y',
                "BONUS" => 0,
                "USERID" => intval($arFields['USERID']),
                "ACTIVE_FROM" => '',
                "ACTIVE_TO" => '',
                "PROMOCODE" => $cop,
                "DOMAINE" => '',
                "URL" => '',
                "COMMISIA" => '',
                "COMMISIAPROMO" => '',
                'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
            );
            \Bitrix\Main\Application::getConnection()->startTransaction();
            if (!\ITRound\Vbchbbonus\CVbchAffiliateTable::add($arAff))
               \Bitrix\Main\Application::getConnection()->rollbackTransaction();

            \Bitrix\Main\Application::getConnection()->commitTransaction();
         }
      }
   }

   //SUCCESS OK
   static function Checkphp($ver = '') {
      $tmp = phpversion();
      unset($tmp);
   }

   static function OnOrderNewSendEmail($ID, &$eventName, &$arFields) {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      if ($BBCORE->CheckSiteOn()) {
         $option = $BBCORE->GetOptions($BBCORE->SITE_ID, 'BONUSINORDERMAIL');
         $option1 = $BBCORE->GetOptions($BBCORE->SITE_ID, 'BONUSNAME');
         if ($option['OPTION'] == 'Y') {
            $arFields['EVENT_NAME'] = $eventName;
            $arFieldsBon = $BBCORE->GetArrayForProfile(0, array(), 1, false, $ID);
            $arFieldsBon['NONE'] = false;
            $profiles = $BBCORE->FilterProfiles($BBCORE->GetSaleProfiles());
            $BBCORE->DescriptionBonus = array();
            if ($BBCORE->CheckArray($profiles)) {
               foreach ($profiles as $prof) {
                  $Filter = call_user_func_array(array($BBCORE->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($BBCORE->FUNC_GETRULES[$prof['TYPE']], $prof['ID'], $BBCORE->CheckSerialize($prof['FILTER']), $arFieldsBon));
                  if ($Filter) {
                     $bonus = call_user_func_array(array($BBCORE->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($BBCORE->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFieldsBon));
                     $bonus = $BBCORE->BonusParams($bonus, $BBCORE->CheckSerialize($prof['BONUSCONFIG']));
                     $bns[$prof['ID']] = $bonus['bonus'];
                     $BBCORE->DescriptionBonus[] = array($prof['ID'], $prof['NAME'], $BBCORE->ReturnCurrency($bonus['bonus']));
                  }
               }
            }
            if ($BBCORE->CheckArray($bns)) {
               $bonus = $BBCORE->GetRangeBonus($bns);
            }
            $arFields['PRICE'] = CurrencyFormat(intval(str_replace(" ", "", $arFields['PRICE'])) - ($_POST['BONUS_CNT'] + $_POST['ACCOUNT_CNT']), \CCurrency::GetBaseCurrency());
            $arFields['BONUSFORORDER'] = $bonus;
            $arFields['BONUSPAY'] = $BBCORE->declOfNum($_POST['BONUS_CNT'] + $_POST['ACCOUNT_CNT'], ($option1['OPTION']['SUFIX'] == 'NAME' ? $option1['OPTION']['NAME'] : array("", "", "")), $BBCORE->ModuleCurrency());
         }
      }
   }

   static function BonusActive() {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->BonusActive();
      unset($BBCORE);
      return "CVbchbbEvents::BonusActive();";
   }

   static public function includeModule() {
      switch (Loader::includeSharewareModule('vbcherepanov.bonus')) {
         case MODULE_NOT_FOUND:
            throw new SystemException(Loc::getMessage('ITROUND_VBCHBBBONUS_MODULE_NOT_FOUND', array(
                        '#MODULE_ID#' => 'vbcherepanov.bonus'
            )));

         case MODULE_DEMO_EXPIRED:
            throw new SystemException(Loc::getMessage('ITROUND_VBCHBBBONUS_MODULE_DEMO_EPIRED'));
            break;
      }
   }

   static function BonusLive() {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->BonusLive();
      unset($BBCORE);
      return "CVbchbbEvents::BonusLive();";
   }

   static function BonusStatistic() {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->BonusStatistic();
      unset($BBCORE);
      return "CVbchbbEvents::BonusStatistic();";
   }

   //SUCCESS OK
   static function BonusBirthday() {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->BonusBirthday();
      unset($BBCORE);
      return "CVbchbbEvents::BonusBirthday();";
   }

   //SUCCESS OK
   static function OnBeforeUserUpdate(&$arFields) {
      $arFields['USERID'] = $arFields['ID'];
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
      if ($BBCORE->CheckSiteOn()) {
         if (array_key_exists('GROUP_ID', $arFields)) {
            if (sizeof($arFields['GROUP_ID']) > 0) {
               if (is_array($arFields['GROUP_ID'][0])) {
                  $l = [];
                  foreach ($arFields['GROUP_ID'] as $lg) {
                     $l[] = $lg['GROUP_ID'];
                  }
                  $arFields['GROUP_ID'] = $l;
                  unset($l);
               }
            } else {
               $arFields['GROUP_ID'][] = 2;
            }
         } else {
            $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($arFields['ID']);
         }
         $option = $BBCORE->GetAllOptions();
         $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);

         $BBCORE->RUNBONUS(__CLASS__ . "::OnRegUser", $arFields);

         if ($option['AFFILIATE_AUTO_GENERATE_COUPON'] == 'Y') {
            self::AffiliateCreate($arFields, $BBCORE, $option);
         }
         unset($BBCORE);
      }
   }

   //SUCCESS OK
   static function OnBeforeSubscriptionUpdate(&$arFields) {
      if (array_key_exists("USER_ID", $arFields) && $arFields['USER_ID'] !== '') {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['TYPE'] = 'SUBSCRIBE_';
         $arFields['USERID'] = $arFields['USER_ID'];
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($arFields['USER_ID']);
         $arFields['ID'] .= "_" . implode("_", $arFields['RUB_ID']);
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
         if ($arFields['ACTIVE'] == 'N' || $arFields['CONFIRMED'] == 'N') {
            if ($BBCORE->CheckSiteOn()) {
               $BBCORE->DeleteBonus(__CLASS__ . "::" . __FUNCTION__, array('IDUNITS' => 'SUBSCRIBE_' . $arFields['ID'], 'TYPE' => 'SUBSCRIBE'));
               unset($BBCORE);
            }
         } elseif ($arFields['ACTIVE'] == 'Y' && $arFields['CONFIRMED'] == 'Y') {
            if ($BBCORE->CheckSiteOn()) {
               $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
               unset($BBCORE);
            }
         }
      } elseif (!array_key_exists("RUB_ID", $arFields)) {
         $arFields1 = CSubscription::GetByID($arFields['ID'])->Fetch();
         $arFields1['ACTIVE'] = $arFields['ACTIVE'] ? $arFields['ACTIVE'] : 'Y';
         $arFields1['CONFIRMED'] = 'Y';
         $arFields1['RUB_ID'] = CSubscription::GetRubricArray($arFields1['ID']);
         self::OnBeforeSubscriptionUpdate($arFields1);
      }
   }

   //SUCCESS OK
   static function OnBeforeSubscriptionAdd(&$arFields) { // for adminpage add subscriber
      if ($arFields['USER_ID'] !== '') {
         if ($arFields['ACTIVE'] == 'Y' && $arFields['CONFIRMED'] == 'Y') {
            if (!isset($arFields['ID'])) {
               $res = Bitrix\Main\Application::getConnection()
                       ->query("SHOW TABLE STATUS like 'b_subscription';")
                       ->fetch();
               $arFields['ID'] = $res['Auto_increment'] + 1;
            }
            $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
            $arFields['TYPE'] = 'SUBSCRIBE_';
            $arFields['USERID'] = $arFields['USER_ID'];
            $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($arFields['USER_ID']);
            $arFields['ID'] .= "_" . implode("_", $arFields['RUB_ID']);
            $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
            if ($BBCORE->CheckSiteOn()) {
               $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
               unset($BBCORE);
            }
         }
      }
   }

   //SUCCESS OK
   static function MailingSubscriptionOnAfterAdd(\Bitrix\Main\Entity\Event $event) {
      global $USER;
      $fields_s = $event->getParameters();
      if ($USER->IsAuthorized()) {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['ACTIVE'] = $arFields['CONFIRMED'] = 'Y';
         $arFields['TYPE'] = 'SENDER_';
         $arFields['USERID'] = $USER->GetID();
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
         $fields_s = $event->getParameters();
         $arFields['ID'] = $fields_s['fields']['CONTACT_ID'];
         $arFields['RUB_ID'] = array($fields_s['fields']['MAILING_ID']);
         $arFields['ID'] .= "_" . implode("_", $arFields['RUB_ID']);
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
            unset($BBCORE);
         }
      }
   }

   //SUCCESS OK
   static function MailingSubscriptionOnAfterDelete(\Bitrix\Main\Entity\Event $event) {
      $arFields['TYPE'] = 'SENDER_';
      global $USER;
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $arFields['ACTIVE'] = $arFields['CONFIRMED'] = 'Y';
      $arFields['TYPE'] = 'SENDER_';
      $arFields['USERID'] = $USER->GetID();
      $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
      $fields_s = $event->getParameters();
      $arFields['ID'] = $fields_s['id']['CONTACT_ID'];
      $arFields['RUB_ID'] = array($fields_s['id']['MAILING_ID']);
      $arFields['ID'] .= "_" . implode("_", $arFields['RUB_ID']);
      $arFields['MODULES'] = 'SENDER_MailingSubscriptionOnAfterDelete';
      $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
      if ($BBCORE->CheckSiteOn()) {
         $BBCORE->DeleteBonus(__CLASS__ . "::" . __FUNCTION__, array('IDUNITS' => 'SUBSCRIBE_' . $arFields['ID'], 'TYPE' => 'SUBSCRIBE'));
         unset($BBCORE);
      }
   }

   //SUCCESS OK
   static function OnAfterRecipientUnsub(\Bitrix\Main\Event $event) {
      $arFields['TYPE'] = 'SENDER_';
      global $USER;
      $fields_s = $event->getParameters();
      $fields_s = current($fields_s);
      $contactId = \Bitrix\Sender\ContactTable::getList(array(
                  'filter' => array('EMAIL' => $fields_s['EMAIL']),
                  'select' => array("ID", "USER_ID"),
              ))->fetch();
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $arFields['ACTIVE'] = $arFields['CONFIRMED'] = 'Y';
      $arFields['TYPE'] = 'SENDER_';
      if ($USER->IsAuthorized()) {
         $arFields['USERID'] = $USER->GetID();
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
      } elseif ($contactId['USER_ID']) {
         $arFields['USERID'] = $contactId['USER_ID'];
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($contactId['USER_ID']);
      }
      $arFields['ID'] = $contactId['ID'];
      $arFields['RUB_ID'] = $fields_s['MAILING_ID']; //$BBCORE->SenderGetMalingByEmail($fields_s['EMAIL']);
      $arFields['ID'] .= "_" . (is_array($arFields['RUB_ID']) ? implode("_", $arFields['RUB_ID']) : $arFields['RUB_ID']);
      $arFields['MODULES'] = 'SENDER_OnAfterRecipientUnsub';
      $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
      if ($BBCORE->CheckSiteOn()) {
         $BBCORE->DeleteBonus(__CLASS__ . "::" . __FUNCTION__, array('IDUNITS' => 'SUBSCRIBE_' . $arFields['ID'], 'TYPE' => 'SUBSCRIBE'));
         unset($BBCORE);
      }
   }

   //SUCCESS OK
   static function ContactOnAfterUpdate(\Bitrix\Main\Entity\Event $event) {
      $arFields['TYPE'] = 'SENDER_';
      global $USER;
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $arFields['ACTIVE'] = $arFields['CONFIRMED'] = 'Y';
      $arFields['TYPE'] = 'SENDER_';
      $arFields['USERID'] = $USER->GetID();
      $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
      $fields_s = $event->getParameters();
      $arFields['ID'] = $fields_s['id']['ID'];
      if (array_key_exists('EMAIL', $fields_s['fields']))
         $arFields['RUB_ID'] = $BBCORE->SenderGetMalingByEmail($fields_s['fields']['EMAIL']);
      if (is_array($arFields['RUB_ID']))
         $arFields['ID'] .= "_" . implode("_", $arFields['RUB_ID']);

      $arFields['MODULES'] = 'SENDER_ContactOnAfterUpdate';
      if ($arFields['ACTIVE'] == 'N' || $arFields['CONFIRMED'] == 'N') {
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->DeleteBonus(__CLASS__ . "::" . __FUNCTION__, array('IDUNITS' => 'SUBSCRIBE_' . $arFields['ID'], 'TYPE' => 'SUBSCRIBE'));
            unset($BBCORE);
         }
      } elseif ($arFields['ACTIVE'] == 'Y' && $arFields['CONFIRMED'] == 'Y') {
         self::ContactOnAfterAdd($event);
      }
   }

   static function ContactOnAfterAdd(\Bitrix\Main\Entity\Event $event) {
      global $USER;
      if ($USER->IsAuthorized()) {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['ACTIVE'] = $arFields['CONFIRMED'] = 'Y';
         $arFields['TYPE'] = 'SENDER_';
         $arFields['USERID'] = $USER->GetID();
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
         $fields_s = $event->getParameters();
         $arFields['ID'] = $fields_s['id'];
         if (array_key_exists('EMAIL', $fields_s['fields']))
            $arFields['RUB_ID'] = $BBCORE->SenderGetMalingByEmail($fields_s['fields']['EMAIL']);
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : $arFields['LID'];
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
            unset($BBCORE);
         }
      }
   }

   //SUCCESS OK
   static function ContactOnAfterDelete(\Bitrix\Main\Entity\Event $event) {
      $arFields['TYPE'] = 'SENDER_';
      global $USER;
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $arFields['TYPE'] = 'SENDER_';
      $arFields['USERID'] = $USER->GetID();
      $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
      $fields_s = $event->getParameters();
      $arFields['ID'] = $fields_s['id']['ID'];
      if (array_key_exists('EMAIL', $fields_s['fields']))
         $arFields['RUB_ID'] = $BBCORE->SenderGetMalingByEmail($fields_s['fields']['EMAIL']);

      $arFields['MODULES'] = 'SENDER_ContactOnAfterDelete';

      $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
      if ($BBCORE->CheckSiteOn()) {
         $BBCORE->DeleteBonus(__CLASS__ . "::" . __FUNCTION__, array('IDUNITS' => 'SUBSCRIBE_' . $arFields['ID'], 'TYPE' => 'SUBSCRIBE'));
         unset($BBCORE);
      }
   }

   //SUCCESS OK
   static function OnAfterHLAdd($params, $TEXT = '') {
      global $USER;
      $arFields['TYPE'] = 'HL_';
      if ($USER->IsAuthorized()) {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['USERID'] = $USER->GetID();
         $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($USER->GetID());
         $arFields['TEXT'] = $TEXT;
         $arFields['HL_ID'] = $params['HL_ID'];
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->RUNBONUS(__CLASS__ . "::OnAfterIBlockElementAdd", $arFields);
            unset($BBCORE);
         }
      }
   }

   //SUCCESS OK
   static function OnAfterIBlockElementAdd(&$arFields) {
      global $USER;
      $arFields['TYPE'] = 'IB_';
      if ($USER->IsAuthorized()) {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['USERID'] = $arFields['CREATED_BY'];
         $arFields['USERGROUP'] = $BBCORE->GetUserGroupByUser($arFields['USERID']);
         $arFields['TEXT'] = $arFields['PREVIEW_TEXT'] ? $arFields['PREVIEW_TEXT'] : $arFields['DETAIL_TEXT'];
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
            unset($BBCORE);
         }
      }
   }

   //SUCCESS OK
   static function onBeforeMessageAdd(&$arFields) {
      global $USER;
      $arFields['TYPE'] = 'FORUM_';
      if ($USER->IsAuthorized()) {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['USERID'] = $arFields['AUTHOR_ID'];
         $arFields['ACTIVE'] = $arFields['APPROVED'];
         $arFields['ID'] = $arFields['FORUM_ID'] . "_" . $arFields['TOPIC_ID'] . "_" . $arFields['USERID'];
         $arFields['USERGROUP'] = $BBCORE->GetUserGroupByUser($USER->GetID());
         $arFields['TEXT'] = $arFields['POST_MESSAGE'];
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
            unset($BBCORE);
         }
         unset($arFields['ID'], $arFields['ACTIVE']);
      }
   }

   static function onAfterMessageUpdate($ID, $arFields) {

      \Bitrix\Main\Loader::includeModule("forum");
      $arFields1 = CForumMessage::GetByID($ID);
      $arFields1['APPROVED'] = $arFields['APPROVED'];
      self::onBeforeMessageAdd($arFields1);
   }

   //SUCCESS OK
   static function OnBeforeCommentAdd(&$arFields) {
      global $USER;
      $arFields['TYPE'] = 'BLOG_';
      if ($USER->IsAuthorized()) {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         $arFields['USERID'] = $arFields['AUTHOR_ID'];
         $arFields['ID'] = $arFields['BLOG_ID'] . "_" . $arFields['POST_ID'] . "_" . $arFields['USERID'];
         $arFields['TEXT'] = $arFields['POST_TEXT'];
         if (array_key_exists('PUBLISH_STATUS', $arFields)) {
            $arFields['ACTIVE'] = ($arFields['PUBLISH_STATUS'] == 'P' ? 'Y' : 'N');
         } else
            $arFields['ACTIVE'] = 'Y';

         $arFields['USERGROUP'] = $BBCORE->GetUserGroupByUser($arFields['USERID']);
         $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
         if ($BBCORE->CheckSiteOn()) {
            $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
            unset($BBCORE);
         }
         unset($arFields['ID'], $arFields['USERID'], $arFields['GROUP_ID']);
      }
   }

   static function OnCommentUpdate($ID, $arFields) {
      \Bitrix\Main\Loader::includeModule("blog");
      $arFields1 = CBlogComment::GetByID($ID);
      $arFields1['PUBLISH_STATUS'] = $arFields['PUBLISH_STATUS'];
      self::OnBeforeCommentAdd($arFields1);
   }

   static function OnProlog() {
      \ITRound\Vbchbbonus\Vbchreferal::OnProlog();
   }

   //CANCEL ORDER
   static function OnSaleOrderCanceled($ENTITY, $func, $types) {
      $order = $ENTITY->getParameter('ENTITY');
      $arFields['ORDER_ID'] = $order->getField('ID');
      $arFields['VALUE'] = $order->getField('CANCELED');
      \Bitrix\Main\Diag\Debug::writeToFile(['CANCEL', $arFields], "", "/cancel.txt");
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $ID = $arFields['ORDER_ID'];
      if ($BBCORE->CheckSiteOn() && $arFields['VALUE'] == 'Y') {
         $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $arFields['ORDER_ID']);
         $arFields = array_merge($arFields, $tmp);
         $arFields['ID'] = $types . $ID;
         unset($tmp);
         $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID);
         \Bitrix\Main\Diag\Debug::writeToFile(['CANCEL1', $arFields], "", "/cancel.txt");
         $BBCORE->DeleteBonus(__CLASS__ . "::" . $func, $arFields);
         $BBCORE->ReturnBonus($arFields['ORDER_ID']);
      }
      unset($BBCORE);
   }

   static function CancelOrder($ENTITY) {
      self::OnSaleOrderCanceled($ENTITY, __FUNCTION__, "Cn_");
   }

   //STATUS
   function OnSaleStatusOrderChange($ENTITY, $func, $types) {
      $order = $ENTITY->getParameter('ENTITY');
      $ID = $order->getField('ID');

      $val = $ENTITY->getParameter('STATUS_ID');

      $arFields = array(
          'STATUS_ID' => $order->getField('STATUS_ID'),
          'ORDER_ID' => $ID,
          'VALUE' => $order->getField('STATUS_ID'),
          'ID' => $types . $ID,
          'NONE' => true,
          'CHANGESTATUS' => true
      );

      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      if ($BBCORE->CheckSiteOn()) {
         $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $arFields['ORDER_ID']);
         $arFields = array_merge($arFields, $tmp);
         unset($tmp);
         $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID);
         $BBCORE->RUNBONUS(__CLASS__ . "::" . $func, $arFields);
         unset($BBCORE);
      }
   }

   static function StatusOrder($ENTITY) {
      self::OnSaleStatusOrderChange($ENTITY, __FUNCTION__, "S_");
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $l = $BBCORE->GetOptions($BBCORE->SITE_ID, 'REFACTIVE');
      unset($BBCORE);
      $l = $l['OPTION'] == 'Y';
      if ($l)
         self::ReferalOrderRun($ENTITY, 'S');
   }

   static function StatusOrderEvent($ID, $val) {
      $ID = intval($ID);
      $val = trim($val);
      $arFields = array(
          'STATUS_ID' => $val,
          'ORDER_ID' => $ID,
          'VALUE' => $val,
          'ID' => "S_" . $ID,
          'NONE' => true,
          'CHANGESTATUS' => true
      );
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      if ($BBCORE->CheckSiteOn()) {
         $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $arFields['ORDER_ID']);
         $arFields = array_merge($arFields, $tmp);
         unset($tmp);
         $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID);
         $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
         unset($BBCORE);
      }
   }

   //ALL PAY Order

   static function OnSaleOrderPaid($ENTITY, $func, $types) {
      $order = $ENTITY->getParameter('ENTITY');
      $arFields = array(
          'PAY_ID' => $order->getField('ID'),
          'ORDER_ID' => $order->getField('ID'),
          'VALUE' => $order->getField('PAYED'),
          'ID' => $types . $order->getField('ID'),
          'NONE' => true,
      );
      $ID = $arFields['ORDER_ID'];
      if ($arFields['VALUE'] == 'Y') {
         $InnerPS[] = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
         $InnerPS[] = \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId();
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         if ($BBCORE->CheckSiteOn() && $arFields['ORDER_ID']) {
            $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $arFields['ORDER_ID']);
            $arFields = array_merge($arFields, $tmp);
            $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID);
            unset($tmp);
            $BBCORE->RUNBONUS(__CLASS__ . "::" . $func, $arFields);
            unset($BBCORE);
         }
      }
   }

   static function OnAfterIBlockElementUpdate(&$arFields) {
      $arFields['TYPE'] = 'IB_';
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      if (\Bitrix\Main\Loader::includeModule("iblock")) {
         $res = \Bitrix\Iblock\ElementTable::getById($arFields['ID'])->fetch();
      }
      if ($res) {
         $arFields['USERID'] = $res['CREATED_BY'];
         $arFields['PREVIEW_TEXT'] = $res['PREVIEW_TEXT'] ? $res['PREVIEW_TEXT'] : $res['DETAIL_TEXT'];
      } else
         return false;
      $arFields['GROUP_ID'] = $BBCORE->GetUserGroupByUser($arFields['USERID']);
      $arFields['TEXT'] = $arFields['PREVIEW_TEXT'];
      $BBCORE->SITE_ID = $arFields['SITE_ID'] ? $arFields['SITE_ID'] : ($arFields['LID'] ? $arFields['LID'] : $BBCORE->GetSiteID());
      if ($BBCORE->CheckSiteOn()) {
         $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
         unset($BBCORE);
      }
   }

   function OnSalePaymentSetField(\Bitrix\Main\Event $event) {
      $name = $event->getParameter('NAME');
      $value = $event->getParameter('VALUE');
      if ($name == 'PAID' && $value == 'Y') {
         $payment = $event->getParameter('ENTITY');
         $arFields = array(
             'PAY_ID' => $payment->getField('ID'),
             'ORDER_ID' => $payment->getField('ORDER_ID'),
             'VALUE' => $payment->getField('PAID'),
             'ID' => 'P_' . $payment->getField('ORDER_ID'),
             'NONE' => true,
         );
         $ID = $payment->getField('ORDER_ID');
         $func = 'PayerOrder';
         $InnerPS[] = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
         $InnerPS[] = \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId();
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         if ($BBCORE->CheckSiteOn() && $ID) {
            $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $ID);
            $arFields = array_merge($arFields, $tmp);
            $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID);
            unset($tmp);
            $BBCORE->RUNBONUS(__CLASS__ . "::" . $func, $arFields);
            unset($BBCORE);
         }
      }
   }

   static function PayerOrder($ENTITY) {
      self::OnSaleOrderPaid($ENTITY, __FUNCTION__, 'P_');
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $l = $BBCORE->GetOptions($BBCORE->SITE_ID, 'REFACTIVE');
      unset($BBCORE);
      $l = $l['OPTION'] == 'Y';
      if ($l)
         self::ReferalOrderRun($ENTITY, 'P');
   }

   static function OnSaleComponentOrderOneStepProcess(\Bitrix\Sale\Order $order, $arUserResult, \Bitrix\Main\HttpRequest $request, &$arParams, &$arResult) {
//        if (!self::isEnabledHandler('OrderProcess'))
//            return;
//        self::disableHandler('OrderProcess');
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->OnSaleComponentOrderOneStepProcess($order, $arResult, $arUserResult, $arParams, $request);
      unset($BBCORE);
//        self::isEnabledHandler('OrderProcess');
   }

   static function OnSaleComponentOrderOneStepComplete($ID, $arOrder, $arParams) {
//        if (!self::isEnabledHandler('OrderComplete'))
//            return;
//        self::disableHandler('OrderComplete');
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->OnSaleComponentOrderOneStepComplete($ID, $arOrder, $arParams);
      unset($BBCORE);
//        self::isEnabledHandler('OrderComplete');
   }

   static function RefreshPayedFromAccount(&$arResult) {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->SITE_ID = SITE_ID;
      $BBCORE->RefreshPayedFromAccount($arResult);
   }

   static function OnSaleComponentOrderCreated(\Bitrix\Sale\Order $order, &$arUserResult, \Bitrix\Main\HttpRequest $request, &$arParams, &$arResult) {
//        if (!self::isEnabledHandler('OrderCreated'))
//            return;
//        self::disableHandler('OrderCreated');
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $BBCORE->SITE_ID = SITE_ID;
      $BBCORE->OnSaleComponentOrderCreated($order, $arUserResult, $request, $arParams, $arResult);
      unset($BBCORE);
//        self::isEnabledHandler('OrderCreated');
   }

   static function ReferalOrderRun($ENTITY, $type) {
      $order = $ENTITY->getParameter('ENTITY');
      $ID = $order->getField('ID');
      $val = $ENTITY->getParameter('VALUE');
      $ball = 0;
      //refer
      if ($type == 'S')
         self::OnSaleStatusOrderChange1($ENTITY, 'OnSaleStatusOrderChange', "S_", $ball);
      elseif ($type == 'P')
         self::OnPayOrder1($ENTITY, 'PayerOrder', "P_", $ball);
   }

   static function OnPayOrder1($ENTITY, $func, $types, $ball) {
      $order = $ENTITY->getParameter('ENTITY');
      $arFields = array(
          'PAY_ID' => $order->getField('ID'),
          'ORDER_ID' => $order->getField('ID'),
          'VALUE' => $order->getField('PAYED'),
          'ID' => $types . $order->getField('ID'),
          'NONE' => true,
      );
      $ID = $arFields['ORDER_ID'];
      if ($arFields['VALUE'] == 'Y') {
         $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
         if ($BBCORE->CheckSiteOn()) {
            $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $arFields['ORDER_ID']);
            $arFields = array_merge($arFields, $tmp);
            $arFields['REFKA'] = $arFields['USERID'];
            $res = $BBCORE->GetLineFromUserID($arFields['USERID'], $BBCORE->SITE_ID);
            $arFields['USERID'] = $res['REF'];
            $arFields['USERGROUP'] = CUser::GetUserGroup($res['REF']);
            $arFields['BASKET'] = 1;
            unset($tmp);
            $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID,);
            $BBCORE->RUNBONUS('CVbchbbEvents::PayerOrder', $arFields);
            unset($BBCORE);
         }
      }
   }

   static function OnSaleStatusOrderChange1($ENTITY, $func, $types, $ball) {
      $order = $ENTITY->getParameter('ENTITY');
      $ID = $order->getField('ID');
      $val = $ENTITY->getParameter('VALUE');
      $arFields = array(
          'STATUS_ID' => $val,
          'ORDER_ID' => $ID,
          'VALUE' => $val,
          'ID' => $types . $ID,
          'NONE' => true,
          'CHANGESTATUS' => true
      );
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      if ($BBCORE->CheckSiteOn()) {
         $tmp = $BBCORE->GetArrayForProfile(0, array(), 1, false, $arFields['ORDER_ID']);
         $arFields = array_merge($arFields, $tmp);
         $arFields['REFKA'] = $arFields['USERID'];
         $res = $BBCORE->GetLineFromUserID($arFields['USERID'], $BBCORE->SITE_ID);
         $arFields['USERID'] = $res['REF'];
         $arFields['USERGROUP'] = CUser::GetUserGroup($res['REF']);
         $arFields['BASKET'] = 1;
         unset($tmp);
         $arFields['IDUNITS'] = array('P_' . $ID, 'S_' . $ID, 'F_S_' . $ID, 'C_S_' . $ID, 'CP_' . $ID,);
         $BBCORE->RUNBONUS('CVbchbbEvents::StatusOrder', $arFields);
         unset($BBCORE);
      }
   }

   static function OnPageStartRestApi() {
      $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
      $l = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'USE_RESTAPI', "", $BBCORE->SITE_ID);
      if ($l == 'Y')
         $l = new \ITRound\Vbchbbonus\CBonusRestAPI($BBCORE->SITE_ID);
      unset($BBCORE);
   }

   static function EndToEndIntegration() {
      if (!\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isAdminSection()) {
         $arIntegration = [];
         $arIntegration['SITE_ID'] = SITE_ID;
         $arIntegration['DETAIL']['ON'] = false;
         $arIntegration['CART']['ON'] = false;
         $arIntegration['SOA']['ON'] = false;
         $arIntegration['CARTBONUSPAY']['ON'] = false;
         $intdetail = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONDETAIL', "", SITE_ID);
         if ($intdetail == 'Y') {
            $arIntegration['DETAIL']['ON'] = ($intdetail == 'Y');
            $arIntegration['DETAIL']['PATH'] = '/bitrix/components/vbcherepanov/vbcherepanov.bonuselement/ajax.php';
            $arIntegration['DETAIL']['MAIN_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONDETAIL_MAIN_BLOCK', "", SITE_ID);
            $arIntegration['DETAIL']['OUT_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONDETAIL_OUT_BLOCK', "", SITE_ID);
            $arIntegration['DETAIL']['COMP_TEMPLATE'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONDETAIL_COMP_TEMPLATE', "", SITE_ID);
            $arIntegration['DETAIL']['LANG_BONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONDETAIL_LANG_BONUS', "", SITE_ID);
         }
         $intsoa = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER', "", SITE_ID);
         if ($intsoa == 'Y') {
            $arIntegration['SOA']['ON'] = ($intsoa == 'Y');
            $arIntegration['SOA']['MAIN_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER_MAIN_BLOCK', "", SITE_ID);
            $arIntegration['SOA']['OUT_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER_OUT_BLOCK', "", SITE_ID);
            $arIntegration['SOA']['LANG_BONUSPAY'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER_LANG_BONUSPAY', "", SITE_ID);
            $arIntegration['SOA']['LANG_BONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER_LANG_BONUS', "", SITE_ID);
            $arIntegration['SOA']['BONUSPAY_MAIN_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER_BONUSPAY_MAIN_BLOCK', "", SITE_ID);
            $arIntegration['SOA']['BONUSPAY_OUT_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDER_BONUSPAY_OUT_BLOCK', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_PATH_TO_HELP'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_PATH_TO_HELP', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_TEXT_IN_YOU_ACCOUNT'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_TEXT_IN_YOU_ACCOUNT', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_TXT_YOU_MUST_PAY'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_TXT_YOU_MUST_PAY', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_TXT_HOW_MUCH_BONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_TXT_HOW_MUCH_BONUS', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_TXT_BONUSPAY_CANCEL'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_TXT_BONUSPAY_CANCEL', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_TEXT_TO_HELP'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_TEXT_TO_HELP', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_TXT_BONUSPAY_OK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'ITR_BONUS_TXT_BONUSPAY_OK', "", SITE_ID);
            $arIntegration['SOA']['ITR_BONUS_NONE_PAYBONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERNOPAY', "", SITE_ID);
         }
         $intcart = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCARTON', "", SITE_ID);
         if ($intcart == 'Y') {
            $arIntegration['CART']['ON'] = ($intcart == 'Y');
            $arIntegration['CART']['PATH'] = '/bitrix/components/vbcherepanov/vbcherepanov.bonusfororder/ajax.php';
            $arIntegration['CART']['LANG_BONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCART_LANG_BONUS', "", SITE_ID);
            $arIntegration['CART']['MAIN_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCART_MAIN_BLOCK', "", SITE_ID);
            $arIntegration['CART']['OUT_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCART_OUT_BLOCK', "", SITE_ID);
            $arIntegration['CART']['BONUS_OFFER'] = ('Y' === \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCARTON_BONUS_OFFER', "", SITE_ID));
            $arIntegration['CART']['OFFER_LANG_BONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCART_OFFER_LANG_BONUS', "", SITE_ID);
         }
         $intcartbonuspay = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCARTBONUSPAYON', "", SITE_ID);
         if ($intcartbonuspay == 'Y') {
            $arIntegration['CARTBONUSPAY']['ON'] = ($intcartbonuspay == 'Y');
            $arIntegration['CARTBONUSPAY']['MAIN_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCARTBONPAY_MAIN_BLOCK', "", SITE_ID);
            $arIntegration['CARTBONUSPAY']['OUT_BLOCK'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCARTBONPAY_OUT_BLOCK', "", SITE_ID);
            $arIntegration['CARTBONUSPAY']['COMP_TEMPLATE'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONCARTBONPAY_COMP_TEMPLATE', "", SITE_ID);
            $arIntegration['CARTBONUSPAY']['LANG_BONUS'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONDETAIL_LANG_BONUS', "", SITE_ID);
         }
         $arIntegration['CHECK_SMS']['ON'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'CHECKSMSBONUSPAY', "", SITE_ID);
         $arIntegration['CHECK_SMS']['SMSTEXT'] = \Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERSMS', "", SITE_ID);
         if ($intdetail == 'Y' || $intsoa == 'Y' || $intcart == 'Y' || $intcartbonuspay == 'Y') {
            \Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/vbcherepanov.bonus/bonus_integration.js');
            $script = '
                <script type="text/javascript">
                    BX.ready(function(){
                        var ITRBonusIntgrtn = new ITRBonusIntegrate(' . CUtil::PhpToJSObject($arIntegration, false, true) . ');
                    });
                </script>
            ';
            \Bitrix\Main\Page\Asset::getInstance()->addString($script);
         }
      }
   }

   static function PrologAffiliate() {
      /*     if($_SERVER['HTTP_BX_AJAX']!='true'  && !defined('BX_CRONTAB')){
        global $USER;
        $k = $_SERVER['HTTP_REFERER'];
        $k = explode("/", $k);
        $k = array_filter($k);
        $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
        $BBCORE->SITE_ID = SITE_ID;
        if ($BBCORE->CheckSiteOn()) {
        $arFields = array(
        'REFERER' => $_SERVER['HTTP_REFERER'],
        'URL_FROM' => $k,
        'LID' => $BBCORE->SITE_ID,
        'USER_ID' => $USER->GetID(),

        );
        $BBCORE->RUNBONUS(__CLASS__ . "::" . __FUNCTION__, $arFields);
        unset($BBCORE);
        }
        } */
   }

   static function onManagerCouponAddHandler(Bitrix\Main\Event $event) {
      \Bitrix\Main\Loader::includeModule("sale");
      \Bitrix\Main\Loader::includeModule("catalog");
      $couponData = $event->getParameters();
      $couponModule = $couponData["MODULE_ID"];
      if ($couponModule === "catalog") {
         $arCoupon = CCatalogDiscountCoupon::GetByID($couponData["ID"]);
      } else if ($couponModule === "sale") {
         $dbCoupon = \Bitrix\Sale\Internals\DiscountCouponTable::GetList(
                         [
                             "select" => ["ID", "DISCOUNT_ID", "COUPON", "DESCRIPTION"],
                             "filter" => ['=COUPON' => $couponData["COUPON"]]
                         ]
         );
         if ($fetched = $dbCoupon->Fetch()) {
            $data = $fetched;
         }
      }
      if ($data) {
         if (sizeof($data) > 0) {
            global $USER;
            $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
            $BBCORE->SITE_ID = SITE_ID;
            if ($BBCORE->CheckSiteOn()) {
               $coupon = $data['COUPON'];
               $arFields = array(
                   'PROMOCODE' => $coupon,
                   'LID' => $BBCORE->SITE_ID,
                   'USER_ID' => $USER->GetID(),
               );
               if ($rs = \ITRound\Vbchbbonus\CVbchAffiliateTable::getList(['filter' => ['PROMOCODE' => $coupon]])->fetch()) {
                  $BBCORE->RUNBONUS('CVbchbbEvents::onManagerCouponAddHandler', $arFields);
                  if ($USER->IsAuthorized()) {
                     $once_coupon = $BBCORE->GetOptions($BBCORE->SITE_ID, "AFFILIATE_AUTO_ONES_COUPON");
                     if ($once_coupon['OPTION'] == 'Y') {
                        $find_user = \ITRound\Vbchbbonus\CVbchRefTable::getList(
                                        [
                                            'filter' => ['=USERID' => intval($USER->GetID()), '!REFFROM' => false]
                                        ]
                                )->getSelectedRowsCount();
                        if ($find_user > 0)
                           \Bitrix\Sale\DiscountCouponsManager::delete($coupon);
                     }
                  }
               }
            }
         }
         unset($BBCORE);
      }
   }

}

class CAcritBonusesMenu {

   public function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu) {
      global $APPLICATION;
      $APPLICATION->addHeadString('<style>
				.adm-main-menu-item.adm-acrit .adm-main-menu-item-icon{
					background:url("/bitrix/themes/.default/images/vbcherepanov.bonus/acrit.png") center center no-repeat;
				}
			</style>');
      $aMenu = array(
          "menu_id" => "acrit",
          "sort" => 150,
          "text" => GetMessage('ACRIT_MENU_TITLE'),
          "title" => GetMessage('ACRIT_MENU_TITLE'),
          "icon" => "clouds_menu_icon",
          "page_icon" => "clouds_page_icon",
          "items_id" => "global_menu_acrit",
          "items" => array()
      );
      $aGlobalMenu['global_menu_acrit'] = $aMenu;
   }

}
