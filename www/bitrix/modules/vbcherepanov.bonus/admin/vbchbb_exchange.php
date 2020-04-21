<?
define('BX_SESSION_ID_CHANGE', false);
define('BX_SKIP_POST_UNQUOTE', true);
define('NO_AGENT_CHECK', true);
define("STATISTIC_SKIP_ACTIVITY_CHECK", true);

if (isset($_REQUEST["type"]) && $_REQUEST["type"] == "bonus")
{
    define("ADMIN_SECTION", true);
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if($type=="bonus")
{
    $APPLICATION->IncludeComponent("vbcherepanov.bonus:bonus.export.1c", "", Array(
            "SITE_LIST" => COption::GetOptionString("sale", "1C_SALE_SITE_LIST", ""),
            "GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("sale", "1C_SALE_GROUP_PERMISSIONS", "1")),
            "USE_ZIP" => COption::GetOptionString("sale", "1C_SALE_USE_ZIP", "Y"),
            "INTERVAL" => COption::GetOptionString("sale", "1C_INTERVAL", 30),
            "FILE_SIZE_LIMIT" => COption::GetOptionString("sale", "1C_FILE_SIZE_LIMIT", 200*1024),
            "IMPORT_NEW_BONUS" => COption::GetOptionString("vbcherepanov.bonus", "1C_IMPORT_NEW_BONUS", "N"),
        )
    );
}elseif($type=="get_bonus")
{
    $APPLICATION->IncludeComponent("vbcherepanov.bonus:bonus.import.1c", "", Array(
            "INTERVAL" => COption::GetOptionString("catalog", "1CE_INTERVAL", "-"),
            "GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("catalog", "1CE_GROUP_PERMISSIONS", "1")),
            "USE_ZIP" => COption::GetOptionString("catalog", "1CE_USE_ZIP", "Y"),
        )
    );
}
elseif($type=="listen")
{
    $APPLICATION->RestartBuffer();

    CModule::IncludeModule('sale');
    CModule::IncludeModule('vbcherepanov.bonus');
    $timeLimit = 60;//1 minute
    $startExecTime = time();
    $max_execution_time = (intval(ini_get("max_execution_time")) * 0.75);
    $max_execution_time = ($max_execution_time > $timeLimit )? $timeLimit:$max_execution_time;

    if(CModule::IncludeModule("sale") && defined("CACHED_b_sale_bonus"))
    {
        while(!$CACHE_MANAGER->getImmediate(CACHED_b_sale_bonus, "sale_bonus"))
        {
            usleep(1000);

            if(intVal(time() - $startExecTime) > $max_execution_time)
            {
                break;
            }
        }
    }

    if($CACHE_MANAGER->getImmediate(CACHED_b_sale_bonus, "sale_bonus"))
    {
        echo "success\n";
    }
    else
    {
        CHTTP::SetStatus("304 Not Modified");
    }
}
else
{
    $APPLICATION->RestartBuffer();
    echo "failure\n";
    echo "Unknown command type.";
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>