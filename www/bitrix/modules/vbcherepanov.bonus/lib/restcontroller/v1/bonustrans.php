<?php
namespace ITRound\Vbchbbonus\restcontroller\v1;


use Bitrix\Main\Loader;
use \ITRound\Vbchbbonus;

class Bonustrans
{
    private $q;
    public function __construct($obj)
    {
        $this->q=$obj;
    }
    public function getlist(){
        $arResult = $this->getRequest();
        $user_id=$arResult['PARAMETERS']['user_id'] ? $arResult['PARAMETERS']['user_id'] : false;
        $account=$arResult['PARAMETERS']['account'] ? $arResult['PARAMETERS']['account'] : false;

        $filter=[];
        $res=[];
        if($user_id)
            $filter['USER_ID']=$user_id;
        if($account)
            $filter['BONUSACCOUNTSID']=$account;
        if(Loader::includeModule("vbcherepanov.bonus")){
            $res=Vbchbbonus\BonusTable::getList([
                'filter'=>$filter,
                'select'=>['*'],
            ])->fetchAll();
        }
        $this->q->ShowResult($res, JSON_UNESCAPED_UNICODE);
    }
    // Get current request
    private function getRequest()
    {
        return  $this->q->Requestget();
    }

    public function add(){
        $arResult = $this->getRequest();
    }

    public function delete(){
        $arResult = $this->getRequest();
    }

    public function summary(){
        $arResult = $this->getRequest();
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