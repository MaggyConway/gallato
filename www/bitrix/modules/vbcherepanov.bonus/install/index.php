<?
use \Bitrix\Main;
use \Bitrix\Main\Localization;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;

global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen("/index.php"));
Loc::loadMessages($PathInstall . "/install.php");
if(class_exists("vbcherepanov_bonus")) return;
class vbcherepanov_bonus extends CModule
{
	var $MODULE_ID = "vbcherepanov.bonus";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";
	const MODULE_ID = "vbcherepanov.bonus";
	
	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("vbcherepanov.bonus_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("vbcherepanov.bonus_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("vbcherepanov.bonus_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("vbcherepanov.bonus_PARTNER_URI");
	}
  
	function DoInstall()
	{	
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		ModuleManager::registerModule($this->MODULE_ID);
		$eventManager = \Bitrix\Main\EventManager::getInstance();
	        $eventManager->registerEventHandler('sale', 'OnBeforeEventAdd', $this->MODULE_ID, 'CVbchbbEvents', 'OnBeforeEventAdd',9999);
		return true;
	}
	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/db/mysql/install.sql");
		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}

		$this->InstallEvents();

		return true;
	}

	function UninstallMAilEvent(){
        $ev='VBCH_BIGBONUS';
	    $l=\CEventMessage::GetList($by,$order,array('EVENT_NAME'=>$ev));
        while($res=$l->GetNext()){
            \CEventMessage::Delete($res['ID']);
        }
        \CEventType::Delete($ev);
    }

	function InstallFiles($arParams = array())
	{
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools",true, true);
	    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/local", $_SERVER["DOCUMENT_ROOT"]."/", true, true);
		return true;
	}
	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes/.default', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default');//css
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components');
        DeleteDirFilesEx('/bitrix/tools/'.$this->MODULE_ID.'/');//tools
        DeleteDirFilesEx('/bitrix/themes/.default/icons/'.$this->MODULE_ID.'/');//icons
		DeleteDirFilesEx('/bitrix/images/'.$this->MODULE_ID.'/');//images
		DeleteDirFilesEx('/bitrix/js/'.$this->MODULE_ID.'/');//js
		return true;
	}

	function DoUninstall()
	{	
		$this->UnInstallDB();
		$this->UnInstallFiles();
		Option::delete($this->MODULE_ID);
		Loader::includeModule('main');
		CAgent::RemoveModuleAgents($this->MODULE_ID);
		$eventManager = \Bitrix\Main\EventManager::getInstance();
	        $eventManager->UnregisterEventHandler('sale', 'OnBeforeEventAdd', $this->MODULE_ID, 'CVbchbbEvents', 'OnBeforeEventAdd');
		ModuleManager::unRegisterModule($this->MODULE_ID);
		$this->UninstallMAilEvent();
		return true;
	}
	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/db/mysql/uninstall.sql");
		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}

		$this->UnInstallEvents();
		//$DB->Query("DELETE FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'");

		return true;
	}

	function InstallEvents()
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CAcritBonusesMenu', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallEvents()
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CAcritBonusesMenu', 'OnBuildGlobalMenu');
		return true;
	}
}
?>