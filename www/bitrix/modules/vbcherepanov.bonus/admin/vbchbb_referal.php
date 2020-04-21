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
$sTableID = "tbl_vbchbb_referer";
$oSort = new CAdminSorting($sTableID, $by,$order);
$lAdmin = new CAdminList($sTableID, $oSort);
$lAdmin->bMultipart = true;
$arFilterFields = Array(
    "find_userid",
    "find_reffrom",
    "find_site",
    "find_active",
);
$by=strtoupper($by);
if($by=='' || $by=='id') $by='ID';
if($order=='') $order='asc';
$lAdmin->InitFilter($arFilterFields);
$arFilter=array();
if($find_active)
    $arFilter['ACTIVE']=$find_active;
if($find_site)
    $arFilter['LID']=$find_site;
if($find_userid)
    $arFilter['USERID']=$find_userid;
if($find_reffrom)
    $arFilter['REFFROM']=$find_reffrom;
if($lAdmin->EditAction()){
}
if(($arID = $lAdmin->GroupAction())){
    if($_REQUEST['action_target']=='selected')
    {
        $arID = Array();
        $rsData=Vbchbbonus\CVbchRefTable::getList(
            array(
                'filter'=>$arFilter,
                'order'=>array($by=>$order),
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
                if(!Vbchbbonus\CVbchRefTable::delete($ID))
                    $lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
                break;
            case "activate":
                Vbchbbonus\CVbchRefTable::update($ID, array("ACTIVE" => "Y"));
                break;
            case "deactivate":
                Vbchbbonus\CVbchRefTable::update($ID, array("ACTIVE" => "N"));
                break;
        }
    }
}
$arHeader = array();
$arHeader[] = array(
    "id" => "ID",
    "content" => "ID",
    "sort" => "ID",
);
$arHeader[] = array(
    "id" => "ACTIVE",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_ACTIVE"),
    "sort" => "ACTIVE",
);
$arHeader[] = array(
    "id" => "LID",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_LID"),
    "sort" => "LID",
);
$arHeader[] = array(
    "id" => "REFROM",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_REFFROM"),
    "sort" => "REFFROM",
);
$arHeader[] = array(
    "id" => "USERID",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_USERID"),
    "sort" => "USERID",
);
$arHeader[] = array(
    "id" => "REFERER",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_REFERER"),
    "sort" => "REFERER",
);
$arHeader[] = array(
    "id" => "ADDRECORDTYPE",
    "content" => Loc::getMessage("VBCHBONUS_PROFILE_ADDRECORDTYPE"),
    "sort" => "ADDRECORDTYPE",
);
$lAdmin->AddHeaders($arHeader);
$lAdmin->AddVisibleHeaderColumn('ID');
$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();

$nav = new \Bitrix\Main\UI\AdminPageNavigation("nav-culture");
$rsData=Vbchbbonus\CVbchRefTable::getList(
    array(
        'filter'=>$arFilter,
        'select'=>array("*",'FROMUSER','USER_ID'),
        'order'=>array($by=>$order),
        'count_total' => true,
        'offset' => $nav->getOffset(),
        'limit' => $nav->getLimit(),
    )
);

//$rsData = new CAdminResult($rsData, $sTableID);
//$rsData->NavStart();
//$lAdmin->NavText($rsData->GetNavPrint(Loc::getMessage("VBCHBB_PROFILE_PAGIN")));
$nav->setRecordCount($rsData->getCount());

$lAdmin->setNavigation($nav, Loc::getMessage("VBCHBB_PROFILE_PAGIN"));


$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$SS=$BBCORE->GetSiteList();
$SS=$SS['S'];
foreach($SS as $SL){
    $site[$SL['LID']]="[".$SL['LID']."]".$SL['NAME'];
}
unset($SS);
while($arRes = $rsData->fetch())
{
    $row =& $lAdmin->AddRow($arRes['ID'], $arRes, $arRes['ID'], "");
    $row->AddField("ID", '<a href="/bitrix/admin/vbchbb_referal_edit.php?ID='. $arRes['ID'].'">'.$arRes['ID'].'</a>');
    $row->AddField("ACTIVE", Loc::getMessage("VBCHBONUS_PROFILE_ACTIVE_".$arRes['ACTIVE']));
    $row->AddField("LID", $site[$arRes['LID']]);
    if($arRes['REFFROM']!=0) {
        $fromuser = "[<a href=\"/bitrix/admin/user_edit.php?ID=" . $arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_FROMUSER_ID'] . "&lang=" . LANG . "\" title=\"" . Loc::getMessage("VBCHBB_SAA_USER_INFO") . "\">" . $arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_FROMUSER_ID'] . "</a>] ";
        $fromuser .= htmlspecialcharsEx($arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_FROMUSER_LAST_NAME'].' ',$arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_FROMUSER_NAME']);
        $fromuser .= "<br/><a href=\"mailto:" . htmlspecialcharsEx($arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_FROMUSER_EMAIL']) . "\" title=\"" . Loc::getMessage("VBCHBB_SAA_MAILTO") . "\">" . htmlspecialcharsEx($arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_FROMUSER_EMAIL']) . "</a>";
    }else $fromuser='';
    $row->AddField("REFROM", $fromuser);
    if($arRes['USERID']!=0) {
        $user_id = "[<a href=\"/bitrix/admin/user_edit.php?ID=" . $arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_USER_ID_ID'] . "&lang=" . LANG . "\" title=\"" . Loc::getMessage("VBCHBB_SAA_USER_INFO") . "\">" . $arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_USER_ID_ID'] . "</a>] ";
        $user_id .= htmlspecialcharsEx($arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_USER_ID_LAST_NAME'].' ',$arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_USER_ID_NAME']);
        $user_id .= "<br/><a href=\"mailto:" . htmlspecialcharsEx($arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_USER_ID_EMAIL']) . "\" title=\"" . Loc::getMessage("VBCHBB_SAA_MAILTO") . "\">" . htmlspecialcharsEx($arRes['ITROUND_VBCHBBONUS_C_VBCH_REF_USER_ID_EMAIL']) . "</a>";
    }else $user_id='';
    $row->AddField("USERID", $user_id);
    $row->AddField("REFERER", $arRes['REFERER']);
    $row->AddField("ADDRECORDTYPE", Loc::getMessage('VBCHBONUS_PROFILE_ADDRECORDTYPE_'.$arRes['ADDRECORDTYPE']));

    $arActions = array();
    $row->AddActions($arActions);
}
// List footer
$lAdmin->AddFooter(
    array(
        array("title"=>Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->getSelectedRowsCount()),
        array("counter"=>true, "title"=>Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);
// Action bar
if(true) {
    $arActions = array(
        "delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
    );
    $arParams = array();
    $lAdmin->AddGroupActionTable($arActions, $arParams);
}
$aContext = array(
    array(
        "TEXT"=>Loc::getMessage("VBCH_REFERAL_ADD_C"),
        "LINK"=>"vbchbb_referal_edit.php?lang=".LANG,
        "TITLE"=>Loc::getMessage("VBCH_REFERAL_ADD_C"),
        "ICON"=>"btn_new",
    ),
);

$boolBtnNew = false;
$boolBtnNew = true;
$childmenu=array();
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_PROFILE_TITLE"));
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
            <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_REFFROM")?>:</td>
            <td><?echo FindUserID("find_reffrom", $find_reffrom, "", "find_form");?></td>
        </tr>
        <tr>
            <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_USERID")?>:</td>
            <td><?echo FindUserID("find_userid", $find_userid, "", "find_form");?></td>
        </tr>
        <tr>
            <td><?echo Loc::getMessage("VBCHBONUS_PROFILE_LID");?>:</td>
            <td>
                <select name="find_lang">
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