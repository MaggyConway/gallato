<?php
use \Bitrix\Main\Localization\Loc;
$profiles = array(
    "AFFILIATE",
    Loc::getMessage('VBCHBB_TYPE_PROFILEAFFILIATE'),
    Loc::getMessage('VBCHBB_TYPE_PROFILEAFFILIATE_DSC'),
    array(
        'TAB1' => array(
            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB1_TITLE'),
            'ELEM' => array(
                'ACTIVE' => array(
                    'WIDGET' => new ITRound\Vbchbbonus\CheckboxWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE'),
                    'DEFAULT' => 'Y',
                    "REQUIRED" => false,
                    "TYPE" => "string",
                    "MULTIPLE" => false,
                ),
                'ACTIVE_FROM' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\DatetimeWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE_FROM'),
                    "DEFAULT" => "",
                    "TYPE" => "",
                ),
                'ACTIVE_TO' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\DatetimeWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE_TO'),
                    "DEFAULT" => "",
                    "TYPE" => "",
                ),
                'TIMESTAMP_X' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\LabelWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CHANGE'),
                    "DEFAULT" => "",
                    "TYPE" => "",
                ),
                'SITE' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\SiteWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SITE'),
                    "REQUIRED" => true,
                    "DEFAULT" => \ITRound\Vbchbbonus\Vbchbbcore::GetSiteID(),
                    "TYPE" => "string",
                    'SIZE' => 10,
                    'MULTIPLE' => false
                ),
                'NAME' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\TextWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROFILENAME'),
                    "DEFAULT" => "",
                    'REQUIRED' => true,
                    'PLACEHOLDER' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROFILENAME'),
                    "TYPE" => "string",
                    "SIZE" => 35,
                    "MAXLENGHT" => 50
                ),
                'BONUS' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\TextWidget(),
                    'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSCNT') . ' ' . Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
                    "DEFAULT" => 0,
                    'REQUIRED' => true,
                    'PLACEHOLDER' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
                    "TYPE" => "string",
                    "SIZE" => 35,
                    "MAXLENGHT" => 50
                ),
                'TYPE' => array(
                    'WIDGET' => new ITRound\Vbchbbonus\LabelWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_TYPES'),
                    "DEFAULT" => "",
                    "TYPE" => "",
                ),
                'SCOREIN' => array(
                    'WIDGET' => new ITRound\Vbchbbonus\CheckboxWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SCOREIN'),
                    'DEFAULT' => 'N',
                    "REQUIRED" => false,
                    "TYPE" => "string",
                    "MULTIPLE" => false,
                ),
                'ISADMIN' => array(
                    'WIDGET' => new ITRound\Vbchbbonus\CheckboxWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_INADMIN'),
                    'DEFAULT' => 'Y',
                    "REQUIRED" => false,
                    "TYPE" => "string",
                    "MULTIPLE" => false,
                ),
            ),
        ),
        'TAB2' => array(
            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB2_TITLE'),
            "ELEM" => array(
                'NOTIFICATION' => array(
                    'ELEMENT' => array(

                    ),
                ),
            ),
        ),
        'TAB3' => array(
            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB3_TITLE'),
            "ELEM" => array(
                'FILTER' => array(
                    'ELEMENT' => array(
                        'USERGROUP' => array(
                            'WIDGET' => new ITRound\Vbchbbonus\UsergroupWidget(),
                            "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_USERGROUP'),
                            'DEFAULT' => '',
                            "REQUIRED" => false,
                            "TYPE" => "",
                            "MULTIPLE" => true,
                            "SIZE" => 6,
                        ),
                        'ONLYAUTH' => array(
                            'WIDGET' => new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ONLYAUTH'),
                            'DEFAULT' => 'Y',
                            "REQUIRED" => false,
                            "TYPE" => "string",
                            "MULTIPLE" => false,
                        ),
                        'ACTIVATE' => array(
                            'WIDGET' => new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVUSER'),
                            'DEFAULT' => 'Y',
                            "REQUIRED" => true,
                            "TYPE" => "string",
                            "MULTIPLE" => false,
                        ),
                        'MINBONUSPAY1' => array(
                            'WIDGET' => new ITRound\Vbchbbonus\BonusAccountsWidget(),
                            "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MINBONUS'),
                            'DEFAULT' => '1',
                            "REQUIRED" => false,
                            "TYPE" => "string",
                        ),
                    ),
                ),
            ),
        ),
        'TAB4' => array(
            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB4_TITLE'),
            "ELEM" => array(
                'BONUSCONFIG' => array(
                    'ELEMENT' => array(

                    ),
                ),
            ),
        ),
        'TAB5' => array(
            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB5_TITLE'),
            "ELEM" => array(
                'SETTINGS' => array(
                    'ELEMENT' => array(
                        'DESCRIPTION' => array(
                            'WIDGET' => new ITRound\Vbchbbonus\TextboxWidget(),
                            "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_DESCRIPTION'),
                            'DEFAULT' => 'Y',
                            "REQUIRED" => false,
                            "TYPE" => "string",
                            'HELP' => Loc::getMessage('VBCHBB_TYPE_PROFILE_DESCRIPTION'),
                        ),
                        'CODE' => array(
                            "WIDGET" => new ITRound\Vbchbbonus\TextWidget(),
                            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CODE'),
                            "DEFAULT" => "",
                            'REQUIRED' => false,
                            'PLACEHOLDER' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CODE'),
                            "TYPE" => "string",
                            "SIZE" => 35,
                            "MAXLENGHT" => 50
                        ),
                        'SORT' => array(
                            "WIDGET" => new ITRound\Vbchbbonus\TextWidget(),
                            'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SORT'),
                            "DEFAULT" => "500",
                            'REQUIRED' => false,
                            'PLACEHOLDER' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SORT'),
                            "TYPE" => "string",
                            "SIZE" => 35,
                            "MAXLENGHT" => 50
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        array(
            'ACTIVE' => "N",
            'BITRIX' => 'main_OnBeforeProlog',
            'MODULE' => 'CVbchbbEvents::PrologAffiliate',
            'RULES' => '1==1;',
        ),
        array(
            'ACTIVE' => "N",
            'BITRIX' => 'sale_onManagerCouponAdd',
            'MODULE' => 'CVbchbbEvents::onManagerCouponAddHandler',
            'RULES' => '1==1;',
        ),
    ),
    array(),
    'GetRules' => function ($profileID, $Filter = array(), $arFields = array()) {
        $fltr = array();
        $flds = array();
        $Settings = $this->GetSettingsProf($profileID);
        if ($arFields['USERID']) {
            if (array_key_exists('ONLYAUTH', $Filter) && $Filter['ONLYAUTH'] == 'Y') {
                unset($arFields['USERGROUP'][array_search(2, $arFields['USERGROUP'])]);
                unset($fltr['ONLYAUTH']);
            }
            $fltr['ACTIVATE'] = $Filter['ACTIVATE'];
            $flds['ACTIVATE'] = $this->UserActive($arFields['USERID']);
            $fltr['USERGROUP'] = $Filter['USERGROUP'];
            $flds['USERGROUP'] = $arFields['USERGROUP'];
        } else {
            $fltr['USERGROUP'] = $Filter['USERGROUP'];
            $flds['USERGROUP'] = array(2);
        }
        if ($Filter['MINBONUSPAY1']) $Filter['MINBONUSPAY1'] = array_filter($Filter['MINBONUSPAY1']);
        if ($Filter['MINBONUSPAY1'] && is_array($Filter['MINBONUSPAY1']) && sizeof($Filter['MINBONUSPAY1']) > 0) {

            foreach ($Filter['MINBONUSPAY1'] as $p => $ll) {
                $l = floatval($this->GetUserBonus($arFields['USERID'], $Settings['SALEORDERAJAX'], $p));
                $fltr['MINBONUSPAY1_' . $p] = true;
                $flds['MINBONUSPAY1_' . $p] = ($l <= $Filter['MINBONUSPAY1'][$p][0] && $l <= $Filter['MINBONUSPAY1'][$p][1]);
            }
        }
        $k=$this->GetCurrentAffiliate();
        if (array_key_exists("URL_FROM", $arFields) && array_key_exists("REFERER", $arFields)) {
            if (sizeof($k) > 0) {
                $flds['DOMAINE'] = 'Y';
                foreach ($k as $aff) {
                    $urls = explode("\n", $aff['URL']);
                    if (in_array($aff['DOMAINE'], $arFields['URL_FROM']) || in_array($arFields['REFERER'], $urls) || strpos($arFields['REFERER'], $aff['DOMAINE'])) {
                        $fltr['DOMAINE'] = 'Y';
                        break;
                    } else {
                        $fltr['DOMAINE'] = 'N';
                    }
                }
            }
        }
        if (array_key_exists("PROMOCODE", $arFields)) {
            if (sizeof($k) > 0) {
                $flds['PROMO'] = 'Y';
                foreach ($k as $aff) {
                    if (trim($arFields['PROMOCODE']) == $aff['PROMOCODE']) {
                        $fltr['PROMO'] = 'Y';
                        break;
                    } else {
                        $fltr['PROMO'] = 'N';
                    }
                }
            }
        }
        unset($k);
        return $this->GetFilterString($fltr, $flds);

    },
    'GetBonus' => function ($profile = array(), $arFields = array()) {
        $bShow = false;
        global $USER;
        if (!\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isAdminSection()) {
            $bShow = !defined('ADMIN_SECTION');
        } else $bShow = false;
        $profaffID = false;
        $bonus = 0;
        if ($bShow) {
            $k=$this->GetCurrentAffiliate();
            if (sizeof($k) > 0) {
                foreach ($k as $aff) {
                    if(array_key_exists("URL_FROM",$arFields)){
                        $urls = explode("\n", $aff['URL']);
                        if (in_array($aff['DOMAIN'], $arFields['URL_FROM']) || in_array($arFields['REFERER'], $urls) || strpos($arFields['REFERER'], $aff['DOMAINE'])) {
                            $profaffID = $aff;
                        }
                    }
                    if(array_key_exists("PROMOCODE",$arFields)){
                        if($arFields['PROMOCODE']==$aff['PROMOCODE'])
                            $profaffID = $aff;
                    }

                }
            }
            $profaffID=array_filter($profaffID);
            if ($profaffID && is_array($profaffID)) {
                $REF_BONUS = \ITRound\Vbchbbonus\Vbchreferal::GetCookie("REFEREFBONUS");
                if($REF_BONUS){
                    $res = \ITRound\Vbchbbonus\CVbchRefTable::getList(array(
                        'filter' => array('COOKIE' => $REF_BONUS, 'ACTIVE' => 'Y', 'LID' => $this->SITE_ID)
                    ))->fetch();
                    if ($res) {
                        $k1['REFERER'] = '';
                        $k1['REFFROM'] = $profaffID['USERID'];
                        $k1['REFBONUS'] = 'Y';
                        $k1['USERID'] = $arFields['USERID'];
                        $k1['ADDRECORDTYPE']=REF_ADD_COUPONE;
                        if(sizeof($res)>1) $res=current($res);
                        \ITRound\Vbchbbonus\CVbchRefTable::update($res['ID'], $k1);
                    }else{
                        \ITRound\Vbchbbonus\Vbchreferal::addRecordsRef($this->SITE_ID,$profaffID['USERID'],$arFields['USER_ID'],REF_ADD_COUPONE);
                    }
                }else{
                    if($USER->isAuthorized()){
                        $u_id=$USER->GetID();
                        $uauth_ref= \ITRound\Vbchbbonus\CVbchRefTable::getList(array(
                            'filter' => array('USERID' => $u_id, 'ACTIVE' => 'Y', 'LID' => $this->SITE_ID)
                        ))->fetch();
                        if($uauth_ref){
                            \ITRound\Vbchbbonus\CVbchRefTable::update($uauth_ref['ID'], ['REFFROM'=>$profaffID['USERID'],'REFBONUS'=>'Y','ADDRECORDTYPE'=>REF_ADD_COUPONE]);
                        }
                    }
                    \ITRound\Vbchbbonus\Vbchreferal::addRecordsRef($this->SITE_ID,$profaffID['USERID'],$arFields['USER_ID']);
                }
                $bonus = 0;
            }
        }
        $REF_BONUS = \ITRound\Vbchbbonus\Vbchreferal::GetCookie("REFEREFBONUS");
        return $bonus;
    }
);