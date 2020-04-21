<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;
use \Bitrix\Main\Localization\Loc as Loc;
use \ITRound\Vbchbbonus;
use \Bitrix\Main\Context;

class RefRegInput extends \CBitrixComponent
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
    public function onPrepareComponentParams($result)
    {
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
            throw new Main\LoaderException(Loc::getMessage('VBCHBB_MODULE_NOT_INSTALL'));
	}

    /**
     * check required input params
     * @throws SystemException
     */
    protected function checkParams()
    {
        /*if ($this->arParams['IBLOCK_ID'] <= 0)
            throw new Main\ArgumentNullException('IBLOCK_ID');
        */
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

        global $USER,$APPLICATION;
		$bb=new Vbchbbonus\Vbchbbcore();
		$bb->SITE_ID=$bb->GetSiteID();
		$option1=$bb->GetOptions($bb->SITE_ID,'REFACTIVE');
		$option2=$bb->GetOptions($bb->SITE_ID,'REFPARAM');
		if($option1['OPTION']=='Y'){
    		$request = Context::getCurrent()->getRequest();
            $ref = $request->getQueryList()->toArray();
            $REFERER = $ref[$option2['OPTION']];
			if(!empty($REFERER)){
					 $res=Vbchbbonus\CVbchRefTable::getList(array(
					'filter'=>array(
						'REFERER'=>$REFERER,
						'ACTIVE'=>'Y',
						'LID'=>$bb->SITE_ID,
					),
					'select'=>array('ID','USERID','NAME'=>'USER_ID.NAME','LAST_NAME'=>'USER_ID.LAST_NAME','EMAIL'=>'USER_ID.EMAIL'),
				))->fetch();
				if($res){
					$this->arResult['REF_NAME'] = $res['NAME'].'&nbsp;'.$res['LAST_NAME'];
					$this->arResult['REF_ID']=$res['USERID'];
					$this->arResult['REF_VALUE']=$REFERER;
				}

			}else{
                $REF_BONUS=\ITRound\Vbchbbonus\Vbchreferal::GetCookie("REFEREFBONUS");
                if(!empty($REF_BONUS)){
                    $res=Vbchbbonus\CVbchRefTable::getList(array(
                        'filter'=>array(
                            'COOKIE'=>$REF_BONUS,
                            'ACTIVE'=>'Y',
                            'LID'=>$bb->SITE_ID,
                        ),
                        'select'=>array('ID','USERID','NAME'=>'USER_ID.NAME','LAST_NAME'=>'USER_ID.LAST_NAME','EMAIL'=>'USER_ID.EMAIL','REFFROM'),
                    ))->fetch();
                    if($res){
                        $res1=Vbchbbonus\CVbchRefTable::getList(array(
                            'filter'=>array(
                                'USERID'=>$res['REFFROM'],
                                'ACTIVE'=>'Y',
                                'LID'=>$bb->SITE_ID,
                            ),
                            'select'=>array('ID','USERID','NAME'=>'USER_ID.NAME','LAST_NAME'=>'USER_ID.LAST_NAME','EMAIL'=>'USER_ID.EMAIL','REFERER'),
                        ))->fetch();
                        if($res1){
                            $this->arResult['REF_NAME'] = $res1['NAME'].'&nbsp;'.$res1['LAST_NAME'];
                            $this->arResult['REF_ID']=$res1['USERID'];
                            $this->arResult['REF_VALUE']=$res1['REFERER'];
                        }

                    }
                }
            }
				$this->arResult['UNICODE']='refreg'.microtime();
				$this->arResult['FIELDNAME']='REFERCODE';
                $this->arResult['SITE_ID']=$bb->SITE_ID;
				$this->arResult['AJAX_PATH']=$this->getPath().'/ajax.php';
				if($this->arParams['REF_USER_ID']!='' || $this->arParams['REF_USER_ID']!=0){
                $res=\ITRound\Vbchbbonus\Vbchreferal::GetRefFrom($this->arParams['REF_USER_ID'],$bb->SITE_ID);
                if($res['REFFROM']==0 || $res['REFFROM']==''){
                    $userfrom=212024;
                }else{
                    $userfrom=$res['REFFROM'];
                }
                $res1=Vbchbbonus\CVbchRefTable::getList(array(
                    'filter'=>array(
                        'USERID'=>$userfrom,
                        'ACTIVE'=>'Y',
                        'LID'=>$bb->SITE_ID,
                    ),
                    'select'=>array('ID','USERID','NAME'=>'USER_ID.NAME','LAST_NAME'=>'USER_ID.LAST_NAME','EMAIL'=>'USER_ID.EMAIL','REFERER'),
                ))->fetch();
                if($res1){
                    $this->arResult['REF_NAME'] = $res1['NAME'].'&nbsp;'.$res1['LAST_NAME'];
                    $this->arResult['REF_ID']=$res1['USERID'];
                    $this->arResult['REF_VALUE']=$res1['REFERER'];
                }
            }
		}else{
			$this->arResult['ERROR']=true;
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
            $this->getResult();
            $this->includeComponentTemplate();
            $this->executeEpilog();
        }
        catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}