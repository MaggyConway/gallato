<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use ITRound\Vbchbbonus;
Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$bonusModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($bonusModulePermissions=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
Loc::loadMessages(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/prolog.php");
$sTableID = "tbl_vbchbonus_affiliate";
$oSort = new CAdminSorting($sTableID, $by, $order);
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
		$dbResultList=Vbchbbonus\BonusCardTable::getList(array(
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
					Application::getConnection()->startTransaction();
					if(!Vbchbbonus\BonusCardTable::delete($i))
					{
						Application::getConnection()->rollbackTransaction();
						$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
					}
					Application::getConnection()->commitTransaction();
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
				Application::getConnection()->startTransaction();
				if(!Vbchbbonus\BonusCardTable::delete($i))
				{
					Application::getConnection()->rollbackTransaction();
					$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
				}
				Application::getConnection()->commitTransaction();
				break;
			case "active":
				@set_time_limit(0);
				Application::getConnection()->startTransaction();
				$trns=Vbchbbonus\BonusCardTable::getById($i)->fetch();
				$trns['ACTIVE']='Y';
				unset($trns['ID']);
				if(!Vbchbbonus\BonusCardTable::update($i,$trns))
				{
					Application::getConnection()->rollbackTransaction();
				}
				Application::getConnection()->commitTransaction();
				unset($trns);
				break;
		}
	}
}
$dbResultList1 = Vbchbbonus\BonusCardTable::getList(array(
	'filter'=>$arFilter,
	'order'=>array($by=>$order),
));
$dbResultList = new CAdminResult($dbResultList1, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("VBCHBB_SAACARD_NAV")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
	array("id"=>"TIMESTAMP","content"=>Loc::getMessage("VBCHBB_BONUSCARD_TIMESTAMP"),"sort"=>"TIMESTAMP_X","default"=>true),
	array("id"=>"LID","content"=>Loc::getMessage("VBCHBB_BONUSCARD_LID"),"sort"=>"LID","default"=>true),
	array("id"=>"ACTIVE","content"=>Loc::getMessage("VBCHBB_BONUSCARD_ACTIVE"),"sort"=>"ACTIVE","default"=>true),
	array("id"=>"USERID","content"=>Loc::getMessage("VBCHBB_BONUSCARD_USERID"),"sort"=>"USERID","default"=>true),
	array("id"=>"NUM","content"=>Loc::getMessage("VBCHBB_BONUSCARD_NUM"),"sort"=>"NUM","default"=>true),
	array("id"=>"DEFAULTBONUS","content"=>Loc::getMessage("VBCHBB_BONUSCARD_DEFAULTBONUS"),"sort"=>"DEFAULTBONUS","default"=>true),
	array("id"=>"BONUSACCOUNTS","content"=>Loc::getMessage("VBCHBB_BONUSCARD_BONUSACCOUNTS"),"sort"=>"BONUSACCOUNTS","default"=>true),

));
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
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
$OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');


while ($arAccount = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arAccount);

	$usr=$BBCORE->GetUserInfo($f_USERID);
	$row->AddField("ID", "<a href=\"/bitrix/admin/vbchbb_bonuscard_edit.php?ID=".$f_ID."&lang=".LANG."\">".$f_ID."</a>");
	$row->AddField("TIMESTAMP", $f_TIMESTAMP_X);
	$row->AddField("LID", $f_LID);
	$row->AddField("ACTIVE", $f_ACTIVE=="Y" ? Loc::getMessage("VBCH_BONUSCARD_YES") : Loc::getMessage("VBCH_BONUSCARD_NO"));
	$fieldValue = "[<a href=\"/bitrix/admin/user_edit.php?ID=".$f_USERID."&lang=".LANG."\" title=\"".Loc::getMessage("VBCHBB_SAA_USER_INFO")."\">".$f_USERID."</a>] ";
	$fieldValue .= htmlspecialcharsEx($usr['FIO']);
	$fieldValue .= "<br/><a href=\"mailto:".htmlspecialcharsEx($usr["EMAIL"])."\" title=\"".Loc::getMessage("VBCHBB_SAA_MAILTO")."\">".htmlspecialcharsEx($usr["EMAIL"])."</a>";
	$row->AddField("USERID", $fieldValue);
	$row->AddField("NUM", $f_NUM);
	$bns=$BBCORE->ReturnCurrency($f_DEFAULTBONUS,$f_BONUSACCOUNTS);

	$row->AddField("DEFAULTBONUS", $bns);
	$row->AddField("BONUSACCOUNTS",  $BNI[$f_BONUSACCOUNTS]);
	$arActions = Array();
	if($bonusModulePermissions=="W") {
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON" => "delete", "TEXT" => Loc::getMessage("bonus_card_act_del"), "ACTION" => "if(confirm('" . Loc::getMessage('bonus_card_act_del_conf') . "')) " . $lAdmin->ActionDoGroup($f_ID, "delete"));
		if($f_ACTIVE=="N")
			$arActions[] = array("ICON" => "active", "TEXT" => Loc::getMessage("bonus_card_act_active"), "ACTION" =>  $lAdmin->ActionDoGroup($f_ID, "active"));
	}
	$row->AddActions($arActions);
}
$lAdmin->AddGroupActionTable(
	array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
	)
);
$lAdmin->AddAdminContextMenu();
$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResultList->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);
$aContext = array(
	array(
		"TEXT"=>Loc::getMessage("VBCH_AFF_ADD_CARD"),
		"LINK"=>"vbchbb_bonuscard_edit.php?lang=".LANG,
		"TITLE"=>Loc::getMessage("VBCH_AFF_ADD_CARD"),
		"ICON"=>"btn_new",
	),
	array(
		"TEXT"=>Loc::getMessage("VBCH_AFF_IMPORT_CARD"),
		"LINK"=>"vbchbb_bonuscard_import.php?lang=".LANG,
		"TITLE"=>Loc::getMessage("VBCH_AFF_IMPORT_CARD"),
		"ICON"=>"btn_new",
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_SAACARD_NAV"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
	<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
		<?
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				Loc::getMessage("VBCHBB_CARD_USER_ID"),
			)
		);
		$oFilter->Begin();
		?>

		<tr>
			<td><?echo Loc::getMessage("VBCHBB_CARD_USER_ID")?>:</td>
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

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>