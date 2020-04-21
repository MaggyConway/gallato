<?php
namespace ITRound\Vbchbbonus\restcontroller\v1;


use Bitrix\Main\Loader;
use \ITRound\Vbchbbonus;

class Bonuscard
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
        $res=[];
        //$res=self::getUserBonusAC($filter);
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