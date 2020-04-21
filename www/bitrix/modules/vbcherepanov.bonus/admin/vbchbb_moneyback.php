<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \ITRound\Vbchbbonus;
Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$bonusModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($bonusModulePermissions=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/prolog.php");
Loader::includeModule("sale");
Loc::loadMessages(__FILE__);
$sTableID = "tbl_bonus_moneyback";
$oSort = new CAdminSorting($sTableID, $by,$order);
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = array(
	"filter_user_id",
);
if($by=='' || $by=='id') $by='ID';
if($order=='') $order='asc';
$lAdmin->InitFilter($arFilterFields);
$arFilter = array();
if (IntVal($filter_user_id) > 0) $arFilter["USERID"] = IntVal($filter_user_id);
if(($lAdmin->GroupAction()) && $bonusModulePermissions=="W")
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList=Vbchbbonus\MoneybackTable::getList(array(
			'filter'=>$arFilter,
			'order'=>array($by=>$order),
		));
		while ($arResult = $dbResultList->fetch())
			$arID[] = $arResult['ID'];
		foreach ($arID as $ID)
		{
			if (strlen($ID) <= 0)
				continue;

			switch ($_REQUEST['action'])
			{
				case "delete":
					@set_time_limit(0);
					$DB->StartTransaction();
					if(!Vbchbbonus\MoneybackTable::delete($i))
					{
						$DB->Rollback();
						$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
					}
					$DB->Commit();
                    backmoney($i);
					break;
			}
		}
	}
	if(!is_array($ID)) $ID=(array)$ID;
	foreach($ID as $i)
	{
		if(strlen($i)<=0)
			continue;
		$i = IntVal($i);
		switch($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!Vbchbbonus\MoneybackTable::delete($i))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
				}
				$DB->Commit();
				break;
			case "active":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!Vbchbbonus\MoneybackTable::update($i,array("ACTIVE"=>"Y")))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('ERROR', $i);
				}
				$DB->Commit();
			break;
			
			case "deactive":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!Vbchbbonus\MoneybackTable::update($i,array("ACTIVE"=>"N")))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('ERROR', $i);
				}
				$DB->Commit();
			break;
			case "status1":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!Vbchbbonus\MoneybackTable::update($i,array("STATUS"=>"1")))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('ERROR', $i);
				}
				$DB->Commit();
			break;
			case "status2":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!Vbchbbonus\MoneybackTable::update($i,array("STATUS"=>"2")))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('ERROR', $i);
				}
				$DB->Commit();
			break;
			case "status3":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!Vbchbbonus\MoneybackTable::update($i,array("STATUS"=>"3")))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('ERROR', $i);
				}
				$DB->Commit();
                backmoney($i);
			break;
		}
	}
}

$dbResultList1=Vbchbbonus\MoneybackTable::getList(array(
    'filter'=>$arFilter,
    'order'=>array($by=>$order),
));
$dbResultList = new CAdminResult($dbResultList1, $sTableID);
$dbResultList->NavStart();
$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage("VBCHBB_SAA_NAV")));
$OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');
$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
	array("id"=>"LID", "content"=>Loc::getMessage("VBCH_BB_SAA_LID"), 	"sort"=>"LID", "default"=>true),
	array("id"=>"ACTIVE","content"=>Loc::getMessage("VBCH_BB_SAA_ACTIVE"), "sort"=>"ACTIVE", "default"=>true),
	array("id"=>"USERID","content"=>Loc::getMessage("VBCH_BB_SAA_USER1"), "sort"=>"USERID", "default"=>true),
	array("id"=>"ACCOUNTBONUS", "content"=>Loc::getMessage('VBCHBB_SAA_SUMBONUS'),	"sort"=>"ACCOUNTBONUS", "default"=>true),
	array("id"=>"USERREKV", "content"=>Loc::getMessage('VBCHBB_SAA_REKV_USER'),	"sort"=>"USERREKV", "default"=>true),
	array("id"=>"BONUS", "content"=>Loc::getMessage('VBCHBB_SAA_SUM'),	"sort"=>"BONUS", "default"=>true),
	array("id"=>"BACK_DATE", "content"=>Loc::getMessage("VBCHBB_SAAN_BACK_DATE"),  "sort"=>"BACK_DATE", "default"=>true),
    array("id"=>"BACK_PERIOD", "content"=>Loc::getMessage("VBCHBB_SAAN_BACK_PERIOD"),  "sort"=>"BACK_PERIOD", "default"=>true),
	array("id"=>"DESCRIPTION", "content"=>Loc::getMessage("VBCHBB_SAAN_DESCRIPTION"),  "sort"=>"", "default"=>true),
	array("id"=>"STATUS", "content"=>Loc::getMessage("VBCHBB_SAAN_STATUS"),  "sort"=>"STATUS", "default"=>true),
	
));
$SS=$BBCORE->GetSiteList();
$SS=$SS['S'];
foreach($SS as $SL){
    $site[$SL['LID']]="[".$SL['LID']."]".$SL['NAME'];
}
unset($SS);

$profMoney=$BBCORE->FilterProfiles('BACKMOYE');
$l=$BBCORE->CheckSerialize($profMoney[0]['BONUSCONFIG']);

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
while ($arAccount = $dbResultList->NavNext(true, "f_"))
{
	
	$dbResultList1=Vbchbbonus\AccountTable::getList(array(
		'filter'=>array('USER_ID'=>$f_USERID,'BONUSACCOUNTSID'=>$f_BONUSACCOUNTSID),
		'select'=>array('CURRENT_BUDGET'),
	))->fetch();
	
	$can_edit = true;
	$usr=$BBCORE->GetUserInfo($f_USERID);
	$row =&$lAdmin->AddRow($f_ID, $arAccount);
	$row->AddField("ID", $f_ID);
	$fieldValue = "[<a href=\"/bitrix/admin/user_edit.php?ID=".$f_USERID."&lang=".LANG."\" title=\"".Loc::getMessage("VBCHBB_SAA_USER_INFO")."\">".$f_USERID."</a>] ";
	$fieldValue .= htmlspecialcharsEx($usr['FIO']);
	$fieldValue .= "<br/><a href=\"mailto:".htmlspecialcharsEx($usr["EMAIL"])."\" title=\"".Loc::getMessage("VBCHBB_SAA_MAILTO")."\">".htmlspecialcharsEx($usr["EMAIL"])."</a>";
	$row->AddField("LID", $site[$f_LID]);
	$row->AddField("ACTIVE", Loc::getMessage('VBCH_BB_SAA_ACTIVE_'.$f_ACTIVE));
	$row->AddField("USERID", $fieldValue);
    $lp=floatval($dbResultList1['CURRENT_BUDGET'] );
	$row->AddField('ACCOUNTBONUS',$lp);
	$row->AddField('USERREKV',$f_USERREKV);
	$row->AddField("BONUS", floatval($f_BONUS));

	$row->AddField("BACK_DATE", $f_BACK_DATE);
	$row->AddField("BACK_PERIOD", FormatDate("f Y",MakeTimeStamp($f_BACK_PERIOD)));
	$row->AddField("DESCRIPTION", $f_DESCRIPTION);
	$row->AddField("STATUS", Loc::getMessage('VBCHBB_SAAN_STATUS_'.$f_STATUS));
	
	$fieldValue = "";
	unset($b);
	$arActions = Array();
	if($bonusModulePermissions=="W")
		$arActions[] = array(
			"ICON"=>"active",
			"TEXT"=>Loc::getMessage("bonus_act_ACT_Y"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "active")
		);
		$arActions[] = array(
			"ICON"=>"deactive",
			"TEXT"=>Loc::getMessage("bonus_act_ACT_Т"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "deactive")
		);
		$arActions[] = array(
			"ICON"=>"",
			"TEXT"=>Loc::getMessage("bonus_act_status1"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "status1")
		);
		$arActions[] = array(
			"ICON"=>"",
			"TEXT"=>Loc::getMessage("bonus_act_status2"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "status2")
		);
		$arActions[] = array(
			"ICON"=>"",
			"TEXT"=>Loc::getMessage("bonus_act_status3"),
			"ACTION"=>$lAdmin->ActionDoGroup($f_ID, "status3")
		);
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>Loc::getMessage("bonus_act_del"),
			"ACTION"=>"if(confirm('".Loc::getMessage("bonus_act_del_conf1")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
	$arActions[] = array("SEPARATOR"=>true);

	if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
		unset($arActions[count($arActions)-1]);
	$row->AddActions($arActions);
}
$lAdmin->AddAdminContextMenu();
$arFooterArray = array(
	array(
		"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
		"value" => $dbResultList->SelectedRowsCount()
	),
);
$arGroupActions = array();
$arGroupActions['delete']=Loc::getMessage("MAIN_ADMIN_LIST_DELETE");
$arGroupActions['active']=Loc::getMessage("bonus_act_ACT_Y");
$arGroupActions['deactive']=Loc::getMessage("bonus_act_ACT_Т");
$arGroupActions['status1']=Loc::getMessage("bonus_act_status1");
$arGroupActions['status2']=Loc::getMessage("bonus_act_status2");
$arGroupActions['status3']=Loc::getMessage("bonus_act_status3");


$lAdmin->AddGroupActionTable($arGroupActions);
$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
$APPLICATION->SetTitle(Loc::getMessage("SAA_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
	<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
		<?
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				Loc::getMessage("VBCH_BB_SAA_USER1"),
			)
		);
		$oFilter->Begin();
		?>

		<tr>
			<td><?echo Loc::getMessage("VBCH_BB_SAA_USER1")?>:</td>
			<td>
				<?echo FindUserID("filter_user_id", $filter_user_id, "", "find_form");?>
			</td>
		</tr>
		<?
		$oFilter->Buttons(
			array(
				"table_id" => $sTableID,
				"url" => $APPLICATION->GetCurPage(),
				"form" => "find_form"
			)
		);
		$oFilter->End();
		?>
	</form>
<?
$lAdmin->DisplayList();
function backmoney($i){
    $res=Vbchbbonus\MoneybackTable::getById($i)->fetch();
    $userac=\ITRound\Vbchbbonus\AccountTable::getList(
            [
                 'filter'=>['USER_ID'=>$res['USERID'],'BONUSACCOUNTSID'=>$res['BONUSACCOUNTSID']],
            ]
    )->fetch();
    if($userac){
        $newbalance=$userac['CURRENT_BUDGET']+$res['BONUS'];
        \Bitrix\Main\Application::getConnection()->startTransaction();
        if (!$l = \ITRound\Vbchbbonus\AccountTable::update($userac['ID'],['CURRENT_BUDGET'=>$newbalance])) {
            \Bitrix\Main\Application::getConnection()->rollbackTransaction();
        }
        \Bitrix\Main\Application::getConnection()->commitTransaction();
    }
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>