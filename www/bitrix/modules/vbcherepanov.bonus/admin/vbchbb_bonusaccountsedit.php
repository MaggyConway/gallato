<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use ITRound\Vbchbbonus;
Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$bonusModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($saleModulePermissions=="D")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
Loader::includeModule($module_id);
ClearVars();
$errorMessage = "";
$bVarsFromForm = false;
$ID = IntVal($ID);

$CURLIST=$BBCORE->ModuleCurrency();

if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $bonusModulePermissions>="U" && check_bitrix_sessid())
{
    $arFields=array(
        'ACTIVE'=>$ACTIVE,
        'NAME'=>trim($NAME),
        'LID'=>$LID,
        'SETTINGS'=>base64_encode(serialize($SETTINGS))
    );
    if ($ID <= 0)
    {
        Vbchbbonus\CVbchBonusaccountsTable::add($arFields);
    }
    else
    {
        if (strlen($errorMessage) <= 0)
        {

            $l=Vbchbbonus\CVbchBonusaccountsTable::update($ID,$arFields);

        }
    }

    if (strlen($errorMessage) <= 0)
    {
        if (strlen($apply) <= 0)
            LocalRedirect("/bitrix/admin/vbchbb_bonusaccounts.php?lang=".LANG.GetFilterParams("filter_", false));
    }
    else
    {
        $bVarsFromForm = true;
    }
}
if ($ID > 0)
    $APPLICATION->SetTitle(Loc::getMessage("VBCHBB_BONUSACCOUNT_EDIT_UPDATING"));
else
    $APPLICATION->SetTitle(Loc::getMessage("VBCHBB_BONUSACCOUNT_EDIT_ADDING"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$dbAccount = Vbchbbonus\CVbchBonusaccountsTable::getList(array('filter'=>array('ID'=>$ID)))->fetch();
if (!$dbAccount)
{
    $ID = 0;
}
if ($bVarsFromForm)
    $DB->InitTableVarsForEdit("vbch_bonusaccounts", "", "str_");

$aMenu = array(
    array(
        "TEXT" => Loc::getMessage("VBCHBB_BONUSACCOUNT_EDITN_2FLIST"),
        "LINK" => "/bitrix/admin/vbchbb_bonusaccounts.php?lang=".LANG.GetFilterParams("filter_"),
        "ICON"	=> "btn_list",
        "TITLE" => Loc::getMessage("VBCHBB_BONUSACCOUNT_EDITN_2FLIST"),
    )
);

if ($ID > 0 && $saleModulePermissions >= "U")
{
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_NEW_ACCOUNT"),
        "LINK" => "/bitrix/admin/vbchbb_bonusaccountsedit.php?lang=".LANG.GetFilterParams("filter_"),
        "ICON"	=> "btn_new",
        "TITLE" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_NEW_ACCOUNT_TITLE"),
    );

    if ($saleModulePermissions >= "W")
    {
        $aMenu[] = array(
            "TEXT" => Loc::getMessage("VBCHBB_BONUSACCOUNT_EDITN_DELETE_ACCOUNT"),
            "LINK" => "javascript:if(confirm('".Loc::getMessage("VBCHBB_BONUSACCOUNT_EDITN_DELETE_ACCOUNT_CONFIRM")."')) window.location='/bitrix/admin/vbchbb_bonusaccounts.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
            "WARNING" => "Y",
            "ICON"	=> "btn_delete"
        );
    }
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if(strlen($errorMessage)>0)
    echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>Loc::getMessage("VBCHBB_BONUSACCOUNT_EDIT_ERROR"), "HTML"=>true));?>


    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
        <?echo GetFilterHiddens("filter_");?>
        <input type="hidden" name="Update" value="Y">
        <input type="hidden" name="lang" value="<?echo LANG ?>">
        <input type="hidden" name="ID" value="<?echo $ID ?>">
        <?=bitrix_sessid_post()?>

        <?
        if($ID>0){
            $title=$dbAccount['NAME'];
        }else{
            $title=Loc::getMessage('VBCHBB_BONUSACCOUNT_EDIT_ADDING');
        }
        $aTabs = array(
            array("DIV" => "edit1", "TAB" =>$title, "ICON" => "sale", "TITLE" => Loc::getMessage("VBCHBB_BONUSACCOUNT_EDITN_TAB_ACCOUNT_DESCR"))
        );

        $tabControl = new CAdminTabControl("tabControl", $aTabs);
        $tabControl->Begin();
        ?>

        <?
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td valign="top"><?echo Loc::getMessage("VBCHBB_ACCOUNT_EDIT_ACTIVE")?></td>
            <td valign="top">
                <input type="hidden" name="ACTIVE" value="N">
                <input type="checkbox" id="ACTIVE" name="ACTIVE" value="Y" <?if($dbAccount['ACTIVE']=="Y")echo " checked"?>>
            </td>
        </tr>
        <?if ($ID > 0):?>
            <tr>
                <td width="40%">ID:</td>
                <td width="60%"><?=$ID?></td>
            </tr>
            <tr>
                <td><?echo Loc::getMessage("VBCHBB_BONUSACCOUNT_EDIT_TIMESTAMP")?></td>
                <td><?=$dbAccount['TIMESTAMP_X']?></td>
            </tr>
        <?endif;?>
        <tr class="adm-detail-required-field">
            <td width="40%"><?echo Loc::getMessage("VBCHBB_BONUSACCOUNT_EDIT_NAME")?></td>
            <td width="60%">
                <input type="text" name="NAME" size="50" maxlength="50" value="<?= $dbAccount['NAME'] ?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_BONUSACCOUNT_EDIT_SITE")?></td>
            <td>
                <?=CLang::SelectBox("LID", $dbAccount['LID']);?>
            </td>
        </tr>
        <tr>
            <td valign="top"><?echo Loc::getMessage("VBCHBB_ACCOUNT_EDIT_SETTINGS")?></td>
            <td valign="top">
                <?
                $OPTION['BONUSNAME']=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSNAME');
                $value=$BBCORE->CheckSerialize($dbAccount['SETTINGS']);
                    $arrBonusName['REFERENCE']=array(Loc::getMessage("VBCHBONUS_NAME_CURRENCY"),Loc::getMessage("VBCHBONUS_NAME_NAME"));
                    $arrBonusName['REFERENCE_ID']=array("CURRENCY","NAME");
                    $html.=SelectBoxFromArray("SETTINGS[SUFIX]", $arrBonusName,$value['SUFIX'], Loc::getMessage("VBCHBONUS_NAME_DEFAULT"), "onchange='SelectName(this.options[selectedIndex].value,\"".$ss."\");'");
                if($ID<=0){
                    $value=array(
                        'SUFIX'=>$OPTION['BONUSNAME']['OPTION']['SUFIX'],
                        'CURRENCY'=>$OPTION['BONUSNAME']['OPTION']['CURRENCY'],
                        'NAME'=>array(
                                1=>$OPTION['BONUSNAME']['OPTION']['NAME'][1],
                                2=>$OPTION['BONUSNAME']['OPTION']['NAME'][2],
                                3=>$OPTION['BONUSNAME']['OPTION']['NAME'][3],
                        )
                    );
                }
                $html.="&nbsp;";
                $html.='<div id="CURRENCY'.$ss.'" style="display:'.($value['SUFIX']=="CURRENCY" ? 'block' :'none').'" >';
                $html.=\CCurrency::SelectBox("SETTINGS[CURRENCY]",$value["CURRENCY"],"",true).'</div>';
                $html.='<div id="NAME'.$ss.'" style="display:'.($value['SUFIX']=="NAME" ? 'block' :'none').'" >';
                $html.='<input type="text" size="20" name="SETTINGS[NAME][1]" value="'.$value["NAME"][1].'" placeholder="'.Loc::getMessage("VBCHBONUS_A").'"/><br/>';
                $html.='<input type="text" size="20" name="SETTINGS[NAME][2]" value="'.$value["NAME"][2].'" placeholder="'.Loc::getMessage("VBCHBONUS_B").'"/><br/>';
                $html.='<input type="text" size="20" name="SETTINGS[NAME][3]" value="'.$value["NAME"][3].'" placeholder="'.Loc::getMessage("VBCHBONUS_C").'"/><br/>';
                echo $html;
                ?>
            </td>
        </tr>
        <?
        $tabControl->EndTab();
        ?>
        <?
        $tabControl->Buttons(
            array(
                "back_url" => "/bitrix/admin/vbchbb_bonusaccounts.php?lang=".LANG.GetFilterParams("filter_")
            )
        );
        ?>
        <?
        $tabControl->End();
        ?>
    </form>
    <script  type="text/javascript">
        SelectName=function(id, site_id){
            if(id==''){
                BX.style(BX("CURRENCY"+site_id),'display','none');
                BX.style(BX("NAME"+site_id),'display','none');
            }
            else if(id == 'CURRENCY'){
                BX.style(BX("CURRENCY"+site_id),'display','table-row');
                BX.style(BX("NAME"+site_id),'display','none');
            }else{
                BX.style(BX("CURRENCY"+site_id),'display','none');
                BX.style(BX("NAME"+site_id),'display','table-row');
            }
        };
    </script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>