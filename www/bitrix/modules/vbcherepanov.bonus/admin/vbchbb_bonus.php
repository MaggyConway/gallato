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
Loader::includeModule("sale");
Loc::loadMessages(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
$sTableID = "tbl_vbchbonus";
$oSort = new CAdminSorting($sTableID, $by, $order);
$lAdmin = new CAdminList($sTableID, $oSort);
$lAdmin->bMultipart = true;
$arFilterFields = array(
	"filter_user_id",
	"filter_bonusinner",
);
if($by=='' || $by=='id') $by='ID';
if($order=='') $order='asc';
$lAdmin->InitFilter($arFilterFields);
$arFilter = array();
if (IntVal($filter_user_id) > 0) $arFilter["USERID"] = IntVal($filter_user_id);
if (IntVal($filter_bonusinner) > 0) $arFilter["BONUSACCOUNTSID"] = IntVal($filter_bonusinner);
if($lAdmin->EditAction()){
}
if(($lAdmin->GroupAction()) && $bonusModulePermissions=="W")
{
	if ($_REQUEST['action_target']=='selected')
	{
		$arID = Array();
		$dbResultList=Vbchbbonus\BonusTable::getList(array(
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
					if(!Vbchbbonus\BonusTable::delete($ID))
					{
						Application::getConnection()->rollbackTransaction();
						$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $ID);
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
				if(!Vbchbbonus\BonusTable::delete($i))
				{
					Application::getConnection()->rollbackTransaction();
					$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
				}
				Application::getConnection()->commitTransaction();
				break;
            case "activate":
                @set_time_limit(0);
                Application::getConnection()->startTransaction();
                $trns=Vbchbbonus\BonusTable::getById($i)->fetch();
                if($trns['ACTIVE']=='Y') continue;
                $trns['ACTIVE']='Y';
                $trns['ACTIVE_FROM']='';
                unset($trns['ID']);
                if(!Vbchbbonus\BonusTable::update($i,$trns))
                {
                    Application::getConnection()->rollbackTransaction();
                }
                Application::getConnection()->commitTransaction();

                Application::getConnection()->startTransaction();
                if($account=\ITRound\Vbchbbonus\AccountTable::getList(array(
                    'filter'=>array('USER_ID'=>$trns['USERID'],'BONUSACCOUNTSID'=>$trns['BONUSACCOUNTSID'])
                ))->fetch())
                {
                    $summa=floatval($account['CURRENT_BUDGET'])+floatval($trns['BONUS']);
                    if(!\ITRound\Vbchbbonus\AccountTable::update($account['ID'],array('CURRENT_BUDGET'=>$summa)))
                        Application::getConnection()->rollbackTransaction();
                }else{
                    if(!\ITRound\Vbchbbonus\AccountTable::add(
                        [
                            'BONUSACCOUNTSID'=>$trns['BONUSACCOUNTSID'],
                            'CURRENT_BUDGET'=>$summa,
                            'CURRENCY'=>'RUB',
                            'USER_ID'=>$trns['USERID'],
                            'TIMESTAMP_X'=>new \Bitrix\Main\Type\DateTime(),
                        ]
                    ))
                        Application::getConnection()->rollbackTransaction();
                }
                Application::getConnection()->commitTransaction();
                unset($trns);
                break;
		}
	}
}
$dbResultList1 = Vbchbbonus\BonusTable::getList(array(
	'filter'=>$arFilter,
	'order'=>array($by=>$order),
));
$dbResultList = new CAdminResult($dbResultList1, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("VBCHBB_SAA_NAV")));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
	array("id"=>"TIMESTAMP","content"=>Loc::getMessage("VBCHBB_BONUS_TIMESTAMP"),"sort"=>"TIMESTAMP_X","default"=>true),
	array("id"=>"LID","content"=>Loc::getMessage("VBCHBB_BONUS_LID"),"sort"=>"LID","default"=>true),
	array("id"=>"ACTIVE","content"=>Loc::getMessage("VBCHBB_BONUS_ACTIVE"),"sort"=>"ACTIVE","default"=>true),
	array("id"=>"BONUS","content"=>Loc::getMessage("VBCHBB_BONUS_BONUS"),"sort"=>"BONUS","default"=>true),
	array("id"=>"USERID","content"=>Loc::getMessage("VBCHBB_BONUS_USERID"),"sort"=>"USERID","default"=>true),
	array("id"=>"ACTIVE_FROM","content"=>Loc::getMessage("VBCHBB_BONUS_ACTIVE_FROM"),"sort"=>"ACTIVE_FROM","default"=>true),
	array("id"=>"ACTIVE_TO","content"=>Loc::getMessage("VBCHBB_BONUS_ACTIVE_TO"),"sort"=>"ACTIVE_TO","default"=>true),
	array("id"=>"DESCRIPTION","content"=>Loc::getMessage("VBCHBB_BONUS_DESCRIPTION"),"sort"=>"DESCRIPTION","default"=>true),
	array("id"=>"TYPES","content"=>Loc::getMessage("VBCHBB_BONUS_TYPE"),"sort"=>"TYPES","default"=>true),
	array("id"=>"OPTIONS","content"=>Loc::getMessage("VBCHBB_BONUS_OPTIONS"),"sort"=>"options","default"=>true),
	array("id"=>"SORT","content"=>Loc::getMessage("VBCHBB_BONUS_SORT"),"sort"=>"SORT","default"=>true),
	array("id"=>"PARTPAY","content"=>Loc::getMessage("VBCHBB_BONUS_PARTPAY"),"sort"=>"PARTPAY","default"=>true),
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

	$usr=$BBCORE->GetUserInfo($f_USERID);
	$row->AddField("ID", $f_ID);
	$row->AddField("TIMESTAMP", $f_TIMESTAMP_X);
	$row->AddField("LID", $f_LID);
	$row->AddField("ACTIVE", $f_ACTIVE=="Y" ? Loc::getMessage("VBCH_BONUS_YES") : Loc::getMessage("VBCH_BONUS_NO"));
	$bns=$BBCORE->ReturnCurrency($arAccount["BONUS"]);
	$bnspay=$BBCORE->ReturnCurrency($f_PARTPAY);
	$row->AddField("BONUS", $bns);
	$row->AddField("PARTPAY", "<small style='color:red;text-align:center'>".$bnspay."</small>");
	$fieldValue = "[<a href=\"/bitrix/admin/user_edit.php?ID=".$f_USERID."&lang=".LANG."\" title=\"".Loc::getMessage("VBCHBB_SAA_USER_INFO")."\">".$f_USERID."</a>] ";
	$fieldValue .= htmlspecialcharsEx($usr['FIO']);
	$fieldValue .= "<br/><a href=\"mailto:".htmlspecialcharsEx($usr["EMAIL"])."\" title=\"".Loc::getMessage("VBCHBB_SAA_MAILTO")."\">".htmlspecialcharsEx($usr["EMAIL"])."</a>";
	$row->AddField("USERID", $fieldValue);
	$row->AddField("ACTIVE_FROM", $f_ACTIVE_FROM);
	$row->AddField("ACTIVE_TO", $f_ACTIVE_TO);
	$row->AddField("DESCRIPTION", $f_DESCRIPTION);
	$row->AddField("TYPES", $f_TYPES);
	$row->AddField("SORT", $f_SORT);
	$str="";
	$l=$BBCORE->CheckSerialize($f_OPTIONS);
	if(is_array($l) && sizeof($l)>0)
		foreach($l as $key=>$val)
			$str.=$key.":".$val."<br/>";
	if($str=="") $str=$l;
	$row->AddField("OPTIONS", $str);
	$row->AddField("BONUSACCOUNTSID", $BNI[$f_BONUSACCOUNTSID]);
	unset($b);
	$arActions = Array();
	if($bonusModulePermissions=="W") {
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON" => "delete", "TEXT" => Loc::getMessage("bonus_act_del"), "ACTION" => "if(confirm('" . Loc::getMessage('bonus_act_del_conf') . "')) " . $lAdmin->ActionDoGroup($f_ID, "delete"));
		if($f_ACTIVE=="N")
			$arActions[] = array("ICON" => "active", "TEXT" => Loc::getMessage("bonus_act_active"), "ACTION" =>  $lAdmin->ActionDoGroup($f_ID, "active"));
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
// Action bar
if(true) {
    $arActions = array(
        "delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
        "activate" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
    );
    $arParams = array();
    $lAdmin->AddGroupActionTable($arActions, $arParams);
}
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(Loc::getMessage("SAA_TITLE"));
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