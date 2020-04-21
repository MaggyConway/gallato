<?
define('NO_AGENT_CHECK', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main;
use ITRound\Vbchbbonus;
Main\Loader::includeModule('vbcherepanov.bonus');
if (isset($_POST['BBLIKEAJAX']) && $_POST['BBLIKEAJAX'] == 'Y')
{
    $siteID = '';
    if (preg_match('/^[a-z0-9_]{2}$/i', (string)$_POST['SITEID']) === 1)
        $siteID = (string)$_POST['SITEID'];
    $type=$_POST['TYPE'];
    $postid=$_POST['POSTID'];
    $URL=$_POST['URL'];
    $bb=new Vbchbbonus\Vbchbbcore();
    $bb->SITE_ID=$siteID;
    $result=$bb->runSocialBonus($type,$URL,$postID);
    echo CUtil::PhpToJSObject($result);
    die();
}