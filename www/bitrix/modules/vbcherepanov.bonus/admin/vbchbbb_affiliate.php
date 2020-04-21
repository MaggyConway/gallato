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
        $dbResultList=Vbchbbonus\CVbchAffiliateTable::getList(array(
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
                    if(!Vbchbbonus\CVbchAffiliateTable::delete($ID))
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
                if(!Vbchbbonus\CVbchAffiliateTable::delete($i))
                {
                    Application::getConnection()->rollbackTransaction();
                    $lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
                }
                Application::getConnection()->commitTransaction();
                break;
            case "active":
                @set_time_limit(0);
                Application::getConnection()->startTransaction();
                $trns=Vbchbbonus\CVbchAffiliateTable::getById($i)->fetch();
                $trns['ACTIVE']='Y';
                unset($trns['ID']);
                if(!Vbchbbonus\CVbchAffiliateTable::update($i,$trns))
                {
                    Application::getConnection()->rollbackTransaction();
                }
                Application::getConnection()->commitTransaction();
                unset($trns);
                break;
        }
    }
}
$dbResultList1 = Vbchbbonus\CVbchAffiliateTable::getList(array(
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
    array("id"=>"NAME","content"=>Loc::getMessage("VBCHBB_AFF_EDIT_NAME"),"sort"=>"NAME","default"=>true),
    array("id"=>"BONUS","content"=>Loc::getMessage("VBCHBB_BONUS_BONUS"),"sort"=>"BONUS","default"=>true),
    array("id"=>"USERID","content"=>Loc::getMessage("VBCHBB_BONUS_USERID"),"sort"=>"USERID","default"=>true),
    array("id"=>"ACTIVE_FROM","content"=>Loc::getMessage("VBCHBB_AFF_ACTIVE_FROM"),"sort"=>"ACTIVE_FROM","default"=>true),
    array("id"=>"ACTIVE_TO","content"=>Loc::getMessage("VBCHBB_AFF_ACTIVE_TO"),"sort"=>"ACTIVE_TO","default"=>true),
    array("id"=>"PROMOCODE","content"=>Loc::getMessage("VBCHBB_AFF_PROMOCODE"),"sort"=>"PROMOCODE","default"=>true),
    array("id"=>"DOMAINE","content"=>Loc::getMessage("VBCHBB_AFF_DOMAINE"),"sort"=>"DOMAINE","default"=>true),
    array("id"=>"COMMISIA","content"=>Loc::getMessage("VBCHBB_AFF_COMMISIA"),"sort"=>"COMMISIA","default"=>true),
    array("id"=>"COMMISIAPROMO","content"=>Loc::getMessage("VBCHBB_AFF_COMMISIAPROMO"),"sort"=>"COMMISIAPROMO","default"=>true),
));
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');


while ($arAccount = $dbResultList->NavNext(true, "f_"))
{
    $row =& $lAdmin->AddRow($f_ID, $arAccount);

    $usr=$BBCORE->GetUserInfo($f_USERID);
    $row->AddField("ID", "<a href=\"/bitrix/admin/vbchbb_affiliate_edit.php?ID=".$f_ID."&lang=".LANG."\">".$f_ID."</a>");
    $row->AddField("TIMESTAMP", $f_TIMESTAMP_X);
    $row->AddField("LID", $f_LID);
    $row->AddField("ACTIVE", $f_ACTIVE=="Y" ? Loc::getMessage("VBCH_BONUS_YES") : Loc::getMessage("VBCH_BONUS_NO"));
    $bns=$BBCORE->ReturnCurrency($arAccount["BONUS"]);
    $bnspay=$BBCORE->ReturnCurrency($f_PARTPAY);
    $row->AddField("NAME", $f_NAME);
    $row->AddField("BONUS", $bns);
      $fieldValue = "[<a href=\"/bitrix/admin/user_edit.php?ID=".$f_USERID."&lang=".LANG."\" title=\"".Loc::getMessage("VBCHBB_SAA_USER_INFO")."\">".$f_USERID."</a>] ";
    $fieldValue .= htmlspecialcharsEx($usr['FIO']);
    $fieldValue .= "<br/><a href=\"mailto:".htmlspecialcharsEx($usr["EMAIL"])."\" title=\"".Loc::getMessage("VBCHBB_SAA_MAILTO")."\">".htmlspecialcharsEx($usr["EMAIL"])."</a>";
    $row->AddField("USERID", $fieldValue);
    $row->AddField("ACTIVE_FROM", $f_ACTIVE_FROM);
    $row->AddField("ACTIVE_TO", $f_ACTIVE_TO);
    $row->AddField("PROMOCODE", $f_PROMOCODE);
    $row->AddField("DOMAINE", $f_DOMAINE);
    $row->AddField("COMMISIA", $f_COMMISIA);
    $row->AddField("COMMISIAPROMO", $f_COMMISIAPROMO);

    unset($b);
    $arActions = Array();
    if($bonusModulePermissions=="W") {
        $arActions[] = array("SEPARATOR" => true);
        $arActions[] = array("ICON" => "delete", "TEXT" => Loc::getMessage("bonus_aff_act_del"), "ACTION" => "if(confirm('" . Loc::getMessage('bonus_aff_act_del_conf') . "')) " . $lAdmin->ActionDoGroup($f_ID, "delete"));
        if($f_ACTIVE=="N")
            $arActions[] = array("ICON" => "active", "TEXT" => Loc::getMessage("bonus_aff_act_active"), "ACTION" =>  $lAdmin->ActionDoGroup($f_ID, "active"));
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
$aContext = array(
    array(
        "TEXT"=>Loc::getMessage("VBCH_AFF_ADD_C"),
        "LINK"=>"vbchbb_affiliate_edit.php?lang=".LANG,
        "TITLE"=>Loc::getMessage("VBCH_AFF_ADD_C"),
        "ICON"=>"btn_new",
    ),
);
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>