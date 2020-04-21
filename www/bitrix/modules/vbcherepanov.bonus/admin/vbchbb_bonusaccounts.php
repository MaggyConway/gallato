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
$sTableID = "tbl_bonusaccounts";
$oSort = new CAdminSorting($sTableID, $by,$order);
$lAdmin = new CAdminList($sTableID, $oSort);
$arFilterFields = Array(
    "find_site_id",
    "find_active",
);
$by=strtoupper($by);
if($by=='' || $by=='id') $by='ID';
if($order=='') $order='asc';
$lAdmin->InitFilter($arFilterFields);
$arFilter=array();
if($find_active)
    $arFilter['ACTIVE']=$find_active;
if($find_site_id)
    $arFilter['LID']=$find_site_id;
if(($lAdmin->GroupAction()) && $bonusModulePermissions=="W")
{
    if ($_REQUEST['action_target']=='selected')
    {
        $arID = Array();
        $dbResultList=Vbchbbonus\CVbchBonusaccountsTable::getList(array(
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
                    if(!Vbchbbonus\CVbchBonusaccountsTable::delete($i))
                    {
                        $DB->Rollback();
                        $lAdmin->AddGroupError(Loc::getMessage("bonusaccounts_del_err"), $i);
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
                if(!Vbchbbonus\CVbchBonusaccountsTable::delete($i))
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(Loc::getMessage("bonusaccounts_del_err"), $i);
                }
                $DB->Commit();
                break;
        }
    }
}

$dbResultList1=Vbchbbonus\CVbchBonusaccountsTable::getList(array(
    'filter'=>$arFilter,
    'order'=>array($by=>$order),
));
$dbResultList = new CAdminResult($dbResultList1, $sTableID);
$dbResultList->NavStart();
$lAdmin->NavText($dbResultList->GetNavPrint(Loc::getMessage("VBCHBB_SAA_NAV")));
$OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');
$lAdmin->AddHeaders(array(
    array("id"=>"ID", "content"=>"ID", 	"sort"=>"ID", "default"=>true),
    array("id"=>"ACTIVE", "content"=>Loc::getMessage('VBCH_BONUS_ACCOUNTS_ACTIVE'),	"sort"=>"ACTIVE", "default"=>true),
    array("id"=>"NAME", "content"=>Loc::getMessage("VBCH_BONUS_ACCOUNTS_NAME"),  "sort"=>"NAME", "default"=>true),
    array("id"=>"LID","content"=>Loc::getMessage("VBCH_BONUS_ACCOUNTS_SITE_ID"), "sort"=>"LID", "default"=>true),
    array("id"=>"PAYSYSTEMID", "content"=>Loc::getMessage("VBCH_BONUS_ACCOUNTS_PAYSYSTEMID"),  "sort"=>"PAYSYSTEMID", "default"=>true),
    array("id"=>"SETTINGS", "content"=>Loc::getMessage("VBCH_BONUS_ACCOUNTS_SETTINGS"),  "sort"=>"", "default"=>true),
));
$SS=$BBCORE->GetSiteList();
$SS=$SS['S'];
foreach($SS as $SL){
    $site[$SL['LID']]="[".$SL['LID']."]".$SL['NAME'];
}
unset($SS);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
while ($arAccount = $dbResultList->NavNext(true, "f_"))
{
    $can_edit = true;
    $row =&$lAdmin->AddRow($f_ID, $arAccount);
    $row->AddField("ID", "<a href=\"/bitrix/admin/vbchbb_bonusaccountsedit.php?ID=".$f_ID."&lang=".LANG."\">".$f_ID."</a>");
    $row->AddField("ACTIVE", Loc::getMessage("VBCH_BONUS_ACCOUNTS_ACTIVE_".$f_ACTIVE));
    $row->AddField("NAME","<a href=\"/bitrix/admin/vbchbb_bonusaccountsedit.php?ID=".$f_ID."&lang=".LANG."\">".$f_NAME."</a>");
    $row->AddField("LID", $site[$f_LID]);
    $row->AddField("PAYSYSTEMID", $f_PAYSYSTEMID);
    $value=$BBCORE->CheckSerialize($f_SETTINGS);
    $val='';
    if(array_key_exists("SUFIX",$value) && $value['SUFIX']=='NAME'){
        $val=Loc::getMessage('VBCH_BONUSACCOUNTS_SELFNAME');
    }
    if(array_key_exists("SUFIX",$value) && $value['SUFIX']=='CURRENCY'){
        $val=Loc::getMessage('VBCH_BONUSACCOUNTS_CURRENCY');
    }
    $val.="[".($value['SUFIX']=='NAME' ? implode(";",$value['NAME']) : $value['CURRENCY'])."]";
    $row->AddField("SETTINGS", $val);


    $arActions = Array();
    if($bonusModulePermissions=="W")
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>Loc::getMessage("bonusaccounts_act_edit"),
            "ACTION"=>$lAdmin->ActionRedirect("vbchbb_bonusaccountsedit.php?ID=".$f_ID),
        );
    if($bonusModulePermissions=="W")
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>Loc::getMessage("bonus_act_del"),
            "ACTION"=>"if(confirm('".Loc::getMessage("bonusaccounts_act_del_conf")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
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
        "TEXT"=>Loc::getMessage("VBCH_BONUSACCOUNT_ADD_C"),
        "LINK"=>"vbchbb_bonusaccountsedit.php?lang=".LANG,
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
    <form method="GET" name="find_form" id="find_form" action="<?echo $APPLICATION->GetCurPage()?>">
        <?
        $filterUrl = $APPLICATION->GetCurPageParam();
        $oFilter = new CAdminFilter($sTableID."_filter", $arFindFields, array("table_id" => $sTableID, "url" => $filterUrl));
        ?>
        <script type="text/javascript">
            var arClearHiddenFields = new Array();
            function applyFilter(el)
            {
                BX.adminPanel.showWait(el);
                <?=$sTableID."_filter";?>.OnSet('<?=CUtil::JSEscape($sTableID)?>', '<?echo CUtil::JSEscape($APPLICATION->GetCurPage().'?type='.urlencode($type).'&IBLOCK_ID='.urlencode($IBLOCK_ID).'&lang='.LANGUAGE_ID.'&')?>');
                return false;
            }
            function deleteFilter(el)
            {
                BX.adminPanel.showWait(el);
                if (0 < arClearHiddenFields.length)
                {
                    for (var index = 0; index < arClearHiddenFields.length; index++)
                    {
                        if (undefined != window[arClearHiddenFields[index]])
                        {
                            if ('ClearForm' in window[arClearHiddenFields[index]])
                            {
                                window[arClearHiddenFields[index]].ClearForm();
                            }
                        }
                    }
                }
                <?=$sTableID."_filter"?>.OnClear('<?=CUtil::JSEscape($sTableID)?>', '<?=CUtil::JSEscape($APPLICATION->GetCurPage().'?type='.urlencode($type).'&IBLOCK_ID='.$IBLOCK_ID.'&lang='.LANGUAGE_ID.'&')?>');
                return false;
            }
            try {
                var DecimalSeparator = Number("1.2").toLocaleString().charCodeAt(1);
                document.cookie = '<?echo $dsc_cookie_name?>='+DecimalSeparator+'; path=/;';
            }
            catch (e)
            {
            }
        </script><?
        $oFilter->Begin();
        ?>
        <tr>
            <td><?echo Loc::getMessage("VBCH_BONUS_ACCOUNTS_SITE_ID");?>:</td>
            <td>
                <select name="find_site_id">
                    <option value=""><?echo Loc::getMessage("IBLOCK_ALL")?></option>
                    <?
                    $l = CLang::GetList($b="sort", $o="asc", Array("VISIBLE"=>"Y"));
                    while($ar = $l->GetNext()):
                        ?><option value="<?echo $ar["LID"]?>"<?if($find_lang==$ar["LID"])echo " selected"?>><?echo $ar["NAME"]?></option><?
                    endwhile;
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?echo Loc::getMessage("VBCH_BONUS_ACCOUNTS_ACTIVE")?>:</td>
            <td>
                <select name="find_active">
                    <option value=""><?=htmlspecialcharsex(Loc::getMessage('IBLOCK_VALUE_ANY'))?></option>
                    <option value="Y"<?if($find_active=="Y")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("IBLOCK_YES"))?></option>
                    <option value="N"<?if($find_active=="N")echo " selected"?>><?=htmlspecialcharsex(Loc::getMessage("IBLOCK_NO"))?></option>
                </select>
            </td>
        </tr>
        <?
        $oFilter->Buttons();
        ?><input  class="adm-btn" type="submit" name="set_filter" value="<? echo Loc::getMessage("admin_lib_filter_set_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_set_butt_title"); ?>" onClick="return applyFilter(this);">
        <input  class="adm-btn" type="submit" name="del_filter" value="<? echo Loc::getMessage("admin_lib_filter_clear_butt"); ?>" title="<? echo Loc::getMessage("admin_lib_filter_clear_butt_title"); ?>" onClick="deleteFilter(this); return false;">
        <?
        $oFilter->End();
        ?>
    </form>
<?
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>