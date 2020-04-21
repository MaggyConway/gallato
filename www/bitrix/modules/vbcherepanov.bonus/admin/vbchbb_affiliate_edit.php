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
        'LID'=>$LID,
        'NAME'=>trim($NAME),
        'ACTIVE'=>$ACTIVE,
        "BONUS"=>$BONUS,
        "USERID"=>intval($USERID),
        "ACTIVE_FROM"=>$ACTIVE_FROM,
        "ACTIVE_TO"=>$ACTIVE_TO,
        "PROMOCODE"=>$PROMOCODE,
        "DOMAINE"=>$DOMAINE,
        "URL"=>$URL,
        "COMMISIA"=>$COMMISIA,
        "COMMISIAPROMO"=>$COMMISIAPROMO,
    );


    if($arFields['TIMESTAMP_X']=='')
        $arFields['TIMESTAMP_X']=new \Bitrix\Main\Type\DateTime();
    if($arFields['ACTIVE_TO'])
        $arFields['ACTIVE_TO']=new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_TO']);
    if($arFields['ACTIVE_FROM'])
        $arFields['ACTIVE_FROM']=new \Bitrix\Main\Type\DateTime($arFields['ACTIVE_FROM']);

    if ($ID <= 0)
    {
        Vbchbbonus\CVbchAffiliateTable::add($arFields);
    }
    else
    {
        if (strlen($errorMessage) <= 0)
        {

            $l=Vbchbbonus\CVbchAffiliateTable::update($ID,$arFields);

        }
    }

    if (strlen($errorMessage) <= 0)
    {
        if (strlen($apply) <= 0)
            LocalRedirect("/bitrix/admin/vbchbb_affiliate.php?lang=".LANG.GetFilterParams("filter_", false));
    }
    else
    {
        $bVarsFromForm = true;
    }
}
if ($ID > 0)
    $APPLICATION->SetTitle(Loc::getMessage("VBCHBB_AFF_EDIT_UPDATING"));
else
    $APPLICATION->SetTitle(Loc::getMessage("VBCHBB_AFF_EDIT_ADDING"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$dbAccount = Vbchbbonus\CVbchAffiliateTable::getList(array('filter'=>array('ID'=>$ID)))->fetch();
if (!$dbAccount)
{
    $ID = 0;
}
if ($bVarsFromForm)
    $DB->InitTableVarsForEdit("vbch_bonus_affiliate", "", "str_");

$aMenu = array(
    array(
        "TEXT" => Loc::getMessage("VBCHBB_AFF_EDITN_2FLIST"),
        "LINK" => "/bitrix/admin/vbchbb_affiliate.php?lang=".LANG.GetFilterParams("filter_"),
        "ICON"	=> "btn_list",
        "TITLE" => Loc::getMessage("VBCHBB_AFF_EDITN_2FLIST"),
    )
);

if ($ID > 0 && $saleModulePermissions >= "U")
{
    $aMenu[] = array("SEPARATOR" => "Y");

    $aMenu[] = array(
        "TEXT" => Loc::getMessage("VBCHBB_AFF_EDITN_NEW_ACCOUNT"),
        "LINK" => "/bitrix/admin/vbchb_affiliate_edit.php?lang=".LANG.GetFilterParams("filter_"),
        "ICON"	=> "btn_new",
        "TITLE" => Loc::getMessage("VBCHBB_AFF_EDITN_NEW_ACCOUNT"),
    );

    if ($saleModulePermissions >= "W")
    {
        $aMenu[] = array(
            "TEXT" => Loc::getMessage("VBCHBB_AFF_EDITN_DELETE_ACCOUNT"),
            "LINK" => "javascript:if(confirm('".Loc::getMessage("VBCHBB_AFF_EDITN_DELETE_ACCOUNT_CONFIRM")."')) window.location='/bitrix/admin/vbchbb_affiliate.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
            "WARNING" => "Y",
            "ICON"	=> "btn_delete"
        );
    }
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if(strlen($errorMessage)>0)
    echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>Loc::getMessage("VBCHBB_AFF_EDIT_ERROR"), "HTML"=>true));?>


    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
        <?echo GetFilterHiddens("filter_");?>
        <input type="hidden" name="Update" value="Y">
        <input type="hidden" name="lang" value="<?echo LANG ?>">
        <input type="hidden" name="ID" value="<?echo $ID ?>">
        <?=bitrix_sessid_post()?>

        <?
        if($ID>0){
            $title=$arFields['NAME'];
        }else{
            $title=Loc::getMessage('VBCHBB_AFF_EDIT_UPDATING');
        }

        $aTabs = array(
            array("DIV" => "edit1", "TAB" =>$title, "ICON" => "sale", "TITLE" => Loc::getMessage("VBCHBB_AFF_EDITN_TAB_ACCOUNT_DESCR"))
        );

        $tabControl = new CAdminTabControl("tabControl", $aTabs);
        $tabControl->Begin();
        ?>

        <?
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td valign="top"><?echo Loc::getMessage("VBCHBB_AFF_EDIT_ACTIVE")?></td>
            <td valign="top">
                <input type="hidden" name="ACTIVE" value="N">
                <input type="checkbox" id="ACTIVE" name="ACTIVE" value="Y" <?if($dbAccount['ACTIVE']=="Y")echo " checked"?>>
            </td>
        </tr>
        <tr>
            <td valign="top"><?echo Loc::getMessage("VBCHBB_AFF_EDIT_ACTIVE_FROM")?></td>
            <td valign="top">
                <? echo \CAdminCalendar::CalendarDate('ACTIVE_FROM', $dbAccount['ACTIVE_FROM'], 19, true);?>

            </td>
        </tr>
        <tr>
            <td valign="top"><?echo Loc::getMessage("VBCHBB_AFF_EDIT_ACTIVE_TO")?></td>
            <td valign="top">
                <? echo \CAdminCalendar::CalendarDate('ACTIVE_TO', $dbAccount['ACTIVE_TO'], 19, true);?>

            </td>
        </tr>
        <?if ($ID > 0):?>
            <tr>
                <td width="40%">ID:</td>
                <td width="60%"><?=$ID?></td>
            </tr>
            <tr>
                <td><?echo Loc::getMessage("VBCHBB_AFF_EDIT_TIMESTAMP")?></td>
                <td><?=$dbAccount['TIMESTAMP_X']?></td>
            </tr>
        <?endif;?>
        <tr class="adm-detail-required-field">
            <td width="40%"><?echo Loc::getMessage("VBCHBB_AFF_EDIT_NAME")?></td>
            <td width="60%">
                <input type="text" name="NAME" size="50" maxlength="50" value="<?= $dbAccount['NAME'] ?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td width="40%"><?echo Loc::getMessage("VBCHBB_AFF_EDIT_USERID")?></td>
            <td width="60%">
                <? echo FindUserID("USERID",  $dbAccount['USERID'] ); ?>
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td width="40%"><?echo Loc::getMessage("VBCHBB_AFF_EDIT_BONUS")?></td>
            <td width="60%">
                <input type="text" name="BONUS" size="50" maxlength="50" value="<?= $dbAccount['BONUS'] ?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_AFF_EDIT_SITE")?></td>
            <td>
                <?=CLang::SelectBox("LID", $dbAccount['LID']);?>
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_AFF_EDIT_PROMOCODE")?></td>
            <td>
                <input type="text" name="PROMOCODE" size="50" maxlength="50" value="<?= $dbAccount['PROMOCODE'] ?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_AFF_EDIT_DOMAINE")?></td>
            <td>
                <input type="text" name="DOMAINE" size="50" maxlength="50" value="<?= $dbAccount['DOMAINE'] ?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_AFF_EDIT_URL")?></td>
            <td>
                <textarea cols="50" rows="15" name="URL"><?= $dbAccount['URL'] ?></textarea>
            </td>
        </tr>

        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_AFF_EDIT_COMMISIA")?></td>
            <td>
                <input type="text" name="COMMISIA" size="50" maxlength="50" value="<?= $dbAccount['COMMISIA'] ?>">
            </td>
        </tr>

        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_AFF_EDIT_COMMISIAPROMO")?></td>
            <td>
                <input type="text" name="COMMISIAPROMO" size="50" maxlength="50" value="<?= $dbAccount['COMMISIAPROMO'] ?>">
            </td>
        </tr>
        <?
        $tabControl->EndTab();
        ?>
        <?
        $tabControl->Buttons(
            array(
                "back_url" => "/bitrix/admin/vbchbb_affiliate.php?lang=".LANG.GetFilterParams("filter_")
            )
        );
        ?>
        <?
        $tabControl->End();
        ?>
    </form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>