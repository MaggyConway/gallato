<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use \ITRound\Vbchbbonus;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Context;
CJSCore::Init(array("jquery"));
Loc::loadMessages(__FILE__);
CJSCore::Init(array('jquery'));
class CompBonusLike extends \CBitrixComponent
{
	const MODULE_ID="vbcherepanov.bonus";
	public function executeComponent()
	{
		try
		{
			self::checkModules();
			self::checkAuth();
			$this->prepareData();
			$this->includeComponentTemplate();
		}
		catch (SystemException $e)
		{
			$this->abortResultCache();
			ShowError($e->getMessage());
		}
	}
	protected static function checkAuth(){
		global $USER;
		if(!$USER->IsAuthorized()){
			throw new SystemException(Loc::getMessage('VBCHBB_ACCESS_DENIED'));
		}
	}
	protected static function checkModules()
	{
		if (!Loader::includeModule(self::MODULE_ID))
			throw new SystemException(Loc::getMessage('VBCHBB_MODULE_NOT_INSTALL'));

	}
	public function onPrepareComponentParams($arParams)
	{
		return $arParams = parent::onPrepareComponentParams($arParams);
	}
	protected function prepareData()
	{
		$bonus=new Vbchbbonus\Vbchbbcore();
		$bonus->SITE_ID=SITE_ID;
		if($bonus->CheckSiteOn()){
			global $APPLICATION;
			$name = $APPLICATION->GetTitle();
			$request = Context::getCurrent()->getRequest();
			$domain= $request->getHttpHost();
			$domain= str_replace(array('https://', 'http://', '/'), '', $domain);
			$protokol = ($request->isHttps() ? 'https' : 'http');
			$url = $request->getRequestUri();
			$st=SiteTable::getList(array(
				'filter'=>array("LID"=>$bonus->SITE_ID),
				'select'=>array("NAME"),
			))->fetch();
			$repl=array("#URL#","#NAME#","#DESCRIPTION#","#SERVER_NAME#","#SITE_NAME#");
			$newp=array(
				$protokol."://".$domain.$url,
				($this->arParams["NAME"] ? $this->arParams["NAME"] : $name),
				$this->arParams["DESCRIPTION"],
				$protokol."://".$domain,
				$st["NAME"]
			);
			$this->arResult['NAME']=($this->arParams["NAME"] ? $this->arParams["NAME"] : $name);
			$this->arResult['URL']=$protokol."://".$domain.$url;
			$this->arResult['SITE_NAME']=$st['NAME'];
			$this->arResult['ADDBONUS']=$this->getPath()."/ajax.php";
			$this->arResult['JSCLASS'] = 'bb'.preg_replace("/[^a-zA-Z0-9_]/", "x", md5(time()));
			$this->arResult['SERVER_NAME']=$protokol."://".$domain;
			$soc_profiles=Vbchbbonus\CvbchbonusprofilesTable::getList(array(
					'filter'=>array('ACTIVE'=>'Y','SITE'=>$bonus->SITE_ID,"TYPE"=>"SOCIAL"),
			))->fetchAll();
			$OPTION_SOCIAL=$bonus->GetOptions($bonus->SITE_ID,'SOCIAL');
			$OPTION_SOCIAL=$OPTION_SOCIAL['OPTION'];
			$COUNTS='';
			if($bonus->CheckArray($soc_profiles)){
				foreach($soc_profiles as $social){
					$filter=$bonus->CheckSerialize($social['FILTER']);
					$notif=$bonus->CheckSerialize($social['NOTIFICATION']);
					$notif=$notif['SENDSOCIAL'];
					$SSC=$filter['TYPESOURCE'];
					$COUNTS[$SSC]=Vbchbbonus\CvbchbonussocpushTable::getList(array(
							'filter'=>array('SOCIAL'=>$SSC),
						))->getSelectedRowsCount();
					if($SSC=="TW"){
						$button[$SSC]="
					        <a href=\"#\" onclick=\"\" class=\"".$SSC."_icon\"></a>
							<a 	href=\"https://twitter.com/share\"
    							class=\"twitter-share-button\"
    							data-lang=\"ru\"
    							data-size=\"large\"
    							data-count=\"none\"
								data-url=\"".$this->arResult['URL']."\",
    							data-text=\"".str_replace($repl,$newp,trim($notif))."\",
    							data-counturl=\"".$this->arResult['URL']."\">
    						</a>";
					}else{
						$button[$SSC]="
					<a href=\"#\" onClick=\"".$this->arResult['JSCLASS'].".".$SSC."();return false;\" class=\"".$SSC."_icon\"/></a>
				";
					}
					$this->arResult['DESCRIPTION'][$SSC]=str_replace($repl,$newp,trim($notif));
				}
			}
			$this->arResult['BUTTON']=$button;
			$this->arResult['COUNTS']=$COUNTS;
			$this->arResult['SOCIAL']=$OPTION_SOCIAL;
		}
	}
}