<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Sale;
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class CompBonusHistory extends \CBitrixComponent
{
	/**
	 * cache keys in arResult
	 * @var array()
	 */
	protected $module_id="vbcherepanov.bonus";
	protected $cacheKeys = array();

	/**
	 * add parameters from cache dependence
	 * @var array
	 */
	protected $cacheAddon = array();

	/**
	 * pager navigation params
	 * @var array
	 */
	protected $navParams = array();

	/**
	 * include lang files
	 */
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	/**
	 * prepare input params
	 * @param array $arParams
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{

		$params['CACHE_TIME'] = intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600;


		return $params;
	}

	/**
	 * read data from cache or not
	 * @return bool
	 */
	protected function readDataFromCache()
	{
		if ($this->arParams['CACHE_TYPE'] == 'N') // no cache
			return false;

		return !($this->StartResultCache(false, $this->cacheAddon));
	}

	/**
	 * cache arResult keys
	 */
	protected function putDataToCache()
	{
		if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
		{
			$this->SetResultCacheKeys($this->cacheKeys);
		}
	}

	/**
	 * abort cache process
	 */
	protected function abortDataCache()
	{
		$this->AbortResultCache();
	}

	/**
	 * check needed modules
	 * @throws LoaderException
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule($this->module_id))
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_COMP_NOT_MODULE'));
	}

	/**
	 * check required input params
	 * @throws SystemException
	 */
	protected function checkParams()
	{
	}
	/**
	 * some actions before cache
	 */
	protected function executeProlog()
	{

	}

	/**
	 * get component results
	 */
	protected function getResult()
	{
		$bb=new Vbchbbonus\Vbchbbcore();
		$bb->SITE_ID=SITE_ID;
		$arResult=array();
		$option1=$bb->GetOptions(SITE_ID,'BONUSNAME');
		if(!$bb->CheckSiteOn()) {
			throw new Main\LoaderException(Loc::getMessage('VBCHBB_COMP_NOTSETTINGS'));
		}
		else{
			$arResult['PROFILES']=Vbchbbonus\CvbchbonusprofilesTable::getList(array(
				'filter'=>array('SITE'=>$bb->SITE_ID,'ACTIVE'=>'Y','!TYPE'=>'BONUS'),
			))->fetchAll();
		}
		if($bb->CheckArray($arResult['PROFILES'])){
			foreach($arResult['PROFILES'] as $prof){
				$data=array();
				$data['NAME']=$prof['NAME'];
				$data['NOTIFICATION']=$bb->CheckSerialize($prof['NOTIFICATION']);
				$data['FILTER']=$bb->CheckSerialize($prof['FILTER']);
				$data['SETTINGS']=$bb->CheckSerialize($prof['SETTINGS']);
				$percent=strpos("%",$prof['BONUS']);
				$data['BONUS']=($percent!==false) ? $bb->declOfNum($prof['BONUS'],($option1['OPTION']['SUFIX']=='NAME' ? $option1['OPTION']['NAME']:array("","","")),$bb->ModuleCurrency()) : $prof['BONUS'];
				$arResult['ITEMS'][]=$data;
			}
		}
		$this->arResult=$arResult;
	}

	/**
	 * some actions after component work
	 */
	protected function executeEpilog()
	{

	}

	/**
	 * component logic
	 */
	public function executeComponent()
	{
		try
		{
			$this->checkModules();
			$this->checkParams();
			$this->executeProlog();
			if (!$this->readDataFromCache())
			{
				$this->getResult();
				$this->putDataToCache();
				$this->includeComponentTemplate();
			}
			$this->executeEpilog();
		}
		catch (Exception $e)
		{
			$this->abortDataCache();
			ShowError($e->getMessage());
		}
	}
}