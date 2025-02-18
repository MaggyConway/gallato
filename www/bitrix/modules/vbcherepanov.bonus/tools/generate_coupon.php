<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals;
$module_id="vbcherepanov.bonus";
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

Loc::loadMessages(__FILE__);

global $USER;
if($type=='saveData'){

    \Bitrix\Main\Config\Option::set($module_id,'GENERATE_COUPON_DATA',base64_encode(serialize($data)));
    $result=array(
        'STATUS' => 'OK',
    );
    echo CUtil::PhpToJSObject($result);
    die();
}
$result = array(
    'STATUS' => 'OK',
    'MESSAGE' => '',
    'COUPON' => '',
);

if ($result['STATUS'] == 'OK')
{
    if (!isset($USER) || !($USER instanceof CUser))
    {
        $result['STATUS'] = 'ERROR';
        $result['MESSAGE'] = Loc::getMessage('BX_SALE_TOOLS_GEN_CPN_ERR_USER');
    }
    elseif (!$USER->IsAuthorized())
    {
        $result['STATUS'] = 'ERROR';
        $result['MESSAGE'] = Loc::getMessage('BX_SALE_TOOLS_GEN_CPN_ERR_AUTH');
    }
}

if ($result['STATUS'] == 'OK')
{
    if (!check_bitrix_sessid())
    {
        $result['STATUS'] = 'ERROR';
        $result['MESSAGE'] = Loc::getMessage('BX_SALE_TOOLS_GEN_CPN_ERR_SESSION');
    }
}

if ($result['STATUS'] == 'OK')
{
    if (!Loader::includeModule($module_id))
    {
        $result['STATUS'] = 'ERROR';
        $result['MESSAGE'] = Loc::getMessage('BX_SALE_TOOLS_GEN_CPN_ERR_MODULE');
    }
}

if ($result['STATUS'] == 'OK')
{
    $result['COUPON'] = \ITRound\Vbchbbonus\CouponTable::GenerateCoupon(true,$mask);
}

echo CUtil::PhpToJSObject($result);