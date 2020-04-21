<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use ITRound\Vbchbbonus;
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
$sTableID = "tbl_bonus_account";
$oSort = new CAdminSorting($sTableID, $by,$order);
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = array(
	"filter_user_id",
    "filter_bonusinner",
);
if($by=='' || $by=='id') $by='ID';
if($order=='') $order='asc';
$lAdmin->InitFilter($arFilterFields);
$arFilter = array();
if (IntVal($filter_user_id) > 0) $arFilter["USER_ID"] = IntVal($filter_user_id);
if (IntVal($filter_bonusinner) > 0) $arFilter["BONUSACCOUNTSID"] = IntVal($filter_user_id);
//if (strlen($filter_login) > 0) $arFilter["USER_LOGIN"] = $filter_login;
//if (strlen($filter_user) > 0) $arFilter["USER_USER"] = $filter_user;
if(($lAdmin->GroupAction()) && $bonusModulePermissions=="W")
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList=Vbchbbonus\AccountTable::getList(array(
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
					if(!Vbchbbonus\AccountTable::delete($i))
					{
						$DB->Rollback();
						$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
					}
					$DB->Commit();
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
				if(!Vbchbbonus\AccountTable::delete($i))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
				}
				$DB->Commit();
				break;
		}
	}
}

$dbResultList1=Vbchbbonus\AccountTable::getList(array(
    'filter'=>$arFilter,
    'order'=>array($by=>$order),
));
$dbResultList = new CAdminResult($dbResultList1, $sTableID);
$dbResultList->NavStart();
$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage("VBCHBB_SAA_NAV")));
$OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');
$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
	array("id"=>"USER_ID","content"=>Loc::getMessage("VBCH_BB_SAA_USER1"), "sort"=>"USER_ID", "default"=>true),
	array("id"=>"CURRENT_BUDGET", "content"=>Loc::getMessage('VBCHBB_SAA_SUM'),	"sort"=>"CURRENT_BUDGET", "default"=>true),
	array("id"=>"TRANSACT", "content"=>Loc::getMessage("VBCHBB_SAAN_TRANSACT"),  "sort"=>"", "default"=>true),
    array("id"=>"BONUSACCOUNTSID", "content"=>Loc::getMessage("VBCHBB_SAAN_BONUSACCOUNTSID"),  "sort"=>"", "default"=>true),
));
$ss="";
$rs=Vbchbbonus\CVbchBonusaccountsTable::getList(array(
    'select'=>array('ID','NAME'),
))->fetchAll();
if(sizeof($rs)>0){
    foreach($rs as $ws){
        $BNIS['REFERENCE'][]=$ws['NAME'];
        $BNIS['REFERENCE_ID'][]=$ws['ID'];
        $BNI[$ws['ID']]=$ws['NAME'];
    }
}
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
while ($arAccount = $dbResultList->NavNext(true, "f_"))
{
	$can_edit = true;
	$usr=$BBCORE->GetUserInfo($f_USER_ID);
	$row =&$lAdmin->AddRow($f_ID, $arAccount);
	$row->AddField("ID", "<a href=\"/bitrix/admin/vbchbb_bonus_accountedit.php?ID=".$f_ID."&lang=".LANG."\">".$f_ID."</a>");
	$fieldValue = "[<a href=\"/bitrix/admin/user_edit.php?ID=".$f_USER_ID."&lang=".LANG."\" title=\"".Loc::getMessage("VBCHBB_SAA_USER_INFO")."\">".$f_USER_ID."</a>] ";
	$fieldValue .= htmlspecialcharsEx($usr['FIO']);
	$fieldValue .= "<br/><a href=\"mailto:".htmlspecialcharsEx($usr["EMAIL"])."\" title=\"".Loc::getMessage("VBCHBB_SAA_MAILTO")."\">".htmlspecialcharsEx($usr["EMAIL"])."</a>";
	$row->AddField("USER_ID", $fieldValue);
	$bns=$BBCORE->ReturnCurrency($arAccount["CURRENT_BUDGET"]);
	$row->AddField("CURRENT_BUDGET", $bns);
	$fieldValue = "";
	unset($b);
	if (in_array("TRANSACT", $arVisibleColumns))
	{
		$numTrans=Vbchbbonus\BonusTable::getList(array(
			'filter'=>array('USERID'=>$f_USER_ID,'BONUSACCOUNTSID'=>$f_BONUSACCOUNTSID),
		));
		$k=$numTrans->getSelectedRowsCount();
		if (IntVal($k) > 0)
		{
			$fieldValue .= IntVal($k);
		}
		else
		{
			$fieldValue .= 0;
		}
	}
	$ahref='<a title="'.Loc::getMessage('VBCHBB_SAAN_TRANSACTUSER').'" href="\bitrix\admin\vbchbb.php?PAGEN_1=1&SIZEN_1=20&lang=ru&set_filter=Y&adm_filter_applied=0&filter_user_id='.$f_USER_ID.'&filter_bonusinner='.$f_BONUSACCOUNTSID.'">'.$fieldValue.'</a>';
	$row->AddField("TRANSACT", $ahref);
    $row->AddField("BONUSACCOUNTSID", $BNI[$f_BONUSACCOUNTSID]);
	$arActions = Array();
	if($bonusModulePermissions=="W")
		$arActions[] = array(
			"ICON"=>"edit",
			"DEFAULT"=>true,
			"TEXT"=>Loc::getMessage("bonus_act_edit"),
			"ACTION"=>$lAdmin->ActionRedirect("vbchbb_bonus_accountedit.php?ID=".$f_ID),
		);
	if($bonusModulePermissions=="W")
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>Loc::getMessage("bonus_act_del"),
			"ACTION"=>"if(confirm('".Loc::getMessage("bonus_act_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
	$arActions[] = array("SEPARATOR"=>true);

	if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
		unset($arActions[count($arActions)-1]);
	$row->AddActions($arActions);
}

$arFooterArray = array(
	array(
		"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
		"value" => $dbResultList->SelectedRowsCount()
	),
);
$aContext = array(
	array(
		"TEXT"=>Loc::getMessage("VBCH_BB_ADD_C"),
		"LINK"=>"vbchbb_bonus_accountedit.php?lang=".LANG,
		"TITLE"=>Loc::getMessage("BB_ADD_TITLE"),
		"ICON"=>"btn_new",
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->AddGroupActionTable(Array(
	"delete"=>Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
));
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
				Loc::getMessage("VBCHBB_SAA_USER_ID"),
				Loc::getMessage("VBCHBB_SAAN_BONUSACCOUNTSID"),
			)
		);
		$oFilter->Begin();
		?>

		<tr>
			<td><?echo Loc::getMessage("VBCHBB_SAA_USER_ID")?>:</td>
			<td>
				<?echo FindUserID("filter_user_id", $filter_user_id, "", "find_form");?>
			</td>
		</tr>

        <tr>
            <td><?echo Loc::getMessage("VBCHBB_SAAN_BONUSACCOUNTSID")?>:</td>
            <td>
                <?echo SelectBoxFromArray('filter_bonusinner', $BNIS,$filter_bonusinner, 'ALL');?>
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
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>