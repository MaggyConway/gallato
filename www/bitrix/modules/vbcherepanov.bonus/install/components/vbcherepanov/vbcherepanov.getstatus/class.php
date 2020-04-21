<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class CompGetStatus extends \CBitrixComponent
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

		$profiles=$bb->FilterProfiles(explode(",",$this->arParams['PROFILETYPE']));
		if($bb->CheckArray($profiles)){

			$arFields['USERID']=$UID;
			$arFields['USERGROUP']=\CUser::GetUserGroup($UID);

			$current=array();
			foreach($profiles as $prof){
				$Filter = call_user_func_array(array($bb->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($bb->FUNC_GETRULES[$prof['TYPE']],$prof['ID'], $bb->CheckSerialize($prof['FILTER']), $arFields));
				if($Filter) {
					$filt1=$bb->GetOrderCountByUser($UID);
					$this->arResult['STATUS'] = $prof['NAME'];
					$this->arResult['BONUS'] = $prof['BONUS'];
					$current = $prof;
					break;
				}
			}

			if(is_array($current)>0){
				$sort=$bb->CheckSerialize($current['SETTINGS']);
				$trans=\ITRound\Vbchbbonus\BonusTable::getList(
					array(
						'filter'=>array('USERID'=>$UID,'TYPES'=>$current['TYPE']),
						'select'=>array('TIMESTAMP_X'),
						'order'=>array('ID'=>'ASC')
					)
				)->fetch();
				if($trans){
					$dt=$trans['TIMESTAMP_X'];
					$dt->add('1 year');
				}else{
					$res=CUser::GetById($USER->GetID())->Fetch();
					$dt=new \Bitrix\Main\Type\DateTime($res['DATE_REGISTER']);
					$dt->add('1 year');
				}
				$this->arResult['NEXT_DATE']=$dt->toString();
				$sort=$sort['CODE'];

				foreach($profiles as $prof){
					$sort1=$bb->CheckSerialize($prof['SETTINGS']);
					$profsort=$sort1['CODE'];
					if(($sort+1)==$profsort){
						$filt=$bb->CheckSerialize($prof['FILTER']);
						$filt2=$filt['ORDERCOUNT'];
						$this->arResult['NEXT_STATUS'] = $prof['NAME'];
						$this->arResult['NEXT_BONUS'] = $prof['BONUS'];
						$this->arResult['NEXT_LEVEL_ORDER']=$filt2-$filt1;
					}
				}
			}


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