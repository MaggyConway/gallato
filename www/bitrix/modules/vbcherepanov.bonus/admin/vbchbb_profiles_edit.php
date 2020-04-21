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
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
ClearVars();
$errorMessage = "";
$bVarsFromForm = false;
$ID = IntVal($ID);
if(!empty($_REQUEST['PROFILES_TYPE'])){
    $types=$_REQUEST['PROFILES_TYPE'];
    $cls=$BBCORE->INSTALL_PROFILE[$types];
    if(!is_null($cls))
        $name=call_user_func_array(array($cls,"getDesc"),array());
}
$APPLICATION->ResetException();
// ACTION
if (isset ( $_REQUEST['action'] ))
{
    if ($_REQUEST['action'] == "copy")
    {
        if ($ID > 0)
        {
            $dat=Vbchbbonus\CvbchbonusprofilesTable::getById($ID)->fetch();
            unset($dat['ID'],$dat['TIMESTAMP_X'],$dat['COUNTS']);
            $type=$dat['TYPE'];
            $result=Vbchbbonus\CvbchbonusprofilesTable::add($dat);
            unset($dat);
            if ($result->isSuccess ())
            {
                $ID = $result->getId ();
                LocalRedirect ( "/bitrix/admin/vbchbb_profiles_edit.php?ID=".$ID."&lang=".LANG."&PROFILES_TYPE=".$type);
            }
        }
    }
}

if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $bonusModulePermissions>="U" && check_bitrix_sessid())
{
    $tp=$TYPE;
    $cls=$BBCORE->INSTALL_PROFILE[$tp];
    $fldsP=$BBCORE->GetProfileFields($cls);
    $RESULT=array();

    if($BBCORE->CheckArray($fldsP)){
        foreach($fldsP as $key=>$lp){
            if($BBCORE->CheckArray($lp)){
                foreach($lp as $lp_key=>$lp_val){
                    $RESULT[$key][$lp_key]=${$lp_key};
                }
            }else{
                $RESULT[$key]=${$key};
            }
        }
    }
    if(is_array($RESULT['FILTER']) && array_key_exists('ELEMENTFILTER',$RESULT['FILTER'])){
            $obCond = new Vbchbbonus\CITRBBFilterCatalogCondTree();
            $boolCond = $obCond->Init(BT_COND_MODE_PARSE, 0, array());
            if (!$boolCond){
                if ($ex = $APPLICATION->GetException()){
                    echo $ex->GetString() . "<br>";
                }
            }
            $RESULT['FILTER']['ELEMENTFILTER']=$obCond->Parse( $RESULT['FILTER']['ELEMENTFILTER'] );
            $l=$BBCORE->GetElementFilter($RESULT['FILTER']['ELEMENTFILTER']);
            $RESULT['FILTER']['ELMNTFLTR']=$l;

    }

    foreach($RESULT as &$RES_val){
        if($BBCORE->CheckArray($RES_val)){
            $RES_val=base64_encode(serialize($RES_val));
        }
        unset($RES_val);
    }
    if($RESULT['TIMESTAMP_X']=='')
        $RESULT['TIMESTAMP_X']=new \Bitrix\Main\Type\DateTime();
    if($RESULT['ACTIVE_TO'])
        $RESULT['ACTIVE_TO']=new \Bitrix\Main\Type\DateTime($RESULT['ACTIVE_TO']);
    if($RESULT['ACTIVE_FROM'])
        $RESULT['ACTIVE_FROM']=new \Bitrix\Main\Type\DateTime($RESULT['ACTIVE_FROM']);
    if ($ID <= 0)
    {
        $res=Vbchbbonus\CvbchbonusprofilesTable::add($RESULT);
        if($res->isSuccess())
        {
            $ID=$res->getId();
            $res=($ID>0);
        }else{
            $errorMessage=implode("<br/>",$res->getErrorMessages());
            $res=false;
        }
    }
    else
    {
        $res=Vbchbbonus\CvbchbonusprofilesTable::update($ID,$RESULT);
        if(!$res->isSuccess())
            $errorMessage=implode("<br/>",$res->getErrorMessages());
    }
    if (strlen($errorMessage) <= 0)
    {
        if($res)
        {
            if(strlen($save) > 0)
                LocalRedirect("/bitrix/admin/vbchbb_profiles.php");
            elseif(strlen($apply) > 0)
                LocalRedirect("/bitrix/admin/vbchbb_profiles_edit.php?&ID=".$ID."&lang=".LANG."&PROFILES_TYPE=".$TYPE);
        }
    }
    else
    {
        $bVarsFromForm = true;
        LocalRedirect("/bitrix/admin/vbchbb_profiles_edit.php?&ID=".$ID."&lang=".LANG."&PROFILES_TYPE=".$TYPE);
    }
}
if ($ID > 0)
    $APPLICATION->SetTitle(Loc::getMessage("VBCHBB_PROFILES_EDIT_UPDATING").' ['.$name.']');
else
    $APPLICATION->SetTitle(Loc::getMessage("VBCHBB_PROFILES_EDIT_ADDING").' ['.$name.']');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$dbAccount = Vbchbbonus\CvbchbonusprofilesTable::getList(array('filter'=>array('ID'=>$ID)))->fetch();
if (!$dbAccount)
{
    $ID = 0;
}
if ($bVarsFromForm)
    $DB->InitTableVarsForEdit("vbch_bonus_profile", "", "str_");

$aMenu = array(
    array(
        "TEXT" => Loc::getMessage("VBCHBB_PROFILES_EDITN_2FLIST"),
        "LINK" => "/bitrix/admin/vbchbb_profiles.php?lang=".LANG.GetFilterParams("filter_"),
        "ICON"	=> "btn_list",
        "TITLE" => Loc::getMessage("VBCHBB_PROFILES_EDITN_2FLIST_TITLE"),
    )
);
if ($ID > 0 && $bonusModulePermissions >= "U")
{
    $aMenu[] = array("SEPARATOR" => "Y");
    if ($bonusModulePermissions >= "W")
    {
        $aMenu[] = array (
            "TEXT" => Loc::getMessage ("VBCHBB_PROFILES_EDIT_ADDING" ),
            "TITLE" => Loc::getMessage ( "VBCHBB_PROFILES_EDIT_ADDING" ),
            "LINK" => "vbchbb_profiles_edit.php?lang=ru&PROFILES_TYPE=".$type,
            "ICON" => "btn_new"
        );
        $aMenu[] = array (
            "TEXT" => Loc::getMessage ("VBCHBB_PROFILES_EDITN_COPY_ACCOUNT" ),
            "TITLE" => Loc::getMessage ( "VBCHBB_PROFILES_EDITN_COPY_ACCOUNT" ),
            "LINK" => "vbchbb_profiles_edit.php?action=copy&ID=" . $ID . "lang=" . LANG . "&" . bitrix_sessid_get () . "';",
            "ICON" => "btn_copy"
        );
        $aMenu[] = array(
            "TEXT" => Loc::getMessage("VBCHBB_PROFILES_EDITN_DELETE_ACCOUNT"),
            "LINK" => "javascript:if(confirm('".Loc::getMessage("VBCHBB_ACCOUNT_EDITN_DELETE_ACCOUNT_CONFIRM")."')) window.location='/bitrix/admin/vbchbb_profiles.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
            "WARNING" => "Y",
            "ICON"	=> "btn_delete"
        );

    }

}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>
<?if(strlen($errorMessage)>0)
    echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>Loc::getMessage("VBCHBB_PROFILES_EDIT_ERROR"), "HTML"=>true));?>
    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => Loc::getMessage("VBCHBB_PROFILES_EDITN_TAB_ACCOUNT").' ['.$name.']', "ICON" => "sale", "TITLE" => Loc::getMessage("VBCHBB_PROFILES_EDITN_TAB_ACCOUNT").' ['.$name.']')
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $tabControl->Begin();?>
    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
        <?$tabControl->BeginNextTab();?>
        <?echo GetFilterHiddens("filter_");?>
        <input type="hidden" name="Update" value="Y">
        <input type="hidden" name="lang" value="<?echo LANG ?>">
        <input type="hidden" name="ID" value="<?echo $ID ?>">
        <input type="hidden" name="TYPE" value="<?echo $types ?>">
        <?=bitrix_sessid_post()?>
        <?
        if(!is_null($cls))
            echo call_user_func_array(array($cls,"GetParameters"),array($ID));
        
        $tabControl->EndTab();

        $tabControl->Buttons(
            array(
                "back_url" => "/bitrix/admin/vbchbb_profiles.php?lang=".LANG.GetFilterParams("filter_")
            )
        );
        $tabControl->End();
        ?>

    </form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>