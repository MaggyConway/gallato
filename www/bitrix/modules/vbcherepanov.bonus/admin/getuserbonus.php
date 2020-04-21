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
if($userid && \Bitrix\Main\Loader::includeModule("vbcherepanov.bonus")){
    $BBCORE=new \ITRound\Vbchbbonus\Vbchbbcore();
    $dbAccountUser=\ITRound\Vbchbbonus\AccountTable::getList(array(
        'filter'=>array("USER_ID"=>$userid),
    ))->fetchAll();
    $k=array();
    if(sizeof( $dbAccountUser)>0){
        foreach( $dbAccountUser as $qa){
            $k[$qa['BONUSACCOUNTSID']]=array('BUDGET'=>$qa['CURRENT_BUDGET'],'BNC'=>$qa['BONUSACCOUNTSID']);
        }
    }
    $rs=\ITRound\Vbchbbonus\CVbchBonusaccountsTable::getList(array(
        'filter'=>array('ID'=>array_keys($k),'ACTIVE'=>"Y"),
    ))->fetchAll();
    if(sizeof($rs)>0){
        foreach($rs as $sr){
            $stng=$BBCORE->CheckSerialize($sr['SETTINGS']);
            $r=array();
            if($stng['SUFIX']=='NAME' && sizeof($stng['NAME'])>0){
                foreach($stng['NAME'] as $w){
                    $r[]=$w;
                }
            }
            $k[$sr['ID']]['NAME']=$r;
            $k[$sr['ID']]['NM']=$sr['NAME'];
            $k[$sr['ID']]['CUR']=$stng['CURRENCY'];
        }
    }

    $q1='';$q2='';
    foreach($k as $kk){

        $q1.='<td>'.$kk['NM'].'</td><td class="separator"></td>';
        $val=$BBCORE->ReturnCurrency($kk['BUDGET']);
        $q2.='<td id="bnscnt">'.$val.'</td><td class="separator"></td>';
    }
    $str='<table class="adm-bus-pay-statuspay" style="width:100%"><thead>';
    $str.='<tr>'.$q1.'</tr></thead>';
    $str.='<body><tr>'.$q2;
    $str.='</tr></tbody>
						</table>';

    echo $str;die();
}