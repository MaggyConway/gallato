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
Loader::includeModule("sale");
Loc::loadMessages(__FILE__);

$sTableID = "tbl_vbchdouble";
$oSort = new CAdminSorting($sTableID, $by, $order);
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
if (IntVal($filter_bonusinner) > 0) $arFilter["BONUSACCOUNTSID"] = IntVal($filter_bonusinner);
$dbResultList1 = Vbchbbonus\DoubleTable::getList(array(
	'filter'=>$arFilter,
	'order'=>array($by=>$order),
));
$dbResultList = new CAdminResult($dbResultList1, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("VBCHBB_SAA_DOUBLE_NAV")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
	array("id"=>"TIMESTAMP","content"=>Loc::getMessage("VBCHBB_BONUS_DOUBLE_TIMESTAMP"),"sort"=>"TIMESTAMP_X","default"=>true),
	array("id"=>"USER_ID","content"=>Loc::getMessage("VBCHBB_BONUS_DOUBLE_USER_ID"),"sort"=>"USER_ID","default"=>true),
	array("id"=>"DEBIT","content"=>Loc::getMessage("VBCHBB_BONUS_DOUBLE_DEBIT"),"sort"=>"DEBIT","default"=>true),
	array("id"=>"CREDIT","content"=>Loc::getMessage("VBCHBB_BONUS_DOUBLE_CREDIT"),"sort"=>"CREDIT","default"=>true),
	array("id"=>"BONUSACCOUNTSID","content"=>Loc::getMessage("VBCHBB_BONUSINNER_NAME"),"sort"=>"BONUSACCOUNTSID","default"=>true),
));
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');

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
while ($arAccount = $dbResultList->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arAccount);
	if($f_USER_ID!=-1)
		$usr=$BBCORE->GetUserInfo($f_USER_ID);
	$row->AddField("ID", $f_ID);
	$row->AddField("TIMESTAMP", $f_TIMESTAMP_X);
	$debit=$BBCORE->ReturnCurrency($arAccount["DEBIT"]);
	$credit=$BBCORE->ReturnCurrency($arAccount["CREDIT"]);
	if($f_USER_ID!=-1){
		$fieldValue = "[<a href=\"/bitrix/admin/user_edit.php?ID=".$f_USER_ID."&lang=".LANG."\" title=\"".Loc::getMessage("VBCHBB_SAA_USER_INFO")."\">".$f_USER_ID."</a>] ";
		$fieldValue .= htmlspecialcharsEx($usr['FIO']);
		$fieldValue .= "<br/><a href=\"mailto:".htmlspecialcharsEx($usr["EMAIL"])."\" title=\"".Loc::getMessage("VBCHBB_SAA_MAILTO")."\">".htmlspecialcharsEx($usr["EMAIL"])."</a>";
	}else{
		$fieldValue = Loc::getMessage('VBCHBB_BONUS_DOUBLE_ORG');
	}
	$row->AddField("USER_ID", $fieldValue);
	$row->AddField("DEBIT", "<span style='color:#ff0814;text-align:center'>" .$debit."</span>");
	$row->AddField("CREDIT", "<span style='color:#1449ff;text-align:center'>" .$credit."</span>");
	$row->AddField("BONUSACCOUNTSID", $BNI[$f_BONUSACCOUNTSID]);
	unset($b);
}
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_SAA_DOUBLE_NAV"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
	<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
		<?
		$oFilter = new CAdminFilter(
			$sTableID."_filter",
			array(
				Loc::getMessage("VBCHBB_SAA_USER_ID"),
				Loc::getMessage("VBCHBB_BONUSINNER_NAME"),
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
			<td><?echo Loc::getMessage("VBCHBB_BONUSINNER_NAME")?>:</td>
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