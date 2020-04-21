<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use \ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc as Loc;

class BonusForOrderComponent extends \CBitrixComponent
{
    /**
     * cache keys in arResult
     * @var array()
     */

    protected $cacheKeys = array();
    protected $module_id="vbcherepanov.bonus";
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
        $result =array(
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
            "CACHE_TYPE"=>$params["CACHE_TYPE"],
            "TYPE"=>$params["TYPE"],
            "RESULT"=>$params["RESULT"],
        );
        return $result;
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
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_NOT_INSTALL'));
    }
    /**
     * check required input params
     * @throws SystemException
     */
    protected function checkParams()
    {
        if($this->arParams['TYPE']=="CART"){
            $this->arParams['OFFERS']=$this->arParams['RESULT']["GRID"]["ROWS"];
        }elseif($this->arParams['TYPE']=="ORDER"){
            $this->arParams['OFFERS']=$this->arParams['RESULT']['BASKET_ITEMS'];
            $this->arParams['PAY_SYSTEM_ID']=$this->arParams['RESULT']['USER_VALS']['PAY_SYSTEM_ID'];
            $this->arParams['DELIVERY_ID']=$this->arParams['RESULT']['USER_VALS']['DELIVERY_ID'];
        }
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
        $option1=$bb->GetOptions( $bb->SITE_ID,'BONUSNAME');
        if($bb->CheckSiteOn()){
            $bonus=$bb->GetCartOrderBonus($this->arParams['TYPE'],$this->arParams);
            if($this->arParams['RESULT']['USER_VALS']['PAY_BONUSORDERPAY']=='Y'){
                $bonus=0;
            }
            $this->arResult['BONUS']=$bb->declOfNum($bonus,($option1['OPTION']['SUFIX']=='NAME' ? $option1['OPTION']['NAME']:array("","","")),$bb->ModuleCurrency());
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