<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class CompLastuserbonus extends \CBitrixComponent
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
	protected function checkAuth(){
		global $USER;
		if(!$USER->IsAuthorized())
			throw new Main\LoaderException(Loc::getMessage('VBCHBB_ACCESS_DENIED_ACCOUNT'));
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
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_MODULE_NOT_INSTALL'));
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
		global $USER;$not=true;$Inot=true;$BNot=true;
		$bb=new Vbchbbonus\Vbchbbcore();
	    $option1=$bb->GetOptions(SITE_ID,'BONUSNAME');
		$bb->SITE_ID=SITE_ID;
		$this->arResult['DATE']=date(\CDatabase::DateFormatToPHP(CSite::GetDateFormat("SHORT", SITE_ID)));
		$UID=intval($USER->GetID());
		Main\Loader::includeModule("sale");

		$filter=array(
			'ACTIVE'=>"Y",
			"TYPES"=>explode(",",$this->arParams['PROFILETYPE']),
			"USERID"=>$UID,
			"LID"=>$bb->SITE_ID,
		);
		$select=array('ID');
		if($this->arParams['TYPE']=='BONUS'){
			$select[]='BONUS';
		}elseif($this->arParams['TYPE']=='DATE'){
			$select[]='TIMESTAMP_X';
		}
		if($this->arParams['NOTMINUS']=='Y'){
			$filter['>BONUS']=0;
		}

		$trans=\ITRound\Vbchbbonus\BonusTable::getList(array(
			'filter'=>$filter,
			"order"=>array('ID'=>'DESC'),
			'select'=>$select
		))->fetch();
		if($trans){
			if($this->arParams['TYPE']=='BONUS'){
				$this->arResult['RESULT']=floatval($trans['BONUS']);
			}elseif($this->arParams['TYPE']=='DATE'){
				if(intval($this->arParams['PLUSDATENUM'])>0){
					$lastdate=$trans['TIMESTAMP_X'];
					$p=$lastdate->add(intval($this->arParams['PLUSDATENUM'])." day");
					$this->arResult['RESULT']=$lastdate->toString();
				}else{
					$this->arResult['RESULT']=$trans['TIMESTAMP_X']->toString();
				}

			}
		}else{
			$this->arResult['RESULT']=false;
		}

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
			$this->checkAuth();
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