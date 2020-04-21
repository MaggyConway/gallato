<?php
namespace ITRound\Vbchbbonus\restcontroller\v1;
use Bitrix\Main\Loader;
use \ITRound\Vbchbbonus;

class Bonusaccount
{
    private $q;
    public function __construct($obj)
    {
        $this->q=$obj;
    }
    public function getlist(){
        $params=[
            'user_id'=>false,
            'account'=>false,
        ];
        $parameter=self::checkParams($params);
        $filter=[];
        if($parameter['user_id']) $filter['USER_ID']=$parameter['user_id'];
        if($parameter['account']) $filter['BONUSACCOUNTSID']=$parameter['account'];
        $res=self::getUserBonusAC($filter);
        $this->q->ShowResult($res, JSON_UNESCAPED_UNICODE);
    }
    public function getuserfromid(){
        $params=[
            'user_id'=>false,
            'xmlid'=>false,
        ];
        $parameter=self::checkParams($params);
        if($parameter['user_id']) $p['user_id']=$parameter['user_id'];
        if($parameter['extid']) $p['extid']=$parameter['extid'];
        if(sizeof($p)==0 || (array_key_exists('user_id',$p) || array_key_exists('extid',$p))){
            $this->q->BadRequest('NOT USER ID OR USER XML_ID');
        }
        $user=self::getuser($p);
        if($user){
            $bonus=self::getUserBonusAC(['USER_ID'=>$user['ID']]);
            $card=self::getUserCard(['USERID'=>$user['ID']]);
        }else {$bonus=$card=[];}
        $this->q->ShowResult(['USER'=>$user,'BONUS'=>$bonus,'CARD'=>$card], JSON_UNESCAPED_UNICODE);
    }

    public function getuserfromemail(){
        $params=[
            'email'=>true,
        ];
        $parameter=self::checkParams($params);
        if($parameter['email']) $p['email']=$parameter['email'];
        else
            $this->q->BadRequest('NOT EMAIL');
        $user=self::getuser($p);
        if($user){
            $bonus=self::getUserBonusAC(['USER_ID'=>$user['ID']]);
            $card=self::getUserCard(['USERID'=>$user['ID']]);
        }else {$bonus=$card=[];}

        $this->q->ShowResult(['USER'=>$user,'BONUS'=>$bonus,'CARD'=>$card], JSON_UNESCAPED_UNICODE);
    }

    public function getuserfrombonuscard(){
        $params = [
            'bonuscard'=>true,
        ];
        $parameter=self::checkParams($params);
        if($parameter['bonuscard']) $p['bonuscard']=$parameter['bonuscard'];
        else
            $this->q->BadRequest('NOT BONUSCARD');
        $card=self::getUserCard(['NUM'=> $p['bonuscard']]);
        if($card) {
            $bonus = self::getUserBonusAC(['USER_ID' => $card['USERID']]);
            $user = self::getuser(['user_id' => $card['USERID']]);
        }else {
            $bonus=$user=[];
        }
        $this->q->ShowResult(['USER'=>$user,'BONUS'=>$bonus,'CARD'=>$card], JSON_UNESCAPED_UNICODE);
    }

    public function getuserfromphone(){
        $params=[
            'phone'=>true,
        ];
        $parameter=self::checkParams($params);
        if($parameter['phone']) $p['phone']=$parameter['phone'];
        else
            $this->q->BadRequest('NOT PHONE');
        $user=self::getuser($p);
        $bonus=self::getUserBonusAC(['USER_ID'=>$user['ID']]);
        $card=self::getUserCard(['USERID'=>$user['ID']]);
        $this->q->ShowResult(['USER'=>$user,'BONUS'=>$bonus,'CARD'=>$card], JSON_UNESCAPED_UNICODE);
    }
    private function getUserCard($filter){
        if(Loader::includeModule("vbcherepanov.bonus")){
            return Vbchbbonus\BonusCardTable::getList([
                'filter'=>$filter,
                'select'=>['*'],
            ])->fetch();
        }else $this->q->BadRequest('Module not install');

    }
    private function getUserBonusAC($filter){
        if(Loader::includeModule("vbcherepanov.bonus")){
            return Vbchbbonus\AccountTable::getList([
                'filter'=>$filter,
                'select'=>['*'],
            ])->fetchAll();
        }else $this->q->BadRequest('Module not install');
    }

    private function getuser(array $param){
        $filter=[];
        if(array_key_exists('user_id',$param)){
            $filter['=ID']=intval(trim($param['user_id']));
        }elseif(array_key_exists('extid',$param)){
            $filter['XML_ID']=trim($param['extid']);
        }elseif(array_key_exists('phone',$param)){
            $filter['%PERSONAL_PHONE']=trim($param['phone']);
        }elseif(array_key_exists('email',$param)){
            $filter['EMAIL']=trim($param['email']);
        }
        $filter['ACTIVE']='Y';
        if(\Bitrix\Main\Loader::includeModule("main")){
            $q=\Bitrix\Main\UserTable::getList(
                [
                    'filter'=>$filter,
                    'select'=>['ID','NAME','LAST_NAME','SECOND_NAME','PERSONAL_PHONE','EMAIL','XML_ID','LOGIN'],
                ]
            );
            if($usr=$q->fetch()){
                return $usr;
            }else return ['ERROR'=>'NOT USER'];
        }
    }

    private function getRequest()
    {
        return  $this->q->Requestget();
    }

    public function add(){
        $params=[
            'user_id'=>false,
            'xmlid'=>false,
            'phone'=>false,
            'email'=>false,
            'account'=>false,
            'amount'=>false,

        ];
        $parameter=self::checkParams($params);
        $p=[];
        $BBCORE=new Vbchbbonus\Vbchbbcore();
        if($parameter['amount']==false) $parameter['amount']=0;
        if($parameter['account']==false){
            $parameter['account']=$BBCORE->getaccountsID();
        }

        if($parameter['user_id']) $p['user_id']=$parameter['user_id'];
        if($parameter['xmlid']) $p['xmlid']=$parameter['xmlid'];
        if($parameter['phone']) $p['phone']=$parameter['phone'];
        if($parameter['email']) $p['email']=$parameter['email'];
        if(sizeof($p)>0)
            $user=self::getuser($p);
        if($user['ID']){
            if(\Bitrix\Main\Loader::includeModule('vbcherepanov.bonus')){
                if(is_numeric($parameter['amount'])){
                    $al=\ITRound\Vbchbbonus\AccountTable::getList(
                        [
                            'filter'=>[
                                'USER_ID'=>$user['ID'],
                                "BONUSACCOUNTSID" => $parameter['account'],
                            ]
                        ]
                    );
                    if($al->fetch()){
                        $this->q->BadRequest('User bonus account already exists');
                    }else{
                        $fields = array(
                            'USER_ID' =>$user['ID'],
                            'CURRENT_BUDGET' => $parameter['amount'],
                            'CURRENCY' =>$BBCORE->ModuleCurrency($parameter['account']),
                            'NOTES' => '',
                            "BONUSACCOUNTSID" => $parameter['account'],
                        );
                        \Bitrix\Main\Application::getConnection()->startTransaction();
                        $l = \ITRound\Vbchbbonus\AccountTable::add($fields);
                        if ($l->isSuccess()) {
                            \Bitrix\Main\Application::getConnection()->commitTransaction();
                            $data=$l->getData();
                            $data['ID']=$l->getId();
                            $this->q->ShowResult(['USER'=>$user,'BONUS'=>$data], JSON_UNESCAPED_UNICODE);
                        } else {
                            \Bitrix\Main\Application::getConnection()->rollbackTransaction();
                            $this->q->BadRequest($l->getErrorMessages());
                        }
                    }
                }else $this->q->BadRequest('BAD amount');
            } else $this->q->BadRequest('not install module vbcherepanov.bonus');
        } else $this->q->BadRequest('USER NOT FOUND');
    }

    public function delete(){
        $params=[
            'user_id'=>false,
            'xmlid'=>false,
            'phone'=>false,
            'email'=>false,
            'account'=>false,
            'id'=>false,

        ];
        $parameter=self::checkParams($params);
        $p=[];$filter=[];
        if($parameter['user_id']) $p['user_id']=$parameter['user_id'];
        if($parameter['xmlid']) $p['xmlid']=$parameter['xmlid'];
        if($parameter['phone']) $p['phone']=$parameter['phone'];
        if($parameter['email']) $p['email']=$parameter['email'];

        if(sizeof($p)>0)
            $user=self::getuser($p);
        if($user['ID']){
            $filter['USER_ID']=$user['ID'];
        }
        if($parameter['account']){
            $filter['BONUSACCOUNTSID']=$parameter['account'];
        }
        if(\Bitrix\Main\Loader::includeModule('vbcherepanov.bonus')){
            if($parameter['id']!=''){
                \Bitrix\Main\Application::getConnection()->startTransaction();
                $l=Vbchbbonus\AccountTable::delete($parameter['id']);
                if ($l->isSuccess()) {
                    \Bitrix\Main\Application::getConnection()->commitTransaction();
                    $this->q->ShowResult([$parameter['id']=>'delete'], JSON_UNESCAPED_UNICODE);
                } else {
                    \Bitrix\Main\Application::getConnection()->rollbackTransaction();
                    $this->q->BadRequest($l->getErrorMessages());
                }
            }else{
                if(sizeof($filter)>0){
                    $res=Vbchbbonus\AccountTable::getList(
                        [
                            'filter'=>$filter,
                            'select'=>['ID']
                        ]
                    );
                    while($q=$res->fetch()){
                        \Bitrix\Main\Application::getConnection()->startTransaction();
                        $l=Vbchbbonus\AccountTable::delete($q['ID']);
                        if ($l->isSuccess()) {
                            \Bitrix\Main\Application::getConnection()->commitTransaction();
                            $ok[]=[$q['ID']=>'delete'];
                        } else {
                            \Bitrix\Main\Application::getConnection()->rollbackTransaction();
                            $ok[]=[$q['ID']=>$l->getErrorMessages()];

                        }
                    }
                    $this->q->ShowResult($ok, JSON_UNESCAPED_UNICODE);
                }else  $this->q->BadRequest('bad filter of account');
            }
        }else $this->q->BadRequest('not install module vbcherepanov.bonus');
    }
    public function summary(){
        $arResult = $this->getRequest();
    }
    public function  addbonus(){
        $params=[
            'user_id'=>false,
            'xmlid'=>false,
            'phone'=>false,
            'email'=>false,
            'account'=>false,
            'amount'=>false,
            'message'=>false,
            'prof_id'=>false,
            'prof_code'=>false,

        ];
        $parameter=self::checkParams($params);
        if($parameter['message']=='') $message='restapi bonus edit';
        $p=[];
        $BBCORE=new Vbchbbonus\Vbchbbcore();
        if($parameter['amount']==false) $parameter['amount']=0;
        if($parameter['account']==false){
            $parameter['account']=$BBCORE->getaccountsID();
        }

        $proffilter=['ACTIVE'=>"Y",'SITE'=>$BBCORE->SITE_ID];
        // if($parameter['prof_id']==false && $parameter['prof_code']==false){
        //     $this->q->BadRequest('check prof_id or prof_code');
        // }
        if($parameter['prof_id']!==false) $proffilter['ID']=$parameter['prof_id'];
        if($parameter['prof_code']!==false) $proffilter['TYPE']=$parameter['prof_code'];
        if($parameter['user_id']) $p['user_id']=$parameter['user_id'];
        if($parameter['xmlid']) $p['xmlid']=$parameter['xmlid'];
        if($parameter['phone']) $p['phone']=$parameter['phone'];
        if($parameter['email']) $p['email']=$parameter['email'];
        if(sizeof($p)>0)
            $user=self::getuser($p);
        if ($user['ID']) {
            if (\Bitrix\Main\Loader::includeModule('vbcherepanov.bonus')) {
                if (is_numeric($parameter['amount'])) {
                    $res = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                        'filter' =>$proffilter,
                    ))->fetchAll();
                    if ($BBCORE->CheckArray($res)) {
                        foreach ($res as $prof) {
                            $l = $BBCORE->CheckSerialize($prof['BONUSCONFIG']);
                            $l['BONUSINNERIN']['BONUSINNER'] = $parameter['account'];
                            $prof['BONUSCONFIG'] = base64_encode(serialize($l));
                            $NOTIFICATIONS = $BBCORE->CheckSerialize($prof['NOTIFICATION']);
                            $NOTIFICATIONS['TRANSACATIONMESSAGE'] = $parameter['message'];
                            $prof['NOTIFICATION'] = base64_encode(serialize($NOTIFICATIONS));
                            $bon=$BBCORE->BonusParams($parameter['amount'],$l);
                            $BBCORE->AddBonus($bon,
                                array('SITE_ID' => $BBCORE->SITE_ID,
                                    'USER_ID' => $user['ID'], 'IDUNITS' => 'REST_API_ADDBONUS_' . $user['ID'] . '_' . $parameter['amount'] . '_' . time()), $prof, true);
                            Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
                            if($user){
                                $bonus=self::getUserBonusAC(['USER_ID'=>$user['ID']]);
                                $card=self::getUserCard(['USERID'=>$user['ID']]);
                            }else {$bonus=$card=[];}
                            $this->q->ShowResult(['USER'=>$user,'BONUS'=>$bonus,'CARD'=>$card], JSON_UNESCAPED_UNICODE);
                        }
                    }

                } else $this->q->BadRequest('BAD amount');
            } else $this->q->BadRequest('not install module vbcherepanov.bonus');
        } else $this->q->BadRequest('USER NOT FOUND');
    }

    private function checkParams(array $param){
        $result=[];
        $arResult = $this->getRequest();
        if($param && sizeof($param)>0){
            foreach($param as $id=>$require){
                $result[$id]=$arResult['PARAMETERS'][$id] ? $arResult['PARAMETERS'][$id] : false;
                if($require && !$result[$id]) $this->q->BadRequest('parameter '. $id . ' is required');
            }
        }
        return $result;
    }
}