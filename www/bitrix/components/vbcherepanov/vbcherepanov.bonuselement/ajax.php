<?
define('NO_AGENT_CHECK', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main;
use ITRound\Vbchbbonus;
Main\Loader::includeModule('vbcherepanov.bonus');
global $USER;

if (isset($_POST['AJAX']) && $_POST['AJAX'] == 'Y'){
	if (isset($_POST['PRODUCT_ID']) && isset($_POST['SITE_ID']))
	{
		$productID = $_POST['PRODUCT_ID'];
		$iblockID = $_POST['IBLOCK_ID'];
		$count = (int)$_POST['COUNTS'];
        $type=boolval($_POST['TYPE']);
        $result=$_POST['RESULT'];
		$price=$_POST['MIN_PRICE'];
		$template=$_POST['TEMPLATE'];
		$siteID = '';
		if (preg_match('/^[a-z0-9_]{2}$/i', (string)$_POST['SITE_ID']) === 1)
			$siteID = (string)$_POST['SITE_ID'];
		if($template!=''){
            ob_start();
            $APPLICATION->IncludeComponent("vbcherepanov:vbcherepanov.bonuselement",$template,
                Array(
                    "CACHE_TIME" => "0",
                    "CACHE_TYPE" => "N",
                    "ELEMENT" => $arResult, //передаем весь результирующий массив в компонент
                    'ELEMENT_ID'=>$productID,
                    'IBLOCK_ID'=>$iblockID,
                    'COUNT'=>$count,
                    "OFFERS_AR" => "OFFERS", //ключ массива $arResult в котором находятся торговые предложения
                    "OFFERS_ID" => "OFFER_ID_SELECTED", //ключ массива $arResult с ID выбранного торгового предложения
                    "ONLY_NUM" => "N", //возвратит бонус в виде числа без валюты
                )
            );
            echo ob_get_clean(); // сохраняем вывод бонусов в переменную массива
        }else{
            $bb=new Vbchbbonus\Vbchbbcore();
            $bb->SITE_ID=$siteID;
            $option1=$bb->GetOptions($siteID,'BONUSNAME');
            $pr=unserialize(base64_decode($price));
            if($result)
                $arResult=unserialize(base64_decode($result));
            else $result=array();
            $pr=$pr[$productID];
            $bonus=$bb->GetBonusElements($productID,$iblockID,$count,$pr,$arResult);
            if($type)
                echo $bonus;
            else echo $bb->ReturnCurrency($bonus);
        }
    }
	die();
}