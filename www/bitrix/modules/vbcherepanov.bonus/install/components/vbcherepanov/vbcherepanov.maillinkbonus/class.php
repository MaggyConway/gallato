<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use \ITRound\Vbchbbonus;

class CITRoundBonusLinkComponent extends CBitrixComponent
{
    public $userId=0;
    public $BONUS=0;
	public function onPrepareComponentParams($arParams)
	{
		if (isset($arParams['USER_ID']))
			$arParams['USER_ID'] = intval($arParams['USER_ID']);
		if (isset($arParams['BONUS']))
			$arParams['BONUS'] = intval($arParams['BONUS']);

		return $arParams;
	}

	public function executeComponent()
	{
		parent::setFramemode(false);
		$this->userId = intval($this->arParams["USER_ID"]);
		$this->BONUS = intval($this->arParams["BONUS"]);
        if($this->userId <= 0 || $this->BONUS <=0)
        {
            return;
        }
        $this->arResult['BONUS']=$this->BONUS;
        $this->arResult['HASH']=$this->CreateHashLink();
        $this->IncludeComponentTemplate();
	}
    public function CreateHashLink(){
        $hash = self::GetHash();
        $arFields['HASH'] = $hash.'_'.$this->userId;
        $arFields['BONUS']=$this->BONUS;
        $arFields['USER_ID']=$this->userId;
        \Bitrix\Main\Application::getConnection()->startTransaction();
        $res=Vbchbbonus\LinkmailTable::add($arFields);
        if($res->isSuccess()){
            \Bitrix\Main\Application::getConnection()->commitTransaction();
            return $arFields['HASH'];
        }else{
            print_r($res->getErrorMessages());
            \Bitrix\Main\Application::getConnection()->rollbackTransaction();
        }

    }

    private static function GetHash()
    {
        return substr(md5(md5(time()).uniqid()),0,25);
    }
}

?>