<?php
namespace VBCherepanov\Bonus;

use Bitrix\Main\Entity;
use Bitrix\Iblock;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main;
use Bitrix\Sale;
use VBCherepanov\Bonus;
use Bitrix\Main\Context;
Loc::loadMessages(__FILE__);

define("REGISTRATION",1); //registration
define("SUBSCRIBE",2); //subscribe
define("FIRST_ORDER",3); //first order
define("ORDER",4); //any order in filter
define("BIRTHDAY",5); //bithday user
define("LIKE",6); //like in social network
define("REVIEW",7); //for review
define("RATING",8); //none in future
define("SUBSCRIBE_DEL",9); // on subscribe delete
define("ORDER_DEL",10); // on order delete
define("SROK_DEL",11); // when bonus time in out
define("PAY",12); //pay form order part
define("ADDEDIT",13); // add or edit bonus
define("ADD_BIRTHDAY_DATA",15); // add or edit bonus

class BMain {

    var $saveload=array();
    var $SETTINGS=array();
    var $module_id="";
    var $suffix="";
    var $LID="";
    var $ACCOUNT_BITRIX;
    var $social=array();

    public function __construct($LID=""){
        $this->module_id="vbcherepanov.bonus";
        global $INSTAL_MODULE;
        if($LID!="")
            $this->LID=$LID;
        else
            $this->LID=$this->GetSiteID();
        $this->INSTALL_MODULES=$INSTAL_MODULE;
        $this->SETTINGS=array();
        $this->SETTINGS['SITE_ON']=unserialize(Option::get($this->module_id,"use_on_sites","",$this->LID));
        $this->suffix="_bx_site_".$this->LID;
        $this->saveload=array("REG","SUBSCRIBE","BITHDAY","LIVE","NAMEBONUS","NOTIFICATION","USED_ACCOUNT",
            "RATING","NOTIFICATIONSMS","ORDER","OTHER","SOC_SERVICES","REVIEW","POROG");
        foreach($this->saveload as $options){
            $this->SETTINGS[$options]=$this->GetOptions($this->LID,$options);
        }
        $this->SETTINGS['FULL']=unserialize(Option::get($this->module_id,"full","",$this->LID));
        $this->SITE=$this->GetSiteSID();
        $this->GetSocialService();
        $this->ACCOUNT_BITRIX=($this->SETTINGS['USED_ACCOUNT']["OPTION"]=="Y" ? true : false);
        $this->ACCOUNT_BITRIX_CLASS=($this->SETTINGS['USED_ACCOUNT']["OPTION"]=="Y"
            ? "VBCherepanov\\Bonus\\TmpTable"
            : "VBCherepanov\\Bonus\\BonusTable"
        );
		$isOrderConverted = \Bitrix\Main\Config\Option::get("main", "~sale_converted_15", 'N');
		$this->D7=($isOrderConverted=='Y' ?true:false);
    }
    public function GetOptions($site,$OPTION="")
    {
        $suffix = ($site <> '' ? '_bx_site_'.$site:'');
        if($OPTION)
            return array("OPTION"=>unserialize(Option::get($this->module_id,$OPTION.$suffix,"",$site)),"SUFFIX"=>$suffix);
        else return false;
    }
    public function declOfNum($number){
        if($number<0) $ot=true;else $ot=false;
        $Value="";
        $cases = array (2, 0, 1, 1, 1, 2);
        if($number<0) $number=$number*(-1);
        $option=$this->SETTINGS['NAMEBONUS'];
        if($option["OPTION"]["SUFIX"]=="NAME"){
            $titles[0] = $option["OPTION"]["NAME"][1];
            $titles[1] = $option["OPTION"]["NAME"][2];
            $titles[2] =  $option["OPTION"]["NAME"][3];
            $Value=round($number,2)." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
        } elseif($option["OPTION"]["SUFIX"]=="CURRENCY") {
            $Value=($ot ? "-": "").FormatCurrency(round($number,2),  $option["OPTION"]["CURRENCY"]);
        }
        return $Value;
    }
    private function GetSiteID($default="s1"){
        $request = Context::getCurrent()->getRequest();
        $domain= $request->getHttpHost();
        $domain= str_replace(array('https://', 'http://', '/'), '', $domain);
        $domain=explode(":",$domain);
        $rsSites=Main\SiteTable::getList(array(
            'filter'=>array("SERVER_NAME"=>$domain[0],"ACTIVE"=>"Y"),
            'select'=>array("LID"),
        ))->fetch();
        if($rsSites){

            return $rsSites["LID"];
        }
        return $default;
    }
    private static function GetSiteSID(){
        $site=array();
        $rsSites=Main\SiteTable::getList(array(
            'filter'=>array("ACTIVE"=>"Y"),
            'select'=>array("LID"),
        ));
        while($arSite=$rsSites->fetch()){
            $site[]=$arSite['LID'];
        }
        return $site;
    }
    public function ModuleCurrency(){
        $Currency="";
        $option=$this->SETTINGS['NAMEBONUS'];
        if($option["OPTION"]["SUFIX"]=="NAME"){
            $titles[0] =  $option["OPTION"]["NAME"][1];
            $titles[1] =  $option["OPTION"]["NAME"][2];
            $titles[2] =  $option["OPTION"]["NAME"][3];
            $Currency=$titles[0];
        } elseif($option["OPTION"]["SUFIX"]=="CURRENCY" && $this->SETTINGS['INSTALL_MODULES']['catalog']) {
            $Currency=preg_replace('/[\d(,+.\'\"]/si','',FormatCurrency(1,$option["OPTION"]["CURRENCY"]));
        }else {
            $Currency = preg_replace('/[\d(,+.\'\"]/si', '', Loc::getMessage("CVBCH_MAIN_BONUS_RUB"));
        }
        return $Currency;
    }
    public  function ReturnCurrency($number){
        $Currency="";
        $option=$this->SETTINGS['NAMEBONUS'];
        $cases = array (2, 0, 1, 1, 1, 2);
        if($option["OPTION"]["SUFIX"]=="NAME"){
            $titles[0] = $option["OPTION"]["NAME"][1];
            $titles[1] = $option["OPTION"]["NAME"][2];
            $titles[2] = $option["OPTION"]["NAME"][3];
            $Currency=round($number,2)." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
        } elseif($option["OPTION"]["SUFIX"]=="CURRENCY") {
            $Currency= $option["OPTION"]["CURRENCY"];
        }
        return $Currency;
    }
    public function GetUserInfo($UserID){
        $username="";
        $UserInfo=Main\UserTable::getList(array(
            'filter'=>array("ID"=>$UserID),
            'select'=>array("NAME","LAST_NAME","EMAIL","LID","LOGIN"),
        ))->fetch();
        if($UserInfo){
            if($UserInfo["NAME"]) $username.=$UserInfo["NAME"];
            if($UserInfo["LAST_NAME"]) $username.=' '.$UserInfo["LAST_NAME"];
        }
        if(trim($username)=="") $username=$UserInfo["LOGIN"];
        return array("FIO"=>trim($username),"EMAIL"=>$UserInfo["EMAIL"],"LID"=>($UserInfo["LID"] ? $UserInfo["LID"] : $this->LID));
    }
    public function GetLive(){
        $options=$this->SETTINGS['LIVE'];
        $add=array("DD"=>"","MM"=>"","YYYY"=>"","HH"=>"","MI"=>"","SS"=>"");
        switch($options["OPTION"]["CH"]){
            case "D":
                $add["DD"]=$options["OPTION"]["COUNT"];
                break;
            case "W":
                $add["DD"]=$options["OPTION"]["COUNT"]*7;
                break;
            case "M":
                $add["MM"]=$options["OPTION"]["COUNT"];
                break;
            case "Y":
                $add["YYYY"]=$options["OPTION"]["COUNT"];
                break;
            case "A":
            default:break;
        }
        return $add;
    }
    public static function GetPeriodTime($count,$type,$in){
        $add=array("DD"=>"","MM"=>"","YYYY"=>"","HH"=>"","MI"=>"","SS"=>"");
        $cnt=1;
        if(strlen($type)==2){
            $cnt=$type[0];
        }
        if($in) $cnt=-$cnt;
        switch($type){
            case "C":
                $add["HH"]=$cnt*$count;
                break;
            case "D":
                $add["DD"]=$cnt*$count;
                break;
            case "N":
            case "W":
            case "2W":
                $add["DD"]=$cnt*$count*7;
                break;
            case "M":
            case "3M":
            case "6M":
                $add["MM"]=$cnt*$count;
                break;
            case "Y":
            case "L":
                $add["YYYY"]=$cnt*$count;
                break;
            default:break;
        }
        return $add;
    }
    public function SendNotification($arFields,$email=true,$sms=false){
        if($email){
            $event=new \CEvent();
            $event->Send("VBCH_BIGBONUSE", $arFields["site"], $arFields, "N",Option::get($this->module_id, "EVENT_".$arFields["EVENT"]));
        }
        if($this->SETTINGS['INSTALL_MODULES']["vbcherepanov.smsnotification"] && $sms){
            //send sms notification
        }
    }
    private function GetSocialService(){
        $this->social=$this->SETTINGS['SOC_SERVICES']['OPTION'];
    }
    public function BonusLive(){
        @set_time_limit(0);
        ignore_user_abort(true);
        global $USER;
        foreach($this->SETTINGS['SITE_ON'] as $pl=>$sid) {
            if ($sid == "Y") {
                $this->LID=$pl;
                if (!is_object($USER)) $USER = new \CUser();
                $params=array(
                    "filter" => array("LID" => $this->LID, "ACTIVE" => "Y", "ACTIVE_TO" => Main\Type\DateTime::convertFormatToPhp(date('d.m.Y', time()))),
                );
                $res = call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array($params));
                while ($bon = $res->fetch()) {
                    $Fields = $bon;
                    $Fields['ACTIVE'] = 'N';
                    call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"update"),array($bon['ID'], $Fields));
                    $this->AddBonus($bon['USERID'], $bon['LID'], -$bon['BONUS'], SROK_DEL, array());
                }
                unset($bon, $res);
            }
        }
    }
    public function PreDelete(){
        @set_time_limit(0);
        ignore_user_abort(true);
        global $USER;
        if($this->SETTINGS["ORDER"]["OPTION"]["PREDELETE"]["DAY"]=="" || $this->SETTINGS["ORDER"]["OPTION"]["PREDELETE"]["DAY"]==0){
            $day=1;
        }else{
            $day=$this->SETTINGS["ORDER"]["OPTION"]["PREDELETE"]["DAY"];
        }
        foreach($this->SETTINGS['SITE_ON'] as $pl=>$sid) {
            if ($sid == "Y") {
                $this->LID=$pl;
                if (!is_object($USER)) $USER = new \CUser();
                $params=array(
                    "filter" => array("LID" => $this->LID, "ACTIVE" => "Y", "ACTIVE_TO" => Main\Type\DateTime::convertFormatToPhp(date('d.m.Y', (time()-3600*24*$day)))),
                    "select"=>array("*","ALL_BUDGET"=>"ACNT.CURRENT_BUDGET"),
                    'runtime' => array(
                        'ACNT' => array(
                            'data_type' => '\\VBCherepanov\\Bonus\\AccountTable',
                            'reference' => array(
                                '=this.USERID' => 'ref.USER_ID',
                            )
                        )
                    )
                );
                $res = call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array($params));
                while ($bon = $res->fetch()) {
                    $user=$this->GetUserInfo($bon['USER_ID']);
                    if ($this->SETTINGS['NOTIFICATION']['OPTION'] == 'Y') {
                        $arFields = array(
                            'TYPE' => "PREDELETE",
                            'USERNAME' => $user['FIO'],
                            'USEREMAIL' => $user['EMAIL'],
                            'site' => $user['LID'] ? $user['LID'] : $this->LID,
                            'EVENT' => "PREDELETE",
                            'DESCRIPTION' => $bon['DESCRIPTION'],
                            'ACTIVE_FROM' => $bon['ACTIVE_FROM'],
                            'ACTIVE_TO' => $bon['ACTIVE_TO'],
                            'BONUS' => $this->declOfNum($bon['BONUS']),
                            'BONUSALL' => $this->declOfNum($bon['ALL_BUDGET']-$bon['BONUS']),
                            "DAY"=>$day,
                        );
                        $this->SendNotification($arFields, true, $this->SETTINGS['NOTIFICATIONSMS']['OPTION'] == 'Y' ? true : false);
                    }
                }
                unset($bon, $res);
            }
        }
    }
    public function AddBonus($USERID,$LID,$SUMMA,$TYPE,$DESCRIPTION_ARRAY=array(),$admin=false){
        if($this->SETTINGS['SITE_ON'][$LID]=="Y") {
            $mas = array();
            $ball=0;
            $mas1 = array();
            $act="Y";
            $bonusall = 0;
            $DESCRIPTION = array(
                REGISTRATION => Loc::getMessage("CVBCH_MAIN_BONUS_REG"),
                SUBSCRIBE => Loc::getMessage("CVBCH_MAIN_BONUS_SUBSCRIBE"),
                FIRST_ORDER => Loc::getMessage("CVBCH_MAIN_BONUS_FIRSTORDER"),
                ORDER => Loc::getMessage("CVBCH_MAIN_BONUS_ORDER"),
                BIRTHDAY => Loc::getMessage("CVBCH_MAIN_BONUS_BIRTHDAY"),
                LIKE => Loc::getMessage("CVBCH_MAIN_BONUS_LIKE"),
                REVIEW => Loc::getMessage("CVBCH_MAIN_BONUS_REVIEW"),
                RATING => Loc::getMessage("CVBCH_MAIN_BONUS_RATING"),
                SUBSCRIBE_DEL => Loc::getMessage("CVBCH_MAIN_BONUS_SUBCR_DEL"),
                ORDER_DEL => Loc::getMessage("CVBCH_MAIN_BONUS_DEL_ORDER"),
                SROK_DEL => Loc::getMessage("CVBCH_MAIN_BONUS_SROK"),
                PAY => Loc::getMessage("CVBCH_MAIN_BONUS_PAY"),
                ADDEDIT => Loc::getMessage("CVBCH_MAIN_BONUS_ADDEDIT"),
                ADD_BIRTHDAY_DATA => Loc::getMessage("CVBCH_MAIN_BONUS_ADD_BIRTHDAY_DATA"),
            );
            $descr = $DESCRIPTION[$TYPE];
            if (is_array($DESCRIPTION_ARRAY)) {
                foreach ($DESCRIPTION_ARRAY as $key => $val) {
                    $mas[] = $key;
                    $mas1[] = $val;
                }
            }
            $descr = str_replace($mas, $mas1, $descr);

            $from = ConvertTimeStamp(time(), "SHORT");

            $option_order = $this->SETTINGS['ORDER']['OPTION'];

            if ($option_order['DELAY']['ACTIVE'] == 'Y' && $SUMMA>0 && !$admin) {
                $time_delta = $this->GetPeriodTime($option_order['DELAY']['COUNT'], $option_order['DELAY']['TIME'], false);
                $from = ConvertTimeStamp(AddToTimeStamp($time_delta, MakeTimeStamp($from, "DD.MM.YYYY")), "SHORT");
                $act = 'N';
            }
            else
                $act = 'Y';

            $toplus = $this->GetLive();

            $flagtime = false;
            if(is_array($toplus)) {
                foreach ($toplus as $tp) {
                    if ($tp != "") $flagtime = true;
                }
            }else $flagtime=false;

            $to = $flagtime ? ConvertTimeStamp(AddToTimeStamp($this->GetLive(), MakeTimeStamp($from, "DD.MM.YYYY")), "SHORT") : "";

            if($TYPE==ADDEDIT){
                $act="Y";
                $from = ConvertTimeStamp(time(), "SHORT");
                $to = $flagtime ? ConvertTimeStamp(AddToTimeStamp($this->GetLive(), MakeTimeStamp($from, "DD.MM.YYYY")), "SHORT") : "";
            }
            $FIELDS = array(
                "LID" => $LID,
                "BONUS" => $SUMMA,
                "ACTIVE" => $act,
                "USERID" => $USERID,
                "ACTIVE_FROM" => $flagtime ?($SUMMA < 0 ? '' : new \Bitrix\Main\Type\DateTime($from)) : new \Bitrix\Main\Type\DateTime($from),
                "ACTIVE_TO" => $flagtime ? ($SUMMA < 0 ? '' : new \Bitrix\Main\Type\DateTime($to)) : '',
                "TYPE" => $TYPE,
                "DESCRIPTION" => $descr,
                "OPTIONS" => base64_encode(serialize($DESCRIPTION_ARRAY)),
            );
            $user = $this->GetUserInfo($USERID);
            $event = new \Bitrix\Main\Event($this->module_id, "OnBeforeBonusAdd", array(&$FIELDS));
            $event->send();
            if ($event->getResults()) {
                foreach ($event->getResults() as $evenResult) {
                    if ($evenResult->getType() == \Bitrix\Main\EventResult::SUCCESS) {
                        $FIELDS = $evenResult->getParameters();
                    }
                }
            }
            call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"add"),array($FIELDS));

            if(($this->SETTINGS['ORDER']['OPTION']['DELAY']['ACTIVE']!='Y' || $SUMMA<0) || $admin) {
                if ($this->ACCOUNT_BITRIX) {
                    $cur = $this->ReturnCurrency($SUMMA);
                    \CSaleUserAccount::UpdateAccount($USERID, $SUMMA, $cur, $descr);
                    if ($ar = \CSaleUserAccount::GetByUserID($USERID, $cur)) {
                        $bonusall = $this->declOfNum($ar['CURRENT_BUDGET']);
                    }
                } else {
                    if ($FIELDS['ACTIVE'] == 'Y') {
                        $resbon = Bonus\AccountTable::getList(array(
                            'filter' => array('USER_ID' => $USERID),
                        ));
                        if ($acc_bon = $resbon->fetch()) {
                            $newFLDS = $acc_bon;
                            $userbonus = $acc_bon['CURRENT_BUDGET'];
                            $newFLDS['CURRENT_BUDGET'] = $SUMMA + $userbonus;
                            $bonusall = $this->declOfNum($SUMMA + $userbonus);
                            Bonus\AccountTable::update($acc_bon['ID'], $newFLDS);
                        } else {
                            $ACC_FIELDS = array(
                                'USER_ID' => $USERID,
                                'CURRENT_BUDGET' => $SUMMA,
                                'CURRENCY' => $this->ReturnCurrency($SUMMA),
                                'NOTES' => '',
                            );
                            Bonus\AccountTable::add($ACC_FIELDS);
                            $bonusall = $this->declOfNum($SUMMA);
                        }
                    }
                }
                $FIELDS['BONUS_ALL'] = $bonusall;
            }else{
                if($this->ACCOUNT_BITRIX){
                    $cur = $this->ReturnCurrency($SUMMA);
                    if ($ar = \CSaleUserAccount::GetByUserID($USERID, $cur)) {
                        if($ar)
                            $ball = $this->declOfNum($ar['CURRENT_BUDGET']);
                        else $ball=$this->declOfNum($SUMMA);
                    }
                }else {
                    $resbon = Bonus\AccountTable::getList(array(
                        'filter' => array('USER_ID' => $USERID),
                    ))->fetch();
                    if($resbon)
                        $ball=$this->declOfNum($resbon['CURRENT_BUDGET']);
                    else $ball=$this->declOfNum($SUMMA);
                }
                $FIELDS['BONUS_ALL']=$ball;
            }
            $event = new \Bitrix\Main\Event($this->module_id, "OnAfterBonusAdd", array(&$FIELDS));
            $event->send();
            if ($event->getResults()) {
                foreach ($event->getResults() as $evenResult) {
                    if ($evenResult->getType() == \Bitrix\Main\EventResult::SUCCESS) {
                        $FIELDS = $evenResult->getParameters();
                    }
                }
            }
            $tt = ($SUMMA < 0 ? 'DELETE' : 'ADD');
            $tt=($TYPE==PAY ? 'PAY' : $tt);
            if ($this->SETTINGS['NOTIFICATION']['OPTION'] == 'Y') {
                $arFields = array(
                    'TYPE' => $TYPE,
                    'USERNAME' => $user['FIO'],
                    'USEREMAIL' => $user['EMAIL'],
                    'site' => $user['LID'] ? $user['LID'] : $this->LID,
                    'EVENT' => $tt,
                    'DESCRIPTION' => $FIELDS['DESCRIPTION'],
                    'ACTIVE_FROM' => $FIELDS['ACTIVE_FROM'],
                    'ACTIVE_TO' => $FIELDS['ACTIVE_TO'],
                    'BONUS' => $this->declOfNum($SUMMA),
                    'BONUS_ALL' => $FIELDS['BONUS_ALL'],
                );
                $this->SendNotification($arFields, true, $this->SETTINGS['NOTIFICATIONSMS']['OPTION'] == 'Y' ? true : false);
            }
        }
    }
    public function GetBonusNum($partpaysumm,$orderprice,$summ_total){
        $delta1=0;$bonus=0;
        if($partpaysumm==0) return 0;
        $percent=strpos($partpaysumm,"%");
        if($percent!==false){
            $per=intval($partpaysumm);
            $bonus=$orderprice*($per/100); //get bonus  (summa)
            if($summ_total=="Y" || $summ_total==true){
                $delta1=$orderprice-$bonus;
                $bonus=$delta1*($per/100); //get bonus (summa-bonus)
            }
            return $bonus;
        }else{
            return $partpaysumm;
        }
    }
    public function OnFirstOrder($ORDER){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $Return = false;
            if ($this->SETTINGS['ORDER']['OPTION']['FIRST']['ACTIVE'] == "Y") {
                $summa = $this->GetBonusNum($this->SETTINGS['ORDER']['OPTION']['FIRST']['COUNT'],
                    $ORDER['PRICE'],
                    $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                $arFilter = array('USER_ID' => $ORDER['USER_ID']);
                $db_sales = \CSaleOrder::GetList(array(), $arFilter);
                $count = $db_sales->SelectedRowsCount();
                if ($count > 1) $Return = false;
                else {
                    $this->AddBonus($ORDER['USER_ID'], $ORDER['LID'], $summa, FIRST_ORDER, array("#ID#" => $ORDER['ID'], "#SUMM#" => CurrencyFormat($ORDER["PRICE"], $ORDER["CURRENCY"])));
                    $Return = true;
                }
            }
            return $Return;
        }
    }
    public function OnRegister($arFields){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $userID = intval($arFields['ID']);
            $siteID = strlen($arFields['LID']) > 0 ? $arFields['LID'] : $this->LID;
            if ($this->SETTINGS['REG']['OPTION']['ACTIVE'] == 'Y') {
                if ($this->SETTINGS['REG']['OPTION']['COUNT']) {
                    $count = floatval($this->SETTINGS['REG']['OPTION']['COUNT']);
                    $this->AddBonus($userID, $siteID, $count, REGISTRATION);
                }
            }
        }
    }
    public function OnCancelOrder($ID,$val,$desc){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $resID = "";
            $arOrder = \CSaleOrder::GetByID($ID);
            $this->LID = $arOrder['LID'];
            if($this->D7) $arOrder['PAYED']="N";
            if ($arOrder['CANCELED'] == 'Y' || $val == 'Y' || $arOrder['PAYED'] == 'N') {
                $res = call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array((array(
                    "filter" => array("LID" => $arOrder['LID'],  'USERID' => $arOrder['USER_ID']),
                ))));
                while ($rec = $res->fetch()) {
                    $dat = unserialize(base64_decode($rec['OPTIONS']));
                    if ($dat['#ORDER_ID#'] == trim($ID)) {
                        $lm=$rec;
                        $lm['ACTIVE']='N';
                        $lm['ACTIVE_TO']=$lm['ACTIVE_FROM']='';
                        call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"update"),array($rec['ID'],$lm));
                        $resID = $rec;
                        break;
                    }
                }
                if ($resID && $resID['ACTIVE']!='N') {
                    $this->AddBonus($resID['USERID'], $resID['LID'], -$resID['BONUS'], ORDER_DEL,
                        array("#ORDER_ID#" => $ID,
                            "#SUMM#" => CurrencyFormat($arOrder['PRICE'], $arOrder['CURRENCY'])
                        )
                    );
                }
                unset($res, $dat, $rec);
            }
        }
    }
    public function OnAddIBlockElement($arFields){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['iblock']) {
            global $USER,$DB;
            if ($USER->isAuthorized()) {
                $cnt=0;
                $create_from = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)),
                    mktime("00", "00", "00", date("m"), date("d"), date("Y")));
                $create_to = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)),
                    mktime("23", "59", "59", date("m"), date("d"), date("Y")));
                $option = $this->SETTINGS['FULL']['REVIEW' . $this->suffix];
                if (is_array($option) && sizeof($option) > 0) {
                    foreach ($option as $id => $rew) {
                        if ($rew['REVIEWTYPE'] == 'IB' && $rew['REVIEWIB'] == $arFields['IBLOCK_ID']) {
                            $cnt=\CIBlockElement::GetList(array(),
                                array(
                                    "IBLOCK_ID"=>$arFields['IBLOCK_ID'],
                                    'CREATED_BY'=>$arFields['CREATED_BY'],
                                    ">=DATE_CREATE"=>$create_from,
                                    "<=DATE_CREATE"=>$create_to,
                                ),false,false,array('ID','DATE_CREATE'))->SelectedRowsCount();
                            if ($cnt<($rew['REVIEW_INDAY']+1))
                                $this->AddBonus($arFields['CREATED_BY'], $this->LID, intval($rew['REVIEW_BONUS']), REVIEW, array("#REVIEW#" => TruncateText(strip_tags($arFields['NAME'], 25))));
                        }
                    }
                }
            }
        }
    }
    public function OnAddBlogElement($arFields){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['blog']) {
            global $USER,$DB;
            if ($USER->isAuthorized()) {
                $cnt=0;
                $create_from = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)),
                    mktime("00", "00", "00", date("m"), date("d"), date("Y")));
                $create_to = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)),
                    mktime("23", "59", "59", date("m"), date("d"), date("Y")));
                $option = $this->SETTINGS['FULL']['REVIEW' . $this->suffix];
                if (is_array($option) && sizeof($option) > 0) {
                    foreach ($option as $id => $rew) {
                        if ($rew['REVIEWTYPE'] == 'BLOG' && $rew['REVIEWBLOG'] == $arFields['BLOG_ID']) {
                            $filter=array(
                                'AUTHOR_ID'=>$arFields["AUTHOR_ID"],
                                'BLOG_ID'=>$arFields["BLOG_ID"],
                                ">=DATE_CREATE"=>$create_from,
                                "<=DATE_CREATE"=>$create_to,
                            );
                            $cnt = \CBlogComment::GetList(
                                array(),
                                $filter,
                                false,
                                false,
                                array("ID")
                            )->SelectedRowsCount();
                            if ($cnt<($rew["REVIEW_INDAY"]+1))
                                $this->AddBonus($arFields['AUTHOR_ID'], $this->LID, intval($rew['REVIEW_BONUS']), REVIEW, array("#REVIEW#" => TruncateText(strip_tags($arFields['POST_TEXT'], 25))));
                        }
                    }
                }
            }
        }
    }
    public function OnAddForumElement($arFields){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['forum']) {
            global $USER,$DB;
            if ($USER->isAuthorized()) {
                $cnt=0;
                $create_from = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)),
                    mktime("00", "00", "00", date("m"), date("d"), date("Y")));
                $create_to = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)),
                    mktime("23", "59", "59", date("m"), date("d"), date("Y")));
                $option = $this->SETTINGS['FULL']['REVIEW' . $this->suffix];
                if (is_array($option) && sizeof($option) > 0) {
                    foreach ($option as $id => $rew) {
                        if ($rew['REVIEWTYPE'] == 'FORUM' && $rew['REVIEWFORUM'] == $arFields['FORUM_ID']) {
                            $cnt = \CForumMessage::GetList(array("ID"=>"ASC"),
                                array("FORUM_ID"=>$arFields['FORUM_ID'],"TOPIC_ID"=>$arFields['TOPIC_ID'],"AUTHOR_ID"=>$arFields['AUTHOR_ID'],
                                    ">=POST_DATE" =>$create_from, "<=POST_DATE" =>$create_to))->SelectedRowsCount();
                            if ($cnt<($rew['REVIEW_INDAY']+1))
                                $this->AddBonus($arFields['AUTHOR_ID'], $this->LID, intval($rew['REVIEW_BONUS']), REVIEW, array("#REVIEW#" => TruncateText(strip_tags($arFields['POST_MESSAGE']), 25)));
                        }
                    }
                }
            }
        }
    }
    private function GetPropInfo($propID=array()){
        $result=array();
        if(sizeof($propID)>0 && $this->INSTALL_MODULES['iblock']){
            foreach($propID as $prID){
                $properties=\CIBlockProperty::GetList(array("sort"=>"asc","name"=>"asc"),array("ACTIVE"=>"Y","ID"=>$prID));
                while($prop=$properties->GetNext()){
                    $result[$prop['IBLOCK_ID']]=$prop['ID'];
                }
            }
            return $result;
        }else return false;
    }
    public function GetCountBonus($Offers){
        $bonus=0;$summa=0;global $USER;
		$summas=true;
		$option=$this->SETTINGS['ORDER']['OPTION'];
		if ($option['SUMMAS']['ACTIVE'] == 'Y') {
                $summas = $this->SearchOrderPeriod($option['SUMMAS']['COUNT'], $option['SUMMAS']['TIME'], $arOrder['USER_ID']);
        }
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['catalog'] && $this->INSTALL_MODULES['iblock'] && $summas) {
            $offerID = array();
            $OfferSum = array();
            $offerSection = array();
            foreach($Offers['OFFERS'] as $offer){
                $offerID[] = $offer['PRODUCT_ID'];
                $OfferSum[$offer['PRODUCT_ID']] = $this->SETTINGS['OTHER']['OPTION']['USEQUANTITY']=='Y' ? $offer['PRICE']*$offer['QUANTITY'] : $offer['PRICE'] ;
                $summa+=$this->SETTINGS['OTHER']['OPTION']['USEQUANTITY']=='Y' ? $offer['PRICE']*$offer['QUANTITY'] : $offer['PRICE'] ;
                $db_old_groups = \CIBlockElement::GetElementGroups($offer['PRODUCT_ID'],true);
                while ($ar_group = $db_old_groups->Fetch()) {
                    $offerSection[$offer['PRODUCT_ID']][] = $ar_group['ID'];
                }
            }
			$bns = array();
            $bonus = 0;
            $pay = $Offers['PAY_SYSTEM_ID'];
            $ppl=strpos($Offers['DELIVERY_ID'],":");
            if($ppl===false)
                $delivery = $Offers['DELIVERY_ID'];
            else{
                $qaz=explode(":",$Offers['DELIVERY_ID']);
                $delivery=$qaz[0];
            }
            $bad = false;
            if ($pay == "" && $delivery == "") {
                $bonus = $this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'];
                $bad = true;
            }
            $ok = false;
            if (sizeof($this->SETTINGS['OTHER']['OPTION']['OFFER']) > 0) {
                $this->SETTINGS['OTHER']['OPTION']['OFFER'] = array_filter($this->SETTINGS['OTHER']['OPTION']['OFFER']);
                foreach ($this->SETTINGS['OTHER']['OPTION']['OFFER'] as $value) {
                    $l = \CCatalogSKU::GetProductInfo($value);
                    if ($l) unset($offerID[array_search($l['ID'], $offerID)]);
                    else unset($offerID[array_search($value, $offerID)]);
                    unset($offerID[array_search($value, $offerID)]);
                    $summa -= $OfferSum[$value];
                }
            }
			
            if (is_array($this->SETTINGS['ORDER']['OPTION']['SECTION']) && sizeof($this->SETTINGS['ORDER']['OPTION']['SECTION']) > 0) {
                foreach ($offerSection as $vls => $idof) {
                    $tmp_ar=array_intersect($idof, $this->SETTINGS['ORDER']['OPTION']['SECTION']);
                    if(empty($tmp_ar)){
                        $summa -= $OfferSum[$vls];
                        unset($offerID[array_search($vls, $offerID)]);
                    }
                }
            }
            if ($this->SETTINGS['OTHER']['OPTION']['DISCOUNT'] == 'Y') {

                $newOffers = $this->CheckDiscount($offerID);
                if (sizeof($newOffers) > 0) {
                    foreach ($newOffers as $idf => $bol) {
                        if ($bol!="") {
                            unset($offerID[array_search($idf, $offerID)]);
                            $summa -= $OfferSum[$idf];
                        }
                    }
                }
            }
            if (!empty($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'])) {
                $bonus = $this->GetBonusNum($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                $ok = true;
            }
            if (!is_int($pay)) $pay = "'" . $pay . "'";
            if (!is_int($delivery)) $delivery = "'" . $delivery . "'";
            if (sizeof($this->SETTINGS["FULL"]["ORDER" . $this->suffix]) > 0) {
                foreach ($this->SETTINGS["FULL"]["ORDER" . $this->suffix] as $fltr) {
                    $stroka = "";
                    if (!empty($fltr['ORDER_OT'])) {
                        $stroka .= $summa . ">=" . $fltr['ORDER_OT'];
                    } else {
                        //$stroka .= $summa . " && ";
                    }
                    if (!empty($fltr['ORDER_DO'])) {
                        $stroka .= "&& ".$summa . "<=" . $fltr['ORDER_DO'];
                    } else {
                        //$stroka .= $summa . " && ";
                    }
                    if ($pay!="" && !empty($fltr['PAY']) && !$bad) {
                        $stroka .= "&& in_array(" . $pay . ",array(" . implode(",", array_filter($fltr['PAY'])) . "))";
                    } else {
                     //   $stroka .= $pay . " && ";
                    }
                    if ($delivery!="" & !empty($fltr['DELIVERY']) && !$bad) {
                        $stroka .= "&& in_array(" . $delivery . ",array(" . implode(",", array_filter($fltr['DELIVERY'])) . "))";
                    } else {
                       // $stroka .= $delivery;
                    }
                    if ($stroka && eval("return " . $stroka . ";")) {
                        $bns[] = $this->GetBonusNum($fltr['ORDER_BONUS'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                        $ok = true;
                    } elseif ($stroka == "" || empty($fltr['ORDER_BONUS']) || !eval("return " . $stroka . ";") && !empty($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'])) {
                        $bonus = $this->GetBonusNum($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                        $ok = true;
                    }
                }
            }
            if (is_array($this->SETTINGS['FULL']['POROG' . $this->suffix]) && sizeof($this->SETTINGS['FULL']['POROG' . $this->suffix]) > 0) {
                foreach ($this->SETTINGS['FULL']['POROG' . $this->suffix] as $prg) {
                    if ($this->SearchOrderPeriod($prg['SUMMA'], $prg['PERIOD'], $USER->GetID()) && $prg['BONUS_L'] != "") {
                        $bonus = $this->GetBonusNum($prg['BONUS_L'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                        $ok = true;
                    }
                }
            }
            if($this->SETTINGS['ORDER']['OPTION']['PERCENTPRICE']['ACTIVE']=='Y'){
                $bnsq=0;
                $percent=$this->SETTINGS['ORDER']['OPTION']['PERCENTPRICE']['PERCENT'];
                foreach($offerID as $ofID){
                    $price=$OfferSum[$ofID];
                    $bnsq+=$price*($percent/100);
                }
                $bonus=$bnsq;
            }
			if($bb->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT']!=''){
					$percent=$bb->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'];
                   $bonus=$bb->GetBonusNum($percent,$summa,false);
					
				}
            if ($this->SETTINGS['ORDER']['OPTION']['PROPERTY']['ACTIVE'] == 'Y' && sizeof($this->SETTINGS['ORDER']['OPTION']['PROPERTY']['ID']) > 0) {
                $bonus = $this->GetBonusFromProp($offerID);
            } else {
                if (sizeof($bns) > 0) {
                    asort($bns);
                    $bonus = $bns[0];
                }
            }
            return $this->declOfNum(intval($bonus));
        }
    }
    private function GetBonusFromProp($offer,$counts=array()){
        $fields=$this->GetPropInfo($this->SETTINGS['ORDER']['OPTION']['PROPERTY']['ID']);
        $bonus=0;
        if(is_array($offer) && sizeof($offer)>0 && $this->INSTALL_MODULES['iblock']){
            foreach($offer as $id) {
                $temp=\CIBlockElement::GetList(array(),array('ID'=>$id),false,array('nTop'=>1),array())->Fetch();
                $iblock_id = intval($temp['IBLOCK_ID']);
                if($fields[$iblock_id]!=""){
                    $prID=$fields[$iblock_id];
                }else{
                    $tmp=$this->GetOtherData($temp['IBLOCK_ID'],$id);
                    $iblock_id=$tmp['IB'];
                    $prID=$tmp['PROPID'];
                    $id=$tmp['ELEMENT_ID'];
                }
				$bns=0;
                if($prID){
                    $db_props=\CIBlockElement::GetProperty($iblock_id,$id,array(),array("ID"=>$prID))->Fetch();
                    if($db_props['VALUE']!=""){
                        $bns+=$db_props['VALUE'];
						if(!empty($counts) && isset($counts[$id])){
								$bns=$bns*$counts[$id];
						}
                    }else{
                        unset($tmp);
                        $tmp=$this->GetOtherData($iblock_id,$id);
                        $tmp=$this->GetOtherData($temp['IBLOCK_ID'],$id);
                        $iblock_id=$tmp['IB'];
                        $prID=$fields[$iblock_id];
                        $id=$tmp['ELEMENT_ID'];
                        $db_props=\CIBlockElement::GetProperty($iblock_id,$id,array(),array("ID"=>$prID))->Fetch();
                        if(!empty($db_props['VALUE'])){
                            $bns=$db_props['VALUE'];
							if(!empty($counts) && isset($counts[$id])){
								$bns=$bns*$counts[$id];
							}
                        }
                    }
                }
				$bonus+=$bns;
            }
        }
        unset($iblock_id,$db_props,$fields);
        return $bonus;
    }
    private function GetOtherData($ib,$id){
        if($this->INSTALL_MODULES['catalog']){
            $mxResult = \CCatalogSKU::GetInfoByOfferIBlock($ib);
            if($mxResult){
                $prID=$fields[$mxResult['PRODUCT_IBLOCK_ID']];
                $iblock_id=$mxResult['PRODUCT_IBLOCK_ID'];
                $idd=$mxResult = \CCatalogSku::GetProductInfo($id);
                if($idd){
                    $id=$idd['ID'];
                }else $prID="";
            }else $prID="";
        }else $prID="";
        return array('IB'=>$iblock_id,'PROPID'=>$prID,'ELEMENT_ID'=>$id);
    }
     public function OnSubscribe(&$arFields){
		
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['subscribe'] && $arFields['CONFIRMED']=='Y') {
            $rubric = array();
            global $USER;
            if ($this->SETTINGS['SUBSCRIBE']['OPTION']['ACTIVE'] == 'Y') {
                $arFilter = array("ACTIVE" => "Y", "LID" => $this->LID, "ID" => $arFields["RUB_ID"]);
                $arOrder = array("SORT" => "asc", "NAME" => "asc");
                $rsRubric = \CRubric::GetList($arOrder, $arFilter);
                while ($arRubric = $rsRubric->GetNext()) {
                    $rubric[] = $arRubric['NAME'];
                }
                $bonus = $this->SETTINGS['SUBSCRIBE']['OPTION']['COUNT'];
                $bonus = intval($bonus);
                $bonus = $bonus * sizeof($rubric);
                if ($arFields['USER_ID'] == "") $UID = $USER->GetID(); else $UID = $arFields['USER_ID'];
				if($UID)
					$this->AddBonus($UID, $this->LID, $bonus, SUBSCRIBE, array("#SUBSCRIBE#" => implode(",", $rubric)));
            }
        }
    }
    public function OnDeleteSubscribe(&$arFields){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $rubric = array();
            if ($this->SETTINGS['SUBSCRIBE']['OPTION']['ACTIVE'] == "Y" && $this->INSTALL_MODULES['subscribe']) {
                if ($arFields['ACTIVE'] == 'N') {
                    $cData = new \CSubscription();
                    $rsData = $cData->GetList(array("ID" => "ASC"), array("ID" => $arFields['ID']))->Fetch();
                    $rsRubric = $cData->GetRubricArray($rsData['ID']);
                    $arOrder = array('SORT' => 'asc', 'NAME' => 'asc');
                    $arFilter = array('ACTIVE' => "Y", 'LID' => $this->LID, "ID" => current($rsRubric));
                    $rsR = \CRubric::GetList($arOrder, $arFilter);
                    while ($arRub = $rsR->GetNext()) {
                        $rubric[] = $arRub['NAME'];
                    }
                    $bonus = intval($this->SETTINGS['SUBSCRIBE']['OPTION']['COUNT']);
                    $bonus = $bonus * sizeof($rubric);
                    $this->AddBonus($rsData['USER_ID'], $this->LID, -$bonus, SUBSCRIBE_DEL, array("#SUBSCRIBE#" => implode(",", $rubric)));
                } elseif ($arFields['ACTIVE'] == "Y") {
                    $this->OnSubscribe($arFields);
                }
            }
        }
    }
    private function CreateFields($item){
        if($this->INSTALL_MODULES['catalog'] && $this->INSTALL_MODULES['iblock']) {
            $sec = array();
            $l = \CCatalogSKU::GetProductInfo($item['ID'], $item['IBLOCK_ID']);
            if ($l) {
                $db_old_groups = \CIBlockElement::GetElementGroups($l['ID'], true);
                while ($ar_group = $db_old_groups->Fetch()) {
                    $sec[] = $ar_group['ID'];
                }
            }
            $prop = $item['PROPERTIES'];
            unset($item['PROPERTIES']);
            $newItem = $item;
            $newItem['SECTION_ID'] = $sec;
            unset($sec);
            foreach ($prop as $props) {
                $newItem["PROPERTY_" . $props['ID'] . "_VALUE"] = $props['VALUE'];
            }
            return $newItem;
        }
    }
    public function DeleteBonus($BonusID){ //all record delete bonus
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $user = array();
            $newBonus = 0;
            $res=call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array(
                "filter" => array('ID' => $BonusID),
            ));
            if ($bon = $res->fetch()) {
                if ($bon['ACTIVE'] == "Y") {
                    $userID = $bon['USERID'];
                    $user = $this->GetUserInfo($userID);
                    $siteID = $bon['LID'] ? $bon['LID'] : $this->LID;
                    call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"delete"),array($bon['ID']));
                    $userBonus = Bonus\AccountTable::getList(array(
                        'filter' => array('USER_ID' => $userID),
                    ));
                    if ($USRAccount = $userBonus->fetch()) {
                        $currentBonus = $USRAccount['CURRENT_BUDGET'];
                        $newFields = $USRAccount;
                        $newBonus = floatval($currentBonus - $bon['BONUS']);
                        $newFields['CURRENT_BUDGET'] = $newBonus;
                        Bonus\AccountTable::update($USRAccount['ID'], $newFields);
                    }
                }
                if ($this->SETTINGS["NOTIFICATION"]["OPTION"] == "Y") {
                    $arFields = array(
                        "TYPE" => $bon['TYPE'],
                        'USERNAME' => $user['FIO'],
                        'USEREMAIL' => $user['EMAIL'],
                        'site' => $bon['LID'],
                        'EVENT' => 'DELETE',
                        'DESCRIPTION' => $bon['DESCRIPTION'],
                        'ACTIVE_FROM' => $bon['ACTIVE_FROM'],
                        'ACTIVE_TO' => $bon['ACTIVE_TO'],
                        'BONUS' => $bon['BONUS'],
                        'BONUS_ALL' => $this->declOfNum($newBonus)
                    );
                    $this->SendNotification($arFields, true, $this->SETTINGS['NOTIFICATIONSMS']['OPTION'] == 'Y' ? true : false);
                }
            }
            unset($res, $bon, $userID, $siteID, $userBonus, $USRAccount, $currentBonus, $newFields, $newBonus);
        }
    }
    public function BonusActive(){
        @set_time_limit(0);
        ignore_user_abort(true);
        global $USER;
        foreach($this->SETTINGS['SITE_ON'] as $pl=>$sid) {
            if ($sid == "Y") {
                $this->LID=$pl;
                $userID = 0;
                $currentbudget = 0;
                if (!is_object($USER)) $USER = new \CUser();
                $res=call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array(array(
                    'filter' => array('ACTIVE' => 'N', 'ACTIVE_FROM' => Main\Type\DateTime::convertFormatToPhp(date('d.m.Y', time())))),
                ));
                while ($bon = $res->fetch()) {
                    $newFields = $bon;
                    $newFields['ACTIVE'] = 'Y';
                    $userID = $bon['USERID'];
                    $bonus = $bon['BONUS'];
                    call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"update"),array($bon['ID'], $newFields));
                    $BonusAccount = Bonus\AccountTable::getList(array(
                        'filter' => array('USER_ID' => $userID),
                    ));
                    if ($bonAc = $BonusAccount->fetch()) {
                        $currentbudget = floatval($bonAc['CURRENT_BUDGET'] + $bonus);
                        $newFlds = $bonAc;
                        $newFlds['CURRENT_BUDGET'] = $currentbudget;
                        Bonus\AccountTable::update($bonAc['ID'], $newFlds);
                    }else{
                        if ($this->ACCOUNT_BITRIX) {
                            $cur = $this->ReturnCurrency($bonus);
                            \CSaleUserAccount::UpdateAccount($userID, $bonus , $cur, "");
                        } else {
                            $ACC_FIELDS = array(
                                'USER_ID' => $userID,
                                'CURRENT_BUDGET' => $bonus,
                                'CURRENCY' => $this->ReturnCurrency($bonus),
                                'NOTES' => '',
                            );
                            Bonus\AccountTable::add($ACC_FIELDS);
                        }
                    }
                }
                if ($this->SETTINGS["NOTIFICATION"]["OPTION"] == "Y") {
                    $user = $this->GetUserInfo($userID);
                    $arFields = array(
                        "TYPE" => $bon['TYPE'],
                        'USERNAME' => $user['FIO'],
                        'USEREMAIL' => $user['EMAIL'],
                        'site' => $bon['LID'],
                        'EVENT' => 'ADD',
                        'DESCRIPTION' => $bon['DESCRIPTION'],
                        'ACTIVE_FROM' => $bon['ACTIVE_FROM'],
                        'ACTIVE_TO' => $bon['ACTIVE_TO'],
                        'BONUS' => $bon['BONUS'],
                        'BONUS_ALL' => $this->declOfNum($currentbudget)
                    );
                    $this->SendNotification($arFields, true, $this->SETTINGS['NOTIFICATIONSMS']['OPTION'] == 'Y' ? true : false);
                }
            }
        }
    }
    public function BirthDay(){
        @set_time_limit(0);
        ignore_user_abort(true);
        global $USER;
        foreach($this->SETTINGS['SITE_ON'] as $pl=>$sid) {
            if ($sid == "Y") {
                $this->LID=$pl;
                if (!is_object($USER)) $USER = new \CUser();
                $option = $this->SETTINGS['BITHDAY']['OPTION'];
                if ($option['ACTIVE'] == 'Y') {
                    $rsUsers = Main\UserTable::getList(array(
                        'filter' => array('ACTIVE' => 'Y', 'PERSONAL_BIRTHDAY' => new \Bitrix\Main\Type\DateTime()),
                        'select' => array('ID', 'LID'),
                    ));
                    $years = date("Y");
                    while ($arUser = $rsUsers->fetch()) {
                        $ok = true;
                        $bonRes=call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array(
                            'filter' => array("USERID" => $arUser['ID'], 'TYPE' => 5),
                        ));
                        while ($bon = $bonRes->fetch()) {
                            $str = explode(" ", $bon["TIMESTAMP_X"]);
                            $str = explode(".", $str[0]);
                            if ($str[2] == $years) $ok = false; else $ok = true;
                        }
                        if ($ok) {
                            $lid = $arUser['LID'] ? $arUser['LID'] : $this->LID;
                            $this->AddBonus($arUser['ID'], $lid, $option['COUNT'], BIRTHDAY,array('#DATE#'=>date("d-m-Y")));
                        }
                    }
                }
                unset($option, $USER, $years, $arFilter, $rsUsers, $arUser, $bon, $bonRes, $lid, $str);
            }
        }
    }
    public function SearchOrderPeriod($summa,$type,$userid){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['sale']) {
            global $DB;
            if ($summa != "") {
                $old = AddToTimeStamp($this->GetPeriodTime(1, $type, true));
                $arFilter = array(
                    "USER_ID" => $userid,
                    ">=DATE_INSERT" => date($DB->DateFormatToPHP(\CSite::GetDateFormat("DD.MM.YYYY HH:MI:SS")), $old),
                    "<=DATE_INSERT" => date($DB->DateFormatToPHP(\CSite::GetDateFormat("DD.MM.YYYY HH:MI:SS")), time()),
                );
                $db_sales=\CSaleOrder::GetList(array(),$arFilter,false,false,array());
                $summ = 0;
                while ($ar_sales = $db_sales->Fetch()) {
                    $summ += $ar_sales['PRICE'] + $ar_sales['PRICE_DELIVERY'];
                }
            }
            return ($summ >= $summa);
        }
    }
    public function CheckOrderFilter($Order){
		
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['iblock'] && $this->INSTALL_MODULES['sale'] && $this->INSTALL_MODULES['catalog']) {
            $offerID = array();
            $OfferSum = array();
            $offerSection = array();
            $res =\CSaleBasket::GetList(
                array(),array('ORDER_ID' => $Order['ID']),false,false,
                array("ID", "CALLBACK_FUNC", "MODULE","PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT")
            );
			$counts=array();
            while ($arItem = $res->Fetch()) {
                $offerID[] = $arItem['PRODUCT_ID'];
                $OfferSum[$arItem['PRODUCT_ID']] = $this->SETTINGS['OTHER']['OPTION']['USEQUANTITY']=='Y' ? $arItem['PRICE']*$arItem['QUANTITY'] : $arItem['PRICE'] ;
				if($this->SETTINGS['OTHER']['OPTION']['USEQUANTITY']=='Y'){
					$counts[$arItem['PRODUCT_ID']]=$arItem['QUANTITY'];
				}
                $db_old_groups = \CIBlockElement::GetElementGroups($arItem['PRODUCT_ID']);
                while ($ar_group = $db_old_groups->Fetch()) {
                    $offerSection[$arItem['PRODUCT_ID']][] = $ar_group['ID'];
                }
            }
            $bns = array();
            $bonus = 0;
            $summa = $Order['PRICE'];
            $pay = $Order['PAY_SYSTEM_ID'];
            $ppl=strpos($Order['DELIVERY_ID'],":");
            if($ppl===false)
                $delivery = $Order['DELIVERY_ID'];
            else{
                $qaz=explode(":",$Order['DELIVERY_ID']);
                $delivery=$qaz[0];
            }
            $bad = false;
            if ($pay == "" && $delivery == "") {
                $bonus = $this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'];
                $bad = true;
            }
            $ok = false;
            if (sizeof($this->SETTINGS['OTHER']['OPTION']['OFFER']) > 0) {
                $this->SETTINGS['OTHER']['OPTION']['OFFER'] = array_filter($this->SETTINGS['OTHER']['OPTION']['OFFER']);
                foreach ($this->SETTINGS['OTHER']['OPTION']['OFFER'] as $value) {
                    $l = \CCatalogSKU::GetProductInfo($value);
                    if ($l) unset($offerID[array_search($l['ID'], $offerID)]);
                    else unset($offerID[array_search($value, $offerID)]);
                    unset($offerID[array_search($value, $offerID)]);
                    $summa -= $OfferSum[$value];
                }
            }
            if (is_array($this->SETTINGS['ORDER']['OPTION']['SECTION']) && sizeof($this->SETTINGS['ORDER']['OPTION']['SECTION']) > 0) {
                foreach ($offerSection as $vls => $idof) {
                    $tmp_ar=array_intersect($idof, $this->SETTINGS['ORDER']['OPTION']['SECTION']);
                    if(empty($tmp_ar)){
                        $summa -= $OfferSum[$vls];
                        unset($offerID[array_search($vls, $offerID)]);
                    }

                }
            }
            if ($this->SETTINGS['OTHER']['OPTION']['DISCOUNT'] == 'Y') {
                $newOffers = $this->CheckDiscount($offerID);
                if (sizeof($newOffers) > 0) {
                    foreach ($newOffers as $idf => $bol) {
                        if ($bol) {
                            unset($offerID[array_search($idf, $offerID)]);
                            $summa -= $OfferSum[$idf];
                        }
                    }
                }
            }
            if (!empty($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'])) {
                $bonus = $this->GetBonusNum($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                $ok = true;
            }
            if (!is_int($pay)) $pay = "'" . $pay . "'";
            if (!is_int($delivery)) $delivery = "'" . $delivery . "'";
            if (sizeof($this->SETTINGS["FULL"]["ORDER" . $this->suffix]) > 0 && !$bad) {
                foreach ($this->SETTINGS["FULL"]["ORDER" . $this->suffix] as $fltr) {
                    $stroka = "";
                    if (!empty($fltr['ORDER_OT'])) {
                        $stroka .= $summa . ">=" . $fltr['ORDER_OT'] . " && ";
                    } else {
                        $stroka .= $summa . " && ";
                    }
                    if (!empty($fltr['ORDER_DO'])) {
                        $stroka .= $summa . "<=" . $fltr['ORDER_DO'] . " && ";
                    } else {
                        $stroka .= $summa . " && ";
                    }
                    if (!empty($fltr['PAY'])) {
                        $stroka .= "in_array(" . $pay . ",array(" . implode(",", array_filter($fltr['PAY'])) . ")) && ";
                    } else {
                        $stroka .= $pay . " && ";
                    }
                    if (!empty($fltr['DELIVERY'])) {
                        $stroka .= "in_array(" . $delivery . ",array(" . implode(",", array_filter($fltr['DELIVERY'])) . "))";
                    } else {
                        $stroka .= $delivery;
                    }
                    if ($stroka && eval("return " . $stroka . ";")) {
                        $bns[] = $this->GetBonusNum($fltr['ORDER_BONUS'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                        $ok = true;
                    } elseif ($stroka == "" || empty($fltr['ORDER_BONUS']) || !eval("return " . $stroka . ";") && !empty($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'])) {
                        $bonus = $this->GetBonusNum($this->SETTINGS['ORDER']['OPTION']['ORDER']['COUNT'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                        $ok = true;
                    }
                }
            }
            if (is_array($this->SETTINGS['FULL']['POROG' . $this->suffix]) && sizeof($this->SETTINGS['FULL']['POROG' . $this->suffix]) > 0) {
                foreach ($this->SETTINGS['FULL']['POROG' . $this->suffix] as $prg) {
                    if ($this->SearchOrderPeriod($prg['SUMMA'], $prg['PERIOD'], $Order['USER_ID']) && $prg['BONUS_L'] != "") {
                        $bonus = $this->GetBonusNum($prg['BONUS_L'], $summa, $this->SETTINGS['ORDER']['OPTION']['SUMM_TOTAL']);
                        $ok = true;
                    }
                }
            }

            if($this->SETTINGS['ORDER']['OPTION']['PERCENTPRICE']['ACTIVE']=='Y'){
                $bnsq=0;
                $percent=$this->SETTINGS['ORDER']['OPTION']['PERCENTPRICE']['PERCENT'];
                foreach($offerID as $ofID){
                    $price=$OfferSum[$ofID];
                    $bnsq+=$price*($percent/100);
                }
                $bonus=$bnsq;
            }
            if ($this->SETTINGS['ORDER']['OPTION']['PROPERTY']['ACTIVE'] == 'Y' && sizeof($this->SETTINGS['ORDER']['OPTION']['PROPERTY']['ID']) > 0) {
                $bonus = $this->GetBonusFromProp($offerID,$counts);
				
            } else {
                if (sizeof($bns) > 0) {
                    asort($bns);
                    $bonus = $bns[0];
                }
            }
            return intval($bonus);
        }
    }

    private function GetPaySYS($fields){
        $itemsFromDbList = \Bitrix\Sale\Internals\PaymentTable::getList(
            array(
                "filter" => array("ORDER_ID" => $fields['ORDER_ID']),
                "select"=>array("ID","ORDER_ID","SUM","PAID")
            )
        );
        $PAYSYS=array();
        while ($itemsFromDbItem = $itemsFromDbList->fetch()) {
            $PAYSYS[$itemsFromDbItem['ID']]=$fields['PAY_ID']==$itemsFromDbItem['ID'] ? $fields['VALUE'] : $itemsFromDbItem['PAID'];
        }
        return $PAYSYS;
    }
    public function OnSalePaymentD7($fields=array()){
		if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['sale'] && $this->D7) {
                $PAYSYS=$this->GetPaySYS($fields);
                if($PAYSYS[$fields['PAY_ID']]=='Y'){
                    $this->OnOrder($fields['ORDER_ID'],'Y');
                }else{
                    $this->OnCancelOrder($fields['ORDER_ID'],'Y','cancel');
                }
        }
    }
    public function OnOrder($ID,$val){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['sale']) {
            $saleperson = true;
            $usergroup = true;
            $summas = true;
            $arOrder = \CSaleOrder::GetByID($ID);
            $this->LID = $arOrder['LID'];
            $option = $this->SETTINGS['ORDER']['OPTION'];
            if (array_key_exists("SUMM_WITHOUTDELIVERY", $option) && $option["SUMM_WITHOUTDELIVERY"] == "Y") {
                $arOrder["PRICE"] = $arOrder["PRICE"] - $arOrder["PRICE_DELIVERY"];
            }
            if (sizeof($option['SALEPERSON']) > 0) {
                $saleperson = (in_array($arOrder['PERSON_TYPE_ID'], $option['SALEPERSON']));
            }
            if (sizeof($option['USERGROUP']) > 0) {
                $arGroup = \CUser::GetUserGroup($arOrder['USER_ID']);
                $inar=false;
				foreach($arGroup as $asg){
				if(in_array($asg,$option['USERGROUP'])) {$usergroup=true;break;}
					else {$usergroup=false;}
				}
            }
            if ($option['SUMMAS']['ACTIVE'] == 'Y') {
                $summas = $this->SearchOrderPeriod($option['SUMMAS']['COUNT'], $option['SUMMAS']['TIME'], $arOrder['USER_ID']);
            }
			
            if ($saleperson && $usergroup && $summas) {
                if ($val=="Y" || $arOrder['PAYED'] == "Y") {
                    if ($this->OnFirstOrder($arOrder)==false) {
                        $bns = $this->CheckOrderFilter($arOrder);
                        if ($bns > 0) {
                            $this->AddBonus($arOrder['USER_ID'], $arOrder['LID'], $bns, ORDER, array("#ORDER_ID#" => $arOrder['ID'], "#SUMM#" => CurrencyFormat($arOrder['PRICE'], $arOrder['CURRENCY'])));
                        }
                    }
                }
            }
            unset($option, $arOrder, $arGroup);
        }
    }
    private function CheckDiscount($offer){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y" && $this->INSTALL_MODULES['iblock'] && $this->INSTALL_MODULES['catalog']) {
            if (sizeof($offer) == 0) return 0;
            $iblockid = array();
            $disc = array();
            $filter = array();
            $offer1 = array();
            foreach ($offer as $id) {
                $iblockid[] = intval(\CIBlockElement::GetIBlockByID($id));
            }
            $res = \CIBlockElement::GetList(array('ID' => "asc"), array('IBLOCK_ID' => $iblockid, "ID" => $offer), false, false, array());
            while ($b = $res->GetNextElement()) {
                $a = $b->GetFields();
                $a['PROPERTIES'] = $b->GetProperties();
                $offer1[] = $this->CreateFields($a);
            }
            $dbProductDiscounts = \CCatalogDiscount::GetList(array("SORT" => "ASC"), array("ACTIVE" => "Y"), false, false, array());
            while ($arProductDiscount = $dbProductDiscounts->Fetch()) {
                $arProductDiscount['CONDITIONS'] = unserialize($arProductDiscount['CONDITIONS']);
                $disc[] = $arProductDiscount;
            }
            $obCond = new \CCatalogCondTree();
            $boolsCond = $obCond->Init(BT_COND_MODE_GENERATE, BT_COND_BUILD_CATALOG);
            if ($boolsCond)
                $filter = array();
            foreach ($disc as $dsc) {
                if($dsc['UNPACK']!='((1 == 1))')
                    $filter[] = $obCond->Generate($dsc['CONDITIONS'], array('FIELD' => '$arItems'));
            }
            $newOffers = array();
            foreach ($offer1 as $arItems) {

                foreach ($filter as $fltr) {
                    $newOffers[$arItems['ID']] = eval('return ' . $fltr . ';');
                    if ($newOffers[$arItems['ID']]) break;
                }
            }
            $newOffers = array_filter($newOffers);
            unset($res, $b, $a, $offer, $offer1, $dbProductDiscounts, $arProductDiscount, $disc, $obCond, $boolsCond, $filter);
            return $newOffers;
        }
    }
    public function BonusStatistic(){
        @set_time_limit(0);
        ignore_user_abort(true);
        global $USER;
        foreach($this->SETTINGS['SITE_ON'] as $pl=>$sid) {
            if ($sid== "Y" && $this->INSTALL_MODULES['sale']) {
                $this->LID=$pl;
                if (!is_object($USER)) $USER = new \CUser();
                if ($this->ACCOUNT_BITRIX) {
                    $accountBon = \CSaleUserAccount::GetList(array(), array(">CURRENT_BUDGET" => 0), false, false, array());
                    while ($accBon = $accountBon->GetNext()) {
                        if ($accBon['CURRENCY'] == $this->ReturnCurrency(1)) {
                            $user = $this->GetUserInfo($accBon['USER_ID']);
                            $siteid = $user['LID'] ? $user['LID'] : $this->LID;
                            $FLDS[] = array(
                                'TYPE' => Loc::getMessage("CVBCH_MAIN_BONUS_MONTHSTATIC"),
                                'USERNAME' => $user['FIO'],
                                'USEREMAIL' => $user['EMAIL'],
                                'site' => $siteid,
                                'EVENT' => 'MONTH',
                                'DESCRIPTION' => Loc::getMessage("CVBCH_MAIN_BONUS_MONTH_STAT"),
                                'ACTIVE_FROM' => "",
                                'ACTIVE_TO' => "",
                                'BONUS' => "",
                                'BONUS_ALL' => $this->declOfNum($accBon['CURRENT_BUDGET']),
                            );
                        }
                    }

                } else {
                    $accountBon = Bonus\AccountTable::getlist(array(
                        'filter' => array(),
                    ));
                    while ($accBon = $accountBon->fetch()) {
                        $user = $this->GetUserInfo($accBon['USER_ID']);
                        if ($accBon['CURRENCY'] == $this->ReturnCurrency(1)) {
                            $user = $this->GetUserInfo($accBon['USER_ID']);
                            $siteid = $user['LID'] ? $user['LID'] : $this->LID;
                            $FLDS[] = array(
                                'TYPE' => Loc::getMessage("CVBCH_MAIN_BONUS_MONTHSTATIC"),
                                'USERNAME' => $user['FIO'],
                                'USEREMAIL' => $user['EMAIL'],
                                'site' => $siteid,
                                'EVENT' => 'MONTH',
                                'DESCRIPTION' => Loc::getMessage("CVBCH_MAIN_BONUS_MONTH_STAT"),
                                'ACTIVE_FROM' => "",
                                'ACTIVE_TO' => "",
                                'BONUS' => "",
                                'BONUS_ALL' => $this->declOfNum($accBon['CURRENT_BUDGET']),
                            );
                        }
                    }
                }
                unset($accBon, $accountBon, $user);
                if ($this->SETTINGS['NOTIFICATION']['OPTION'] == "Y") {
                    if (sizeof($FLDS) > 0) {
                        foreach ($FLDS as $arFields) {
                            $this->SendNotification($arFields, true, $this->SETTINGS['NOTIFICATIONSMS']['OPTION'] == "Y" ? true : false);
                        }
                    }
                }
                unset($arFields, $FLDS);
            }
        }
    }

    public function RefreshPayedFromAccount(&$arResult){
		$arResult['PAYED_FROM_ACCOUNT_FORMATED']='';
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
			$sum_total=$arResult['ORDER_PRICE']+$arResult['DELIVERY_PRICE'];
            global $USER,$GLOBALS;
            $option = $this->SETTINGS['ORDER']['OPTION'];
            $currentBudget = 0;
            
			if (array_key_exists("SUMM_WITHOUTDELIVERY", $option) && $option["SUMM_WITHOUTDELIVERY"] == "Y") {
                $orderTotalSum=$sum_total-$arResult['DELIVERY_PRICE'];
            }else{
				$orderTotalSum=$sum_total;
			}
            
			if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] && ($arResult['TYPEPAY']=='SYSTEM_BIGBONUS_PAY' || $arResult['TYPEPAY']=='SYSTEM_PAY')){
                $orderTotalSum-=intval(str_replace(" ","",$arResult['PAYED_FROM_ACCOUNT_FORMATED']));
            }
            $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $USER->GetID(), 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
            if ($arUserAccount = $dbUserAccount->GetNext()) {
                $currentBudget = $arUserAccount['CURRENT_BUDGET'];
                $currentBudgetAccount = $arUserAccount['CURRENT_BUDGET'];
            }
            if($currentBudgetAccount<0) $arResult['PAY_FROM_ACCOUNT1']='N';else $arResult['PAY_FROM_ACCOUNT1']='Y';
            $dbUserAccount = Bonus\AccountTable::getList(array(
                'filter' => array('USER_ID' => $USER->GetID()),
            ));
            if ($arUserAccount = $dbUserAccount->fetch())
                $currentBonusBudget = $arUserAccount["CURRENT_BUDGET"];
            else $currentBonusBudget=0;

            if ($option["USERPAY"]["INPUT"] == "Y") {
                $max_pay=$this->GetBonusNum($option['USERPAY']['COUNT'],$orderTotalSum,false);
                $arResult['PAY_FROM_ACCOUNT']='N';
                if (!empty($_POST['ACCOUNT_CNT']) && $_POST['ACCOUNT_CNT'] != "" && $_POST['ACCOUNT_CNT'] != 0) {
                    $lp1 = intval($_POST['ACCOUNT_CNT']);
                    if ($lp1 > $currentBudgetAccount) {
                        $lp1 = intval($currentBudgetAccount);
                    }
                    if ($lp1 > $orderTotalSum)
                        $_POST['ACCOUNT_CNT'] = $lp1 = $orderTotalSum;
                    if($lp1>$max_pay)
                        $lp1=intval($max_pay);
                }else{
                    $max_pay=$this->GetBonusNum($option['USERPAY']['COUNT'],$orderTotalSum,false);
                    if($max_pay>$currentBudgetAccount){
                        $lp1=intval($currentBudgetAccount);
                    }else{
                        $lp1=intval($max_pay);
                    }

                }
            }else{
                $lp1 = intval($this->GetBonusNum($option['USERPAY']['COUNT'],$orderTotalSum,false));
                if ($lp1 > $currentBudgetAccount) {
                    $lp1 = intval($currentBudgetAccount);
                }
                if ($lp1 > $orderTotalSum)
                    $_POST['ACCOUNT_CNT'] = $lp1 = $orderTotalSum;
            }

            if ($option["PAY"]["INPUT"] == "Y") {
                $max_pay_b = intval($this->GetBonusNum($option['PAY']['COUNT'],$orderTotalSum,false));
                if (!empty($_POST['BONUS_CNT']) && $_POST['BONUS_CNT'] != "" && $_POST['BONUS_CNT'] != 0) {
                    $lp = intval($_POST['BONUS_CNT']);
                    if ($lp > $currentBonusBudget) {
                        $lp = intval($currentBonusBudget);
                    }
                    if ($lp > $orderTotalSum)
                        $_POST['BONUS_CNT'] = $lp = $max_pay_b;
                    if($lp>$max_pay_b)
                        $lp=intval($max_pay_b);
                }else{
                    $lp=$this->GetBonusNum($option['PAY']['COUNT'],$orderTotalSum,false);
                    if($lp>$currentBonusBudget)
                        $lp=intval($currentBonusBudget);
                    else $lp=intval($lp);
                }
            }else{
                $lp = intval($this->GetBonusNum($option['PAY']['COUNT'],$orderTotalSum,false));
                if ($lp > $currentBonusBudget) {
                    $lp = intval($currentBonusBudget);
                }
                else $lp=intval($lp);
                if ($lp > $orderTotalSum)
                    $_POST['BONUS_CNT'] = $lp;
                else
                    $lp=intval($lp);
                $arResult['PAYFROMPAY_FORMATED']=$this->declOfNum($lp);
            }
            $arResult['PAYED_FROM_USERACCOUNT']=$lp1;
            $arResult['PAYFROMPAY'] = $lp;
            $arResult['PAYED_FROM_BONUS_FORMATED']="";
            $summa=$arResult['ORDER_PRICE'] + $arResult['DELIVERY_PRICE'] + $arResult['TAX_PRICE'] - $arResult['DISCOUNT_PRICE'];
            if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] && !$arResult['USER_VALS']['PAY_BONUS_ACCOUNT']) {
                $ac_summa=intval($arResult['PAYED_FROM_ACCOUNT_FORMATED']);
                $summa-=$ac_summa;
            }

            if($arResult['USER_VALS']['PAY_BONUS_ACCOUNT']=='Y') {
                $b_summa=intval($arResult['PAYFROMPAY']);
                $summa-=$b_summa;
                $GLOBALS['BONUS_PAY']=$b_summa;
                $arResult['PAYED_FROM_BONUS_FORMATED'] = $this->declOfNum($arResult['PAYFROMPAY']);
            }
            if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y') {
                $a_summa=intval($arResult['PAYED_FROM_USERACCOUNT']);
                $summa-=$a_summa;
                $GLOBALS['ACCOUNT_PAY']=$a_summa;
                $arResult['PAYED_FROM_ACCOUNT_FORMATED'] = FormatCurrency($a_summa,$arResult['BASE_LANG_CURRENCY']);
            }
            $arResult['ORDER_TOTAL_PRICE_FORMATED'] = SaleFormatCurrency($summa, $arResult['BASE_LANG_CURRENCY']);
        }
    }
    public function OnSaleComponentOrderOneStepProcess(&$arResult,&$arUserResult,&$arParams){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $option=$this->SETTINGS["ORDER"]["OPTION"];
            unset($bb);
            if ($arParams['PAY_FROM_ACCOUNT'] == "Y" && $option['PAYORDER']=="SYSTEM_PAY"){
                $arResult['PAY_BONUS_ACCOUNT'] = 'N';
                $arResult['TYPEPAY']="SYSTEM_PAY";
            }elseif ($arParams['PAY_FROM_ACCOUNT'] == "Y" && $option['PAYORDER']=="BIGBONUSPAY" && $option['PAY']['ACTIVE']=='Y'){
                $arResult['PAY_FROM_ACCOUNT'] = 'N';
                $arResult['PAY_BONUS_ACCOUNT'] = 'Y';
                $arResult['TYPEPAY']="BIGBONUS_PAY";
            }elseif ($arParams['PAY_FROM_ACCOUNT'] == "Y" && $option['PAYORDER']=="SYSTEM_BIGBONUS_PAY"){
                if($option['PAY']['ACTIVE']=='Y'){
                    $arResult['PAY_BONUS_ACCOUNT'] = 'Y';
                    $arResult['TYPEPAY']="SYSTEM_BIGBONUS_PAY";
                }
                else{
                    $arResult['PAY_BONUS_ACCOUNT'] = 'N';
                    $arResult['TYPEPAY']="SYSTEM_PAY";
                }
            }
            if ($option["USERPAY"]["INPUT"] == "Y" || $option["USERPAY"]["ACTIVE"] == "Y") {
                $arResult['PAY_FROM_ACCOUNT'] = 'N';
            }
            $currentBudget = 0;
            global $USER;
            $option = $this->SETTINGS["ORDER"]['OPTION'];
            $payorder = $option['PAY']['COUNT'];
            $payuserorder=$option['USERPAY']['COUNT'];
            if($payorder) {
                if (strpos($payorder, "%") === false) {
                    $payorder = intval($payorder);
                    $percent = false;
                } else {
                    $payorder = intval($payorder);
                    $percent = true;
                }
            }
            if($payuserorder) {
                if (strpos($payuserorder, "%") === false) {
                    $payuserorder = intval($payuserorder);
                    $percentuser = false;
                } else {
                    $payuserorder = intval($payuserorder);
                    $percentuser = true;
                }
            }
            $arResult['ORDER_PAY_PERCENT']=($percent ? $option['PAY']['COUNT'] : $this->declOfNum($option['PAY']['COUNT']));
            $arResult['ORDER_USER_PERCENT']=($percentuser ? $option['USERPAY']['COUNT'] : $this->declOfNum($option['USERPAY']['COUNT']));
            if ($option["USERPAY"]["ACTIVE"] == "Y") {
                $arResult['USERINPUT']=$option['USERPAY']['INPUT']=='Y' ? true : false;
                $orderTotalSum=($this->SETTINGS['ORDER']['OPTION']['SUMM_WITHOUTDELIVERY']=='Y' ? $arResult['ORDER_PRICE'] :$arResult['ORDER_PRICE']+$arResult['DELIVERY_PRICE']);
                $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $USER->GetID(), 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
                if ($arUserAccount = $dbUserAccount->GetNext()) {
                    $currentBudget = round($arUserAccount['CURRENT_BUDGET'], 2);
                    $arResult["CURRENT_BUDGET_FORMATED"] = $this->declOfNum($currentBudget);
                    $arResult["USER_ACCOUNT"] = $arUserAccount;
                    $arResult["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"] >= $orderTotalSum) ? $orderTotalSum : $arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]), $arResult["BASE_LANG_CURRENCY"]);
                }
            }
            if ($option["PAY"]["ACTIVE"] == "Y") {
                $orderTotalSum=($this->SETTINGS['ORDER']['OPTION']['SUMM_WITHOUTDELIVERY']=='Y' ? $arResult['ORDER_PRICE'] :$arResult['ORDER_PRICE']+$arResult['DELIVERY_PRICE']);
                if ($this->ACCOUNT_BITRIX && ($arResult['TYPEPAY']=="SYSTEM_PAY" || $arResult['TYPEPAY']=="SYSTEM_BIGBONUS_PAY")) {
                    $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $USER->GetID(), 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
                    if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $currentBudget = round($arUserAccount['CURRENT_BUDGET'], 2);
                        $arResult["CURRENT_BUDGET_FORMATED"] = $this->declOfNum($currentBudget);
                        $arResult["USER_ACCOUNT"] = $arUserAccount;
                        $arResult["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"] >= $orderTotalSum) ? $orderTotalSum : $arResult["USER_ACCOUNT"]["CURRENT_BUDGET"]), $arResult["BASE_LANG_CURRENCY"]);
                    }
                } elseif(!$this->ACCOUNT_BITRIX && ($arResult['TYPEPAY']=="BIGBONUS_PAY" || $arResult['TYPEPAY']=="SYSTEM_BIGBONUS_PAY")) {
                    $dbUserAccount = Bonus\AccountTable::getList(array(
                        'filter' => array('USER_ID' => $USER->GetID()),
                    ));
                    if ($arUserAccount = $dbUserAccount->fetch())
                        $currentBudget = round($arUserAccount["CURRENT_BUDGET"], 2);
                    $arResult["CURRENT_BONUS_BUDGET_FORMATED"] = $this->declOfNum($currentBudget);
                    $arResult["USER_ACCOUNT"] = array('USER_ID' => $USER->GetID());
                    $arResult["PAYED_FROM_BONUS_FORMATED"] = $this->declOfNum($currentBudget >= $orderTotalSum ? $orderTotalSum : $currentBudget);
                }
                if ($percent) {
                    $delta = ($orderTotalSum * ($payorder / 100));
                    $payfrompay = ($delta > $currentBudget) ? $currentBudget : $delta;
                } else {
                    $payfrompay = ($payorder > $currentBudget) ? $currentBudget : $payorder;
                }
                $arResult['BONUSINPUT']=$option['PAY']['INPUT']=='Y' ? true : false;
            }
        }
    }
    public function OnSaleComponentOrderOneStepComplete($ID,$arOrder,$arParams){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            $currentBudget = 0;$bon1=$bon2=0;
            global $USER;
            $option = $this->SETTINGS["ORDER"]["OPTION"];
            if($option['PAYORDER']!='none') {
                $arOrder = \CSaleOrder::GetByID($arOrder['ID']);
                $UID = $USER->GetID();
                $request = Context::getCurrent()->getRequest();
                $bonuspay=$request->getPost('PAY_BONUS_ACCOUNT');
                $userpay=$request->getPost('PAY_CURRENT_ACCOUNT');
                if ($bonuspay=='Y' && $option["PAY"]["ACTIVE"] == "Y") {
                    $bon1 = $request->getPost("BONUS_CNT");
                    $dbUserAccount = Bonus\AccountTable::getList(array(
                        'filter' => array('USER_ID' => $UID),
                    ));
                    if ($arUserAccount = $dbUserAccount->fetch())
                        $CB = round($arUserAccount["CURRENT_BUDGET"], 2);
                    if($CB>0 && $bon1>$CB) $bon1=0;
                    $currentBudget += $bon1;
                }
                if ($userpay=='Y' && $option["USERPAY"]["ACTIVE"] == "Y") {
                    $bon2 = $request->getPost("ACCOUNT_CNT");
                    $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $USER->GetID(), 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
                    if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $CB1 = round($arUserAccount['CURRENT_BUDGET'], 2);
                        if($CB1>0 && $bon2>$CB1) $bon2=0;
                    }
                } else {
                    $bon2 = $arOrder['SUM_PAID'];
                }
                $currentBudget += $bon2;
                if ($arOrder['ID'] > 0 && $currentBudget > 0) {
                    if ($bonuspay=='Y' && $option["PAY"]["ACTIVE"] == "Y") {
                        $this->AddBonus($UID, $arOrder['LID'], -$bon1, PAY, array("#ORDER_ID#" => $arOrder["ID"], "#SUMM#" => CurrencyFormat($arOrder["PRICE"], $arOrder["CURRENCY"])));
                    }
                    if ($userpay=='Y' && $option["USERPAY"]["ACTIVE"] == "Y") {
                        \CSaleUserAccount::Withdraw(
                            $UID,
                            $bon2,
                            $arOrder['CURRENCY'],
                            $arOrder["ID"]
                        );
                    }
                    \CSaleOrder::Update($arOrder['ID'], array('SUM_PAID' => $currentBudget, "PRICE" => $arOrder['PRICE'] - $currentBudget, 'USER_ID' => $UID));
                    if ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT']=='Y' && $currentBudget == $arOrder['PRICE']) {
                        \CSaleOrder::PayOrder($arOrder["ORDER_ID"], "Y", False, False);
                    }
                }
            }
        }
    }
    public function OnSocialLike($social,$path,$postid){
        if($this->SETTINGS['SITE_ON'][$this->LID]=="Y") {
            global $USER;
            $option = $this->SETTINGS['SOC_SERVICES']['OPTION'];
            if ($option[$social]['ACTIVE'] == 'Y') {
                $UID = $USER->GetID();
                if (!$UID) return array('OK'=>0,'ERROR'=>'BADUSER');
                if ($this->CheckSocial($social, $path)) {
                    $this->AddBonus($UID, $this->LID, $option[$social]['COUNT'], LIKE, array("#SC#" => $social, "#PATH#" => $path,'#POST_ID#'=>$postid));
                    return array('OK'=>1,'BONUS'=>$option[$social]['COUNT']);
                }
            }
        }
    }
    public function CheckSocial($social,$path){
        global $USER;
        $UID=$USER->GetID();$ok=true;
        if(!$UID) return false;
        $res=call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS,"getList"),array(array(
            'filter'=>array("USERID"=>$UID,"TYPE"=>LIKE))
        ));
        while($bon=$res->fetch()){
            $opt=unserialize(base64_decode($bon['OPTIONS']));
            if($opt['#SC#']==$social && $opt['#PATH#']==$path) $ok=false;else $ok=true;
        }
        return $ok;
    }
}