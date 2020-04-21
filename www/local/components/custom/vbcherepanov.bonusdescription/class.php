<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class CompBonusDescription extends \CBitrixComponent
{
    /**
     * cache keys in arResult
     * @var array()
     */
    protected $moduleid="vbcherepanov.bonus";
    protected $cacheKeys = array();
    protected $filter=array();
    protected $asc=array();
    protected $asc_inner=array();

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
        $params['FIELDS']=$params['ORDER'] ? $params['ORDER'] : 'TIMESTAMP_X';
        $params['SORT']=$params['ORDERDEC'] ? $params['ORDERDEC'] : 'ASC';
        $params['ACTIVE']=$params['NOTACTIVE'] ? $params['NOTACTIVE'] : 'N';

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
        if (!Main\Loader::includeModule($this->moduleid))
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_NOT_MODULE'));
    }
    protected function checkAuth(){
        global $USER;
        if(!$USER->IsAuthorized())
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_ACCESS_DENIED'));
    }

    /**
     * check required input params
     * @throws SystemException
     */

    protected function checkParams()
    {
        $replace=array("TIMESTAMP_X"=>"TIMESTAMP_X","BONUS"=>"AMOUNT","TYPE"=>"DESCRIPTION");

        $this->asc=array($this->arParams['FIELDS']=>$this->arParams['SORT']);
        $this->asc_inner=array($replace[$this->arParams['FIELDS']]=>$this->arParams['SORT']);

        if($this->arParams['ACTIVE']=='Y'){
            $this->filter['ACTIVE']='Y';
        }
        else {
            $this->filter = array();
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
        global $USER; $not=true;
        $arResult["TYPES"] = array(
            "ORDER_PAY" => Loc::getMessage("SMOT_TR_TYPE_PAYMENT"),
            "CC_CHARGE_OFF" => Loc::getMessage("SMOT_TR_TYPE_FROM_CARD"),
            "OUT_CHARGE_OFF" => Loc::getMessage("SMOT_TR_TYPE_INPUT"),
            "ORDER_UNPAY" => Loc::getMessage("SMOT_TR_TYPE_CANCEL_PAYMENT"),
            "ORDER_CANCEL_PART" => Loc::getMessage("SMOT_TR_TYPE_CANCEL_SEMIPAYMENT"),
            "MANUAL" => Loc::getMessage("SMOT_TR_TYPE_HAND"),
            "DEL_ACCOUNT" => Loc::getMessage("SMOT_TR_TYPE_DELETE"),
            "AFFILIATE" => Loc::getMessage("SMOT_MOBILEAPP_NOT_INSTALLED")
        );
        $bb=new Vbchbbonus\Vbchbbcore();
        $bb->SITE_ID=SITE_ID;
        $UID=$USER->GetID();
        Main\Loader::includeModule("sale");
        $option1=$bb->GetOptions(SITE_ID,'BONUSNAME');
        if($this->arParams['SHOW_INNER_ACCOUNT']=='Y'){
            $dbTransactList = \CSaleUserTransact::GetList(
                $this->asc_inner,
                array_merge($this->filter,array("USER_ID"=>$UID)),
                false,
                false,
                array("*")
            );
            while($arAccountList = $dbTransactList->GetNext()){
                $arResultTemp=array();
                $arResultTemp['ACTIVE']=Loc::getMessage("VBCHBB_YES");
                $arResultTemp['DATE']=$arAccountList['TRANSACT_DATE'];
                $arResultTemp['ACTIVE_FROM']="-";
                $arResultTemp['ACTIVE_TO']="-";
                $arResultTemp['SUMMA']=$bb->declOfNum($arAccountList['AMOUNT'],($option1['OPTION']['SUFIX']=='NAME' ? $option1['OPTION']['NAME']:array("","","")),$bb->ModuleCurrency());
                $arResultTemp['DESCRIPTION']=(array_key_exists($arAccountList['DESCRIPTION'],$arResult["TYPES"]) ? $arResult["TYPES"][$arAccountList['DESCRIPTION']] : $arAccountList['DESCRIPTION']);
                $this->arResult['ACCOUNTUSER'][] = $arResultTemp;
            }
        }

            foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $cls) {
                $dbAccountUser=call_user_func_array(array($cls,"getList"),
                    array(array(
                        'order'=>$this->asc,
                        'filter'=>array_merge($this->filter,array("USERID"=>$UID)),
                        'select'=>array("*"),
                    ))
                );
                while($bon=$dbAccountUser->fetch()){
                    $arResultTemp = Array();
                    $arResultTemp['ACTIVE']=($bon['ACTIVE']=='Y' ? Loc::getMessage("VBCHBB_YES") : Loc::getMessage("VBCHBB_NO"));
                    $arResultTemp['DATE']=$bon['TIMESTAMP_X'];
                    $arResultTemp['ACTIVE_FROM']=$bon['ACTIVE_FROM'];
                    $arResultTemp['ACTIVE_TO']=$bon['ACTIVE_TO'];
                    $arResultTemp['SUMMA']=$bb->ReturnCurrency($bon['BONUS'],$bon['BONUSACCOUNTSID']);
                    $arResultTemp['DESCRIPTION']=$bon['DESCRIPTION'];
                    $arResultTemp['SCORE']=($cls=='ITRound\\Vbchbbonus\\TmpTable') ? 'BITRIX' :'BONUS';
                    if($arResultTemp['SCORE']=='BITRIX'){
                        $this->arResult['ACCOUNTUSER'][] = $arResultTemp;
                    }else
                        $this->arResult['DATA'][] = $arResultTemp;
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