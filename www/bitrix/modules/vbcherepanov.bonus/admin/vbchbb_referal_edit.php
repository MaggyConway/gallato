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
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
ClearVars();
$errorMessage = "";
$bVarsFromForm = false;
$ID = IntVal($ID);
if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $bonusModulePermissions>="U" && check_bitrix_sessid())
{
    global $DB;
	$USERID = IntVal($USERID);
	$REFFROM= intval($REFFROM);
	if ($USERID <= 0)
		$errorMessage .= Loc::getMessage("VBCHBB_REFERAL_EDIT_EMPTY_USER").".<br>";
	if($USERID==$REFFROM){
		$errorMessage .= Loc::getMessage("VBCHBB_REFERAL_EDIT_DUBLE_USER").".<br>";
	}
	if($ID<=0) {
		$res = ITRound\Vbchbbonus\CVbchRefTable::getList(
			array(
				'filter' => array('USERID' => $USERID)
			)
		)->fetch();
		if ($res) {
			$errorMessage .= Loc::getMessage("VBCHBB_REFERAL_EDIT_DUBLE_USER1") . ".<br>";
		}
	}
	if(strlen($errorMessage)<=0){
		$arFields=array(
			'ACTIVE'=>$ACTIVE,
			'LID'=>$LID,
			'USERID'=>$USERID,
			'REFFROM'=>$REFFROM,
			'REFERER'=>htmlspecialchars($REFERER),
			'COOKIE'=>htmlspecialchars($COOKIE),

		);
		$DB->StartTransaction();
		if ($ID <= 0)
		{
			$arFields['TIMESTAMP_X']=new \Bitrix\Main\Type\DateTime();
            $arFields['ADDRECORDTYPE']=REF_ADD_MANUAL_ADMIN;
			$l=\ITRound\Vbchbbonus\CVbchRefTable::add($arFields);
			if($l->isSuccess()){
				$DB->Commit();
			}else{
				$DB->Rollback();
				$errorMessage .=implode('<br/>',$l->getErrorMessages());
			}
		}else{
			$l=\ITRound\Vbchbbonus\CVbchRefTable::update($ID,$arFields);
			if($l->isSuccess()){
				$DB->Commit();
			}else{
				$DB->Rollback();
				$errorMessage .=implode('<br/>',$l->getErrorMessages());
			}
		}
	}


	if (strlen($errorMessage) <= 0)
	{
		if (strlen($apply) <= 0)
			LocalRedirect("/bitrix/admin/vbchbb_referal.php?lang=".LANG.GetFilterParams("filter_", false));
	}

}
if ($ID > 0)
	$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_REFERAL_EDIT_UPDATING"));
else
	$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_REFERAL_EDIT_ADDING"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$dbAccount = Vbchbbonus\CVbchRefTable::getList(array('filter'=>array('ID'=>$ID)))->fetch();
if (!$dbAccount)
{
	$ID = 0;
	$dbAccount['REFERER']=\ITRound\Vbchbbonus\Vbchreferal::GenerateRef();
	$dbAccount['COOKIE']=\ITRound\Vbchbbonus\Vbchreferal::uniqKey();
}
if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("vbch_bonus_referal", "", "str_");

$aMenu = array(
	array(
		"TEXT" => Loc::getMessage("VBCHBB_REFERAL_EDITN_2FLIST"),
		"LINK" => "/bitrix/admin/vbchbb_referal.php?lang=".LANG.GetFilterParams("filter_"),
		"ICON"	=> "btn_list",
		"TITLE" => Loc::getMessage("VBCHBB_REFERAL_EDITN_2FLIST"),
	)
);

if ($ID > 0 && $bonusModulePermissions >= "U")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
		"TEXT" => Loc::getMessage("VBCHBB_REFERAL_EDITN_NEW_ACCOUNT"),
		"LINK" => "/bitrix/admin/vbchbb_referal_edit.php?lang=".LANG.GetFilterParams("filter_"),
		"ICON"	=> "btn_new",
		"TITLE" => Loc::getMessage("VBCHBB_REFERAL_EDITN_NEW_ACCOUNT"),
	);

	if ($bonusModulePermissions >= "W")
	{
		$aMenu[] = array(
			"TEXT" => Loc::getMessage("VBCHBB_REFERAL_EDITN_DELETE_ACCOUNT"),
			"LINK" => "javascript:if(confirm('".Loc::getMessage("VBCHBB_REFERAL_EDITN_DELETE_ACCOUNT_CONFIRM")."')) window.location='/bitrix/admin/vbchbb_referal.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
			"WARNING" => "Y",
			"ICON"	=> "btn_delete"
		);
	}
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

$SS=$BBCORE->GetSiteList();
$SITES=$SS['LIST'];
foreach($SITES as $ss){
	$sites['REFERENCE'][]='['.$ss['ID'].']'.$ss['NAME'];
	$sites['REFERENCE_ID'][]=$ss['ID'];
}
?>

<?if(strlen($errorMessage)>0)
	echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>Loc::getMessage("VBCHBB_PROFILES_EDIT_ERROR"), "HTML"=>true));?>


	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
		<?echo GetFilterHiddens("filter_");?>
		<input type="hidden" name="Update" value="Y">
		<input type="hidden" name="lang" value="<?echo LANG ?>">
		<input type="hidden" name="ID" value="<?echo $ID ?>">
		<input type="hidden" name="COOKIE" value="<?echo $dbAccount['COOKIE'] ?>">
		<?=bitrix_sessid_post()?>

		<?
		$aTabs = array(
			array("DIV" => "edit1", "TAB" => Loc::getMessage("VBCHBB_REFERAL_EDITN_TAB_ACCOUNT"), "ICON" => "sale", "TITLE" => Loc::getMessage("VBCHBB_REFERAL_EDITN_TAB_ACCOUNT"))
		);

		$tabControl = new CAdminTabControl("tabControl", $aTabs);
		$tabControl->Begin();
		?>

		<?
		$tabControl->BeginNextTab();
		?>

		<?if ($ID > 0):?>
			<tr>
				<td width="40%">ID:</td>
				<td width="60%"><?=$ID?></td>
			</tr>
			<tr>
				<td><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_TIMESTAMP")?></td>
				<td><?=$dbAccount['TIMESTAMP_X']?></td>
			</tr>
		<?endif;?>
		<tr class="adm-detail-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_ACTIVE")?></td>
			<td width="60%">
				<input type="hidden" name="ACTIVE" value="N"/>
				<input type="checkbox" name="ACTIVE" value="Y" <? echo ($dbAccount['ACTIVE']=='Y' ? 'checked' : '');?> />
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_SITEID")?></td>
			<td width="60%">
				<?echo SelectBoxFromArray("LID", $sites, $dbAccount['LID'], "", "", false)?>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_USERID")?></td>
			<td width="60%">
					<?echo FindUserID("USERID", $dbAccount['USERID']);?>
			</td>
		</tr>
		<tr class="adm-detail-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_REFFROM")?></td>
			<td width="60%">
				<?echo FindUserID("REFFROM", $dbAccount['REFFROM']);?>
			</td>
		</tr>
		<tr class="adm-detail-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_REFCODE")?></td>
			<td width="60%">
				<input type="text" name="REFERER" value="<?=$dbAccount['REFERER']?>"/>
				<b style="color:red"><?=Loc::getMessage('VBCHBB_REFERAL_EDIT_REFCODE_BAD')?></b>
			</td>
		</tr>
		<tr class="adm-detail-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_REFERAL_EDIT_COOKIE")?></td>
			<td width="60%">
				<span><b><?=$dbAccount['COOKIE']?></b></span>
			</td>
		</tr>

		<?
		$tabControl->EndTab();
		?>
		<?
		$tabControl->Buttons(
			array(
				"back_url" => "/bitrix/admin/vbchbb_account.php?lang=".LANG.GetFilterParams("filter_")
			)
		);
		?>
		<?
		$tabControl->End();
		?>
	</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>