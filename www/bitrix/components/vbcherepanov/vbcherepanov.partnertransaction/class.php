<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class CompPartnerTransaction extends \CBitrixComponent
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
		\Bitrix\Main\Loader::includeModule("sale");
	    $option1=$bb->GetOptions(SITE_ID,'BONUSNAME');
		$bb->SITE_ID=SITE_ID;
		$this->arResult['DATE']=date(\CDatabase::DateFormatToPHP(CSite::GetDateFormat("SHORT", SITE_ID)));
		$UID=intval($USER->GetID());

		$transactionList=\ITRound\Vbchbbonus\BonusTable::getList(array(
			'filter'=>array('LID'=>$bb->SITE_ID,'USERID'=>$UID,'BONUSACCOUNTSID'=>$this->arParams['MONEYBACKACCOUNT'],'TYPES'=>explode(",",$this->arParams['PROFILETYPE']))
		))->fetchAll();
	if($transactionList){
		$orderid=array();
		foreach ($transactionList as $trans){
			$dat=$bb->CheckSerialize($trans['OPTIONS']);
			$dt=explode("-",$dat['IDUNITS']);
			$orderid[$dt[2]]['BONUS']=$trans['BONUS'];
		}
		$couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(array(
			'select' => array('ORDER_ID','COUPON'),
		'filter' => array('ORDER_ID' => array_keys($orderid))
		))->fetchAll();
		if($couponList){
			foreach($couponList as $coup){
				$orderid[$coup['ORDER_ID']]['COUPON']=$coup['COUPON'];
			}
		}
		$dbResultList = CSaleStatus::GetList(
			array(),
			array(),
			false,
			false,
			array('ID', 'NAME')
		);
		while($st=$dbResultList->Fetch()){
			$statusList[$st['ID']]=$st['NAME'];
		}

		$orderRes=\Bitrix\Sale\Order::getList(array(
			'filter'=>array('ID'=>array_keys($orderid)),
			'select'=>array('ID','DATE_INSERT','PRICE','STATUS_ID')
		))->fetchAll();
		if($orderRes){
			foreach($orderRes as $order){
				$orderid[$order['ID']]['ORDER_ID']=$order['ID'];

				$orderid[$order['ID']]['ORDER_DATE']=explode(" ",$order['DATE_INSERT']->format('d.m.Y h:i'));
				$orderid[$order['ID']]['PRICE']=$order['PRICE'];
				$orderid[$order['ID']]['STATUS']=$statusList[$order['STATUS_ID']];
				$orderid[$order['ID']]['PERCENT']=intval(($orderid[$order['ID']]['BONUS']*100)/$order['PRICE']).'%';
			}
		}
		$this->arResult['TRANSACTION']=$orderid;
	}}

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