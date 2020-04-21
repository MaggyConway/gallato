<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Context;
CJSCore::Init(array("jquery"));
Loc::loadMessages(__FILE__);
CJSCore::Init(array('jquery'));
class Compprofilefields extends \CBitrixComponent
{
	const MODULE_ID="vbcherepanov.bonus";
	public function executeComponent()
	{
		try
		{
			self::checkModules();
    		$this->prepareData();
			$this->includeComponentTemplate();
		}
		catch (SystemException $e)
		{
			$this->abortResultCache();
			ShowError($e->getMessage());
		}
	}

	protected static function checkModules()
	{
		if (!Loader::includeModule(self::MODULE_ID))
			throw new SystemException(Loc::getMessage('VBCHBB_MODULE_NOT_INSTALL'));

	}
	public function onPrepareComponentParams($arParams)
	{
		return $arParams = parent::onPrepareComponentParams($arParams);
	}
	protected function prepareData()
	{
        $bonus=new \ITRound\Vbchbbonus\Vbchbbcore();
        foreach($bonus->PROFILES as $prof){
            if($prof['ID']==$this->arParams['PROFILE']){
                $l=$prof;break;
            }
        }
        foreach($this->arParams['PROFILEFIELDS'] as $k){
            $this->arResult[$k]=$l[$k];
        }

	}
}