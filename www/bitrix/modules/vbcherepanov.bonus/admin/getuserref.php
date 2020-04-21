<?php
define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("DisableEventsCheck", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

$siteId = isset($_REQUEST['SITE_ID']) && is_string($_REQUEST['SITE_ID']) ? $_REQUEST['SITE_ID'] : '';
$siteId = substr(preg_replace('/[^a-z0-9_]/i', '', $siteId), 0, 2);
if (!empty($siteId) && is_string($siteId))
{
    define('SITE_ID', $siteId);
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

$userid=$request->getPost('userid');
if($userid && $userid!=0 && \Bitrix\Main\Loader::includeModule("vbcherepanov.bonus")){
    $APPLICATION->IncludeComponent(
        "vbcherepanov:vbcherepanov.refreg",
        "refreg",
        Array(
            "CACHE_TIME" => "3600",
            "REF_USER_ID"=> $userid,
            "CACHE_TYPE" => "N",
        ),false
    );
}else{
    $APPLICATION->IncludeComponent(
        "vbcherepanov:vbcherepanov.refreg",
        "refreg",
        Array(
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "N",
        ),false
    );
}