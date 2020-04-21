<?
define('NO_AGENT_CHECK', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main;
use Bitrix\Catalog;
use \ITRound\Vbchbbonus;
Main\Loader::includeModule('vbcherepanov.bonus');
global $USER;

if (isset($_POST['AJAX']) && $_POST['AJAX'] == 'Y'){
	if (isset($_POST['TYPE']) && isset($_POST['SITE_ID']))
	{
		$type = $_POST['TYPE'];
		$siteID = '';
		if (preg_match('/^[a-z0-9_]{2}$/i', (string)$_POST['SITE_ID']) === 1)
			$siteID = (string)$_POST['SITE_ID'];
		if($type=='CART'){
			$bb=new Vbchbbonus\Vbchbbcore();
			$bb->SITE_ID=$siteID;
			$option1=$bb->GetOptions($siteID,'BONUSNAME');
			if($bb->CheckSiteOn()){
				$bonus=$bb->GetCartOrderBonus($type);
				echo $bb->declOfNum($bonus,($option1['OPTION']['SUFIX']=='NAME' ? $option1['OPTION']['NAME']:array("","","")),$bb->ModuleCurrency());
			}
		}
	}
	die();
}