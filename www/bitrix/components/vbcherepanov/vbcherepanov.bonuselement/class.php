<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc as Loc;

class VBChBonusElement extends \CBitrixComponent
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
        $params['CACHE_TIME'] = intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600;
        $params['ONLY_NUM']=$params['ONLY_NUM']=='Y';
        $params['OFFERS_ID']=trim($params['OFFERS_ID']);
        $params['OFFERS_AR']=trim($params['OFFERS_AR']);

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
        $bb=new Vbchbbonus\Vbchbbcore();
        $bb->SITE_ID=SITE_ID;
        $option1=$bb->GetOptions(SITE_ID,'BONUSNAME');
		$option2=$bb->GetOptions(SITE_ID,'BNSELEMENTCART');
		$option2=$option2['OPTION'];
			if($option2=='SUMM'){
				
			}elseif($option2=='MIN'){
				$this->arResult['PREFIX']=Loc::getMessage('BONUS_PREFIX_OT');
			}elseif($option2=='MAX'){
				$this->arResult['PREFIX']=Loc::getMessage('BONUS_PREFIX_DO');
			}
        if(!$bb->CheckSiteOn())
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_ELEMENT_NOSITE_CONF'));
        else{
            $IBLOCK_ID=$this->arParams['ELEMENT']['IBLOCK_ID'];
            if($this->arParams['OFFERS_AR'] && $this->arParams['OFFERS_ID'] && $bb->CheckArray($this->arParams['ELEMENT'][$this->arParams['OFFERS_AR']])){
				$EL_ID=$this->arParams['ELEMENT'][$this->arParams['OFFERS_AR']][$this->arParams['ELEMENT'][$this->arParams['OFFERS_ID']]];
				$all_price=array();
				foreach($this->arParams['ELEMENT'][$this->arParams['OFFERS_AR']] as $offer){
					$all_price[$offer['ID']]=$offer['MIN_PRICE'];
				}
            }else{
				$EL_ID=$this->arParams['ELEMENT'];
				$all_price[$EL_ID['ID']]=$EL_ID['ITEM_PRICES'][0];
			}
            $EL_ID['MIN_PRICE']=[
                'DISCOUNT_VALUE'=>$EL_ID['ITEM_PRICES'][0]['PRICE'],
                'ID'=>$EL_ID['ITEM_PRICES'][0]['ID'],
                'PRICE_ID'=>$EL_ID['ITEM_PRICES'][0]['PRICE_TYPE_ID']

            ];
            if($this->arParams['ELEMENT_ID']) $EL_ID['ID']=$this->arParams['ELEMENT_ID'];
            if($this->arParams['IBLOCK_ID']) $EL_ID['IBLOCK_ID']=$this->arParams['IBLOCK_ID'];
            if($this->arParams['COUNT']) $cnt=$this->arParams['COUNT'];else $cnt=1;
            $bonus=$bb->GetBonusElements($EL_ID['ID'],$EL_ID['IBLOCK_ID'],$cnt,$EL_ID['ITEM_PRICES'][0],$this->arParams['ELEMENT']);
            $this->arResult['BONUS_PRICE']=$all_price;
            $this->arResult['DATA']['ID']=$EL_ID['ID'];
            $this->arResult['DATA']['IBLOCK_ID']=$EL_ID['IBLOCK_ID'];
            if($bonus!==false || $bonus!=="" || $bonus!==0){
                $this->arResult['BONUS']=$this->arParams['ONLY_NUM'] ? $bonus :$bb->ReturnCurrency($bonus);
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