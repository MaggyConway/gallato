<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SystemException;
Loc::loadMessages(__FILE__);

class CompMoneyBack extends \CBitrixComponent
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
		global $USER;
		$bb=new Vbchbbonus\Vbchbbcore();
	    $option1=$bb->GetOptions(SITE_ID,'BONUSNAME');
		$bb->SITE_ID=SITE_ID;
		$this->arResult['DATE']=date(\CDatabase::DateFormatToPHP(CSite::GetDateFormat("SHORT", SITE_ID)));
        $uid=intval($USER->GetID());
        $arFields=[
            'USERID'=>$uid,
            'USERGROUP'=>$USER->GetUserGroupArray(),
        ];
		$profile=\ITRound\Vbchbbonus\CvbchbonusprofilesTable::getList(
		    [
		        'filter'=>['ID'=>$this->arParams['PROFILEOUT']],
            ]
        );

        $backmoneyUser=\ITRound\Vbchbbonus\MoneybackTable::getList(array(
            'filter'=>array('LID'=>$bb->SITE_ID,'ACTIVE'=>'Y','USERID'=>$uid),
            'select'=>array('*')
        ))->fetchAll();
        $BACKMONEYCHECK=0;
        $BACKMONEYWAIT=0;
        if($backmoneyUser){
            foreach($backmoneyUser as $bmus){
                if($bmus['STATUS']==1){
                    $BACKMONEYWAIT+=floatval($bmus['BONUS']);
                }elseif($bmus['STATUS']==2){
                    $BACKMONEYCHECK+=floatval($bmus['BONUS']);
                }elseif($bmus['STATUS']==3){
                    //$this->arResult['BACKMONEYCHECK']+=floatval($bmus['BONUS']);
                }
            }
        }

		while($prof=$profile->fetch()){
		    $k=$prof;
		    $k['NOTIFICATION']=$bb->CheckSerialize($prof['NOTIFICATION']);
            $k['FILTER']=$bb->CheckSerialize($prof['FILTER']);
            $k['BONUSCONFIG']=$bb->CheckSerialize($prof['BONUSCONFIG']);
            $k['SETTINGS']=$bb->CheckSerialize($prof['SETTINGS']);
            $Filter = call_user_func_array(array($bb->INSTALL_PROFILE[$k['TYPE']], "GetRules"), array($bb->FUNC_GETRULES[$k['TYPE']],$prof['ID'], $k['FILTER'], $arFields));
            if($Filter)
                $arFields['ACCOUNT']=floatval($bb->GetUserBonus($arFields['USERID'],'BONUSPAY',$k['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER']));
		        $data=[
		            'PROFILE_DATA'=>$k,
                    'MAXSUM'=>$ms=(int)call_user_func_array(array($bb->INSTALL_PROFILE[$k['TYPE']], "GetBonus"), array($bb->FUNC_GETBONUS[$k['TYPE']],$k, $arFields)),
                    'MAXSUM_FORMATED'=>$bb->ReturnCurrency($ms,$k['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER']),
                    'SUMM'=>$arFields['ACCOUNT'],
                    'SUMM_FORMAT'=>$bb->ReturnCurrency($arFields['ACCOUNT'],$k['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER']),
                    'OUT'=>$BACKMONEYCHECK,
                    'OUT_FORMAT'=>$bb->ReturnCurrency($BACKMONEYCHECK,$k['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER']),
                    'WAIT'=>$BACKMONEYWAIT,
                    'WAIT_FORMAT'=>$bb->ReturnCurrency($BACKMONEYWAIT,$k['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER']),
                ];
                $profil[$k['ID']]=$data;
		    unset($data);
        }
        $this->arResult['DATA']=$profil;

    	if($this->arParams['SHOWBUTTON']=='Y'){
			if (check_bitrix_sessid() && !empty($_REQUEST["waitmoney"])) {
                foreach ($profil as $proff) {
                    $countback = floatval(htmlspecialchars($_REQUEST['COUNTBACK']));
                    if ($countback != ''&& $countback>0  && $countback != 0 && $countback <= $proff['SUMM'] && $countback <= $proff['MAXSUM']) {
                        $arFields = array(
                            'ACTIVE' => "Y",
                            'USERID' => $uid,
                            'BONUS' => floatval($_REQUEST['COUNTBACK']),
                            'LID' => $bb->SITE_ID,
                            'STATUS' => 1,
                            'BONUSACCOUNTSID'=>$proff['PROFILE_DATA']['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER'],
                            'TIMESTAMP_X' => new \Bitrix\Main\Type\DateTime(),
                            'BACK_DATE' => new \Bitrix\Main\Type\DateTime(),
                            'BACK_PERIOD' => new \Bitrix\Main\Type\DateTime(),
                        );
                        if ($this->arParams['SHOWFIELDSPARAMS'] == 'Y') {
                            $rekv = trim(htmlspecialchars($_REQUEST['ACCOUNTS']));
                            if (strlen($rekv) != 0) {
                                $arFields['USERREKV'] = $rekv;
                            }
                        }
                        \Bitrix\Main\Application::getConnection()->startTransaction();
                        if (!$l = \ITRound\Vbchbbonus\MoneybackTable::add($arFields)) {
                            \Bitrix\Main\Application::getConnection()->rollbackTransaction();
                        }
                        \Bitrix\Main\Application::getConnection()->commitTransaction();
                        $newsum=$proff['SUMM']-$countback;
                        $kl=\ITRound\Vbchbbonus\AccountTable::getList(
                            [
                                'filter'=>['USER_ID'=>$uid,'BONUSACCOUNTSID'=>$proff['PROFILE_DATA']['BONUSCONFIG']['BONUSINNEROUT']['BONUSINNER']],
                                'select'=>['ID'],
                            ]
                        )->fetch();

                        if($kl){
                            \Bitrix\Main\Application::getConnection()->startTransaction();
                            if (!$l = \ITRound\Vbchbbonus\AccountTable::update($kl['ID'],['CURRENT_BUDGET'=>$newsum])) {
                                \Bitrix\Main\Application::getConnection()->rollbackTransaction();
                            }
                            \Bitrix\Main\Application::getConnection()->commitTransaction();
                        }

                        $arFields = Array(
                            'FIO' => $USER->GetFullName(),
                            'EMAIL' => $USER->GetEmail(),
                            'COUNT' => floatval($_REQUEST['COUNTBACK']),
                        );
                        if (!empty($this->arParams["EVENT_MESSAGE_ID"])) {
                            foreach ($this->arParams["EVENT_MESSAGE_ID"] as $v)
                                if (IntVal($v) > 0)
                                    CEvent::Send($this->arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
                        } else
                            CEvent::Send($this->arParams["EVENT_NAME"], SITE_ID, $arFields);

                    } else {
                        $this->arResult['ERROR'] = 'Введенная сумма превышает баланс счета';
                        $_SESSION["COUNTBACK"] = htmlspecialcharsbx($_REQUEST['COUNTBACK']);
                    }
                }
            }
        	$_SESSION["COUNTBACK"] = 0;
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