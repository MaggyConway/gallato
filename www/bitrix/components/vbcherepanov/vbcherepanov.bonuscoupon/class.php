<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\Context;
use \Bitrix\Main\Engine\Contract;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class BonusCoupon extends \CBitrixComponent implements Contract\Controllerable
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

    public function getActivateCouponAction($post){
        \Bitrix\Main\Loader::includeModule($this->module_id);
        return Vbchbbonus\CouponTable::ActivateCoupon($post['coupon'],$post['user_id']);

    }
    protected function listKeysSignedParameters()
    {
        return [
        ];
    }

    public function configureActions()
    {
        return [
            'getActivateCoupon' => [
                'prefilters' => [],
            ],
        ];
    }


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
		global $USER;
        $currentDatetime = new \Bitrix\Main\Type\DateTime();
        $this->arResult['COUPON']=[];
		$UID=intval($USER->GetID());
		$bb=new Vbchbbonus\Vbchbbcore();
		$bb->SITE_ID=SITE_ID;
		$dbAccount = Vbchbbonus\CouponTable::getList(
		    [
		        'filter'=>[
		            'ACTIVE'=>'Y',
                    [
                        'LOGIC'=>'OR',
                        ['USER_ID'=>$UID],
                        ['USER_ID'=>false],
                    ],
                    [
                        'LOGIC' => 'OR',
                        'ACTIVE_FROM' => '',
                        '<=ACTIVE_FROM' => $currentDatetime
                    ],
                    [
                        'LOGIC' => 'OR',
                        'ACTIVE_TO' => '',
                        '>=ACTIVE_TO' => $currentDatetime
                    ]
                ]
            ]
        );
		if($dbAccount){
		    while($cpn=$dbAccount->fetch()){
                $this->arResult['COUPON'][]=$cpn;
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