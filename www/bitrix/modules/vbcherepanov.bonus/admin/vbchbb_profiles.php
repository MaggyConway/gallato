<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Loader,\Bitrix\Main\Localization\Loc,Bitrix\Main,Bitrix\Currency;
use \ITRound\Vbchbbonus;
Loader::includeModule("vbcherepanov.bonus");

$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$saleModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($saleModulePermissions=="D")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
Loc::loadMessages(__FILE__);
$sTableID = "tbl_vbchbb_profiles";
$oSort = new CAdminSorting($sTableID, "ID","asc");
$lAdmin = new CAdminList($sTableID, $oSort);
$lAdmin->bMultipart = true;
$arFilterFields = Array(
    "find_id_1",		"find_id_2",
    "find_name",
    "find_timestamp_1",	"find_timestamp_2",
    "find_type",
    "fins_site",
    "find_active",
);

$lAdmin->InitFilter($arFilterFields);
$arFilter=array();
if($find_ID1)
    $arFilter['>=ID']=$find_ID1;
if($find_ID2)
    $arFilter['<=ID']=$find_ID2;
if($find_active)
    $arFilter['ACTIVE']=$find_active;
if($find_site)
    $arFilter['SITE']=$find_site;
if($find_type)
    $arFilter['TYPE']=$find_type;
if($find_name)
    $arFilter['TYPE']=$find_name;
if($lAdmin->EditAction()){
}
if(($arID = $lAdmin->GroupAction())){
    if($_REQUEST['action_target']=='selected')
    {
        $arID = Array();
        $rsData=Vbchbbonus\CvbchbonusprofilesTable::getList(
            array(
                'filter'=>$arFilter,
            )
        );
        while($arRes = $rsData->fetch())
            $arID[] = $arRes['ID'];
    }
    foreach ($arID as $ID)
    {
        $ID = intval($ID);
        if($ID<=0)
            continue;
        switch ($_REQUEST['action'])
        {
            case "delete":
                @set_time_limit(0);
                if(!Vbchbbonus\CvbchbonusprofilesTable::delete($ID))
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                break;
            case "activate":
                Vbchbbonus\CvbchbonusprofilesTable::update($ID, array("ACTIVE" => "Y"));
                break;
            case "deactivate":
                Vbchbbonus\CvbchbonusprofilesTable::update($ID, array("ACTIVE" => "N"));
                break;
            case "copy":
                $dat=Vbchbbonus\CvbchbonusprofilesTable::getById($ID)->fetch();
                unset($dat['ID'],$dat['TIMESTAMP_X'],$dat['COUNTS']);
                Vbchbbonus\CvbchbonusprofilesTable::add($dat);
                unset($dat);
                break;
        }
    }
}
CJSCore::Init(array('date'));
$arHeader = array();
$arHeader[] = array(
    "id" => "ID",
    "content" => "ID",
    "sort" => "ID",
);
$arHeader[] = array(
    "id" => "NAME",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_NAME"),
    "sort" => "NAME",
);
$arHeader[] = array(
    "id" => "ACTIVE",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_ACTIVE"),
    "sort" => "ACTIVE",
);
$arHeader[] = array(
    "id" => "TYPE",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_TYPE"),
    "sort" => "TYPE",
);
$arHeader[] = array(
    "id" => "BONUS",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_BONUS"),
    "sort" => "BONUS",
);
$arHeader[] = array(
    "id" => "SITE",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_FILT_SITE"),
    "sort" => "SITE",
);
$arHeader[] = array(
    "id" => "COUNTS",
    "content" => Loc::getMessage("VBCHBB_PROFILE_COUNT"),
    "sort" => "COUNTS",
);
$lAdmin->AddHeaders($arHeader);
$lAdmin->AddVisibleHeaderColumn('ID');
$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();
if($by=='' || $by=='id') $by='ID';

$rsData=Vbchbbonus\CvbchbonusprofilesTable::getList(
    array(
        'filter'=>$arFilter,
		'order'=>array($by=>$order),
    )
);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage("VBCHBB_PROFILE_PAGIN")));
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$SS=$BBCORE->GetSiteList();
$SS=$SS['S'];
foreach($SS as $SL){
    $site[$SL['LID']]="[".$SL['LID']."]".$SL['NAME'];
}
unset($SS);
while($arRes = $rsData->NavNext(true, "f_"))
{
    $row =& $lAdmin->AddRow($f_ID, $arRes, "vbchbb_profiles_edit.php?ID=".$f_ID."&lang=".LANG."&PROFILES_TYPE=".$f_TYPE);
    $row->AddField("ID", "<a href=\"/bitrix/admin/vbchbb_profiles_edit.php?ID=".$f_ID."&lang=".LANG."&PROFILES_TYPE=".$f_TYPE."\">".$f_ID."</a>");
    $row->AddField("NAME", "<a href=\"/bitrix/admin/vbchbb_profiles_edit.php?ID=".$f_ID."&lang=".LANG."&PROFILES_TYPE=".$f_TYPE."\">".$f_NAME."</a>");
    $row->AddField("ACTIVE", Loc::getMessage("VBCHBONUS_PROFILE_ACTIVE_".$f_ACTIVE));
    $cls=$BBCORE->INSTALL_PROFILE[$f_TYPE];
    if(!is_null($cls)){
        $name=call_user_func_array(array($cls,"getDesc"),array());
    }else $name="";
    $row->AddField("TYPE", $name);
    $row->AddField("BONUS", $f_BONUS);
    $row->AddField("SITE", $site[$f_SITE]);
    $row->AddField("COUNTS", $f_COUNTS);
    $arActions = array();

    if($f_ACTIVE == "Y")
    {
        $arActive = array(
            "TEXT" => Loc::getMessage("IBLIST_A_DEACTIVATE"),
            "ACTION" => $lAdmin->ActionDoGroup($f_TYPE.$f_ID, "deactivate", $sThisSectionUrl),
            "ONCLICK" => "",
        );
    }
    else
    {
        $arActive = array(
            "TEXT" => Loc::getMessage("IBLIST_A_ACTIVATE"),
            "ACTION" => $lAdmin->ActionDoGroup($f_TYPE.$f_ID, "activate", $sThisSectionUrl),
            "ONCLICK" => "",
        );
    }
    $row->AddActions($arActions);
}
// List footer
$lAdmin->AddFooter(
    array(
        array("title"=>Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
        array("counter"=>true, "title"=>Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);
// Action bar
if(true) {
    $arActions = array(
        "delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
        "copy" => Loc::getMessage("MAIN_ADMIN_LIST_COPY"),
        "activate" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
        "deactivate" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
     );
    $arParams = array();
    $lAdmin->AddGroupActionTable($arActions, $arParams);
}
$boolBtnNew = false;
$aContext = array();
$boolBtnNew = true;
$childmenu=array();

$childmenu=$BBCORE->GetTypes();
$aContext=array(
    array(
    "TEXT"=>Loc::getMessage("VBCHBB_PROFILE_CREATE"),
    "TITLE"=>Loc::getMessage("VBCHBB_PROFILE_CREATE_DESC"),
    "MENU"=>$childmenu,
    ),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_PROFILE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
CJSCore::Init('file_input');
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
        <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_FROM_TO_ID")?></td>
        <td nowrap>
            <input type="text" name="find_id_1" size="10" value="<?echo htmlspecialcharsex($find_id_1)?>">
            ...
            <input type="text" name="find_id_2" size="10" value="<?echo htmlspecialcharsex($find_id_2)?>">
        </td>
    </tr>
    <tr>
        <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_NAME")?>:</td>
        <td><input type="text" name="find_name" value="<?=$find_name?>"/></td>
    </tr>
    <tr>
        <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_TYPE")?>:</td>
        <td><?=$BBCORE->SelectGetTypes("find_type",$find_type);?></td>
    </tr>
    <tr>
        <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_FILT_SITE");?>:</td>
        <td>
            <select name="find_site">
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
        <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_ACTIVE")?>:</td>
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


