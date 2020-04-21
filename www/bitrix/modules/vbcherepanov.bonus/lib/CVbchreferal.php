<?php
namespace ITRound\Vbchbbonus;

use Bitrix\Main\Application;
use \Bitrix\Main\Context;
use ITRound\Vbchbbonus;
use Bitrix\Main\Web\Cookie;

class Vbchreferal
{

    public static function OnProlog(){
        global $USER;
        $BBCORE=new Vbchbbonus\Vbchbbcore();
        $bShow=false;
        $BBCORE->SITE_ID=SITE_ID;
        $REF_OPTION['ACTIVE']=$BBCORE->GetOptions($BBCORE->SITE_ID,'REFACTIVE');
        if(!Application::getInstance()->getContext()->getRequest()->isAdminSection()){
            $bShow = !defined('ADMIN_SECTION');
        }else $bShow=false;
        if(!Application::getInstance()->getContext()->getRequest()->isAdminSection()){
            $bShow = !defined('ADMIN_SECTION');
        }else $bShow=false;
        if($BBCORE->CheckSiteOn() && $REF_OPTION['ACTIVE']['OPTION']=='Y' && $bShow && (array_key_exists("SERVER_NAME",$_SERVER) && $_SERVER['SERVER_NAME']!='')) {
            $REFERER = '';$REF_BONUS='';
            $REF_OPTION['REFFIRST'] = $BBCORE->GetOptions($BBCORE->SITE_ID, 'REFFIRST');
            $REF_OPTION['REFLEN'] = $BBCORE->GetOptions($BBCORE->SITE_ID, 'REFLEN');
            $REF_OPTION['REFPARAM'] = $BBCORE->GetOptions($BBCORE->SITE_ID, 'REFPARAM');
            $request = Context::getCurrent()->getRequest();
            $ref = $request->getQueryList()->toArray();
            if(array_key_exists($REF_OPTION['REFPARAM']['OPTION'],$ref)){
                $REFERER = trim($ref[$REF_OPTION['REFPARAM']['OPTION']]);
                $_SESSION['REFERER'] = $REFERER;
                $REF_BONUS = trim(self::GetCookie("REFEREFBONUS"));
                $REF_FROM_ID=0;
                $client_id=($USER->IsAuthorized() ? $USER->GetID() : "");
                if(strlen($REFERER)>0){
                    $fnd = Vbchbbonus\CVbchRefTable::getList(array(
                        'filter' => array('REFERER'=>$REFERER,'LID'=>$BBCORE->SITE_ID),
                        'limit' => null,
                        'offset' => null,
                    ))->fetch();
                    if($fnd){
                        $REF_FROM_ID=$fnd['USERID'];
                    }
                    if($client_id!='')
                        $u_fnd=Vbchbbonus\CVbchRefTable::getList(array(
                            'filter' => array('USERID'=>$client_id,'LID'=>$BBCORE->SITE_ID),
                            'limit' => null,
                            'offset' => null,
                        ))->fetch();
                    if(strlen($REF_BONUS)==0 && !$u_fnd){
                        self::addRecordsRef(SITE_ID,$REF_FROM_ID,$client_id,REF_ADD_BROWSER_REF);
                    }else{
                        $fnd_cooc = Vbchbbonus\CVbchRefTable::getList(array(
                            'filter' => array('COOKIE'=>$REF_BONUS,'LID' => $BBCORE->SITE_ID),
                            'limit' => null,
                            'offset' => null,
                        ))->fetch();
                        if($fnd_cooc){
                            if($fnd_cooc['REFFROM']!=$REF_FROM_ID){
                                self::addRecordsRef(SITE_ID,$REF_FROM_ID,$client_id,REF_ADD_BROWSER_REF);
                            }else{
                                if($fnd_cooc['USERID']=='' || $fnd_cooc['USERID']==0){
                                    self::UpdateRecordsRef($fnd_cooc,$client_id);
                                }else{
                                    self::addRecordsRef(SITE_ID,$REF_FROM_ID,$client_id,REF_ADD_BROWSER_REF);
                                }

                            }
                        }else{
                            if(!$u_fnd){
                                self::addRecordsRef(SITE_ID,$REF_FROM_ID,$client_id,REF_ADD_BROWSER_REF);
                            }
                        }
                    }
                }
            }
        }
        unset($BBCORE);
    }
    public function UpdateRecordsRef($rec,$client_id,$REF_CODE=''){
        $k=$rec;
        unset($k['ID']);
        if($REF_CODE!='')
            $k['REFERER']=$REF_CODE;
        $k['USERID']=$client_id;
        Application::getConnection()->startTransaction();
        $tr=Vbchbbonus\CVbchRefTable::update($rec['ID'],$k);
        if($tr->isSuccess()){
            Application::getConnection()->commitTransaction();
            self::AddCookie("REFEREFBONUS", '');
        }else{
            Application::getConnection()->rollbackTransaction();
        }
    }

    public function addRecordsRef($site,$ref_from_id,$client_id,$type=REF_ADD_MANUAL_ADMIN,$referer='',$cook=false){

        if($cook==false)
            $lp=self::uniqKey('refbonus_');
        else $lp=$cook;
        if($ref_from_id==$client_id)  return false;
        $fields = array(
            'LID' => $site,
            'ACTIVE' => 'Y',
            'REFFROM' => $ref_from_id,
            'REFERER' => $referer,
            'REFBONUS' => 'N',
            'USERID' =>  $client_id,
            'COOKIE' => $lp,
            'ADDRECORDTYPE'=>$type,
        );
        if($client_id!='')
            $l=Vbchbbonus\CVbchRefTable::getList([
                'filter'=>['USERID'=>$client_id],
                'select'=>['ID'],
            ])->fetch();
        else $l=false;
        if(!$l){
            Application::getConnection()->startTransaction();
            $tr=Vbchbbonus\CVbchRefTable::add($fields);
            if($tr->isSuccess()){
                Application::getConnection()->commitTransaction();
                if($client_id=='') {
                    self::AddCookie("REFEREFBONUS", $lp);
                }
            }else{
                Application::getConnection()->rollbackTransaction();
            }
        }
        unset($l);
    }

    public static function AddRef(&$arFields){
        $BBCORE=new Vbchbbcore();
        $BBCORE->SITE_ID=$arFields['SITE_ID'] ? $arFields['SITE_ID'] : $BBCORE->GetSiteID();
        $REF_OPTION['ACTIVE']=$BBCORE->GetOptions($BBCORE->SITE_ID,'REFACTIVE');
        if(!Application::getInstance()->getContext()->getRequest()->isAdminSection()){
            $bShow = !defined('ADMIN_SECTION');
        }else $bShow=false;
        if($BBCORE->CheckSiteOn() && $REF_OPTION['ACTIVE']['OPTION']=='Y' && $bShow){
            $REF_OPTION['REFFIRST']=$BBCORE->GetOptions($BBCORE->SITE_ID,'REFFIRST');
            $REF_OPTION['REFLEN']=$BBCORE->GetOptions($BBCORE->SITE_ID,'REFLEN');
            $REF_OPTION['REFPARAM']=$BBCORE->GetOptions($BBCORE->SITE_ID,'REFPARAM');
            $REF_CODE=self::GenerateRef( $REF_OPTION['REFFIRST']['OPTION'],intval($REF_OPTION['REFLEN']['OPTION']),$arFields['USERID']);
            $REF_BONUS = trim(self::GetCookie("REFEREFBONUS"));
            $REF_FROM_ID=0;
            $client_id=$arFields['USERID'];

            if(array_key_exists('REFERER',$arFields['REFERALS']) && !empty($arFields['REFERALS']['REFERER'])){
                $REF_FROM_ID=$arFields['REFERALS']['REFERER'];
            }
            if($REF_BONUS!=''){
                $ures=Vbchbbonus\CVbchRefTable::getList(array(
                    'filter'=>array('ACTIVE'=>'Y','LID'=>$BBCORE->SITE_ID,'COOKIE'=>$REF_BONUS)
                ))->fetch();
                if(sizeof($ures)>0) {
                    if($ures['USERID']==0 || $ures['USERID']==''){
                        self::UpdateRecordsRef($ures,$client_id,$REF_CODE);
                    }
                }else{
                    self::addRecordsRef(SITE_ID,$REF_FROM_ID,$client_id,REF_ADD_COMPONENT,$REF_CODE,$REF_BONUS);
                }
            }else{
                self::addRecordsRef(SITE_ID,$REF_FROM_ID,$client_id,REF_ADD_COMPONENT,$REF_CODE,$REF_CODE);
            }
        }
    }
    public static function AddCookie($NAME,$VAL){
        $BBCORE=new Vbchbbonus\Vbchbbcore();
        $secure=false;
        $cookieTime = IntVal(\COption::GetOptionString("vbcherepanov.bonus", "COOKIETIME", "365",$BBCORE->SITE_ID));
        $bmc=\COption::GetOptionString("main","cookie_name","BITRIX_SM");
        if(\COption::GetOptionString("sale", "use_secure_cookies", "N") == "Y" && \CMain::IsHTTPS())
            $secure=true;
        setcookie($bmc.'_'.$NAME, $VAL, ($cookieTime <= 0) ? 0 : time() + $cookieTime * 24 * 60 * 60, "/");
        unset($BBCORE);
    }
    public static function GetCookie($NAME){
        return Application::getInstance()->getContext()->getRequest()->getCookie($NAME);
    }

    public function AddReferal($USER_ID,$REF_USER){
    }
    public static function GetRefFrom($USERID,$site){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'USERID'=>$USERID,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
        ))->fetch();
        return $res;
    }
    public static function GetReferal($USER_ID,$site){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'REFFROM'=>$USER_ID,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
        ))->fetchAll();
        return $res;
    }
    public static function GetReferalCount($USER_ID,$site){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'REFFROM'=>$USER_ID,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
        ))->getSelectedRowsCount();
        return $res;
    }
    public static function GetUserByRef($ref,$site,$select=array()){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'REFERER'=>$ref,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
            'select'=>$select,
        ))->fetchAll();
        return $res;
    }

    public static function GetUserByID($ref,$site,$select=array()){
        $res=Vbchbbonus\CVbchRefTable::getList(array(
            'filter'=>array(
                'ID'=>$ref,
                'ACTIVE'=>'Y',
                'LID'=>$site,
            ),
            'select'=>$select,
        ))->fetchAll();
        return $res;
    }

    public function GenerateRef($FIRST="REF",$n=10,$user_id=0){
        $BBCORE=new Vbchbbonus\Vbchbbcore();
        $type = \COption::GetOptionString("vbcherepanov.bonus", "TYPEREFCODE", "NUMNEXT",$BBCORE->SITE_ID);
        unset($BBCORE);
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '1234567890';
        switch($type){
            case 'PHONE':
                $str='';
                if($user_id!=0 || $user_id!=''){
                    $us_res=\Bitrix\Main\UserTable::getList([
                        'filter'=>['ID'=>intval($user_id)],
                        'select'=>['PERSONAL_PHONE'],
                    ])->fetch();
                    if($us_res['PERSONAL_PHONE']!=''){
                        $str=str_replace(['+','(',')','-','_'],'',$us_res['PERSONAL_PHONE']);
                    } else return '';
                }
                return $str;
                break;
            case 'NUM':
                $chars = $numbers;
                break;
            case 'ABS':
                $chars = $upper;
                break;
            case 'NUMABS':
                $chars = $upper. $numbers;
                break;
            case 'NUMNEXT':
                $last=Vbchbbonus\CVbchRefTable::getList(array(
                    'filter'=>[],
                    'order'=>['ID'=>'DESC'],
                    'select'=>['REFERER'],
                ))->fetch();
                $ll=intval(str_replace($FIRST."-","",$last['REFERER']));
                $next=$ll+1;
                return $FIRST."-".$next;
                break;
        }
        $chars_length = strlen($chars) - 1;
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < $n; $i = strlen($string)) {
            $random = $chars{rand(0, $chars_length)};
            if ($random != $string{$i - 1}) $string .= $random;
        }
        return $FIRST."-".$string;
    }
    public static function uniqKey($prefix=""){
        $upper = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '1234567890'; // numbers
        $chars = $upper. $numbers;
        $chars_length = strlen($chars) - 1;
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < 15; $i = strlen($string)) {
            $random = $chars{rand(0, $chars_length)};
            if ($random != $string{$i - 1}) $string .= $random;
        }
        return $prefix.$string;
    }
}