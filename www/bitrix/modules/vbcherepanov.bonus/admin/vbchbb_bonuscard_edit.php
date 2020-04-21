<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use ITRound\Vbchbbonus;
Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$bonusModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($saleModulePermissions=="D")
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
ClearVars();
$errorMessage = "";
$bVarsFromForm = false;
$ID = IntVal($ID);

$CURLIST=$BBCORE->ModuleCurrency();

if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $bonusModulePermissions>="U" && check_bitrix_sessid())
{
	$arFields=array(
		'LID'=>$LID,
		'ACTIVE'=>$ACTIVE,
		"USERID"=>intval($USERID),
		'NUM'=>trim(htmlspecialchars($NUM)),
        'DEFAULTBONUS'=>$DEFAULTBONUS,
        'BONUSACCOUNTS'=>$BONUSACCOUNTS,
	);


	if($arFields['TIMESTAMP_X']=='')
		$arFields['TIMESTAMP_X']=new \Bitrix\Main\Type\DateTime();

	Application::getConnection()->startTransaction();
	if ($ID <= 0)
	{
		if(!Vbchbbonus\BonusCardTable::add($arFields))
			{
				Application::getConnection()->rollbackTransaction();
				$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
			}
			Application::getConnection()->commitTransaction();

	}
	else
	{
		if (strlen($errorMessage) <= 0)
		{

			if(!Vbchbbonus\BonusCardTable::update($ID,$arFields))
			{
				Application::getConnection()->rollbackTransaction();
				$lAdmin->AddGroupError(Loc::getMessage("bonus_del_err"), $i);
			}
			Application::getConnection()->commitTransaction();

		}
	}

	if (strlen($errorMessage) <= 0)
	{
		if (strlen($apply) <= 0)
			LocalRedirect("/bitrix/admin/vbchbb_bonuscard.php?lang=".LANG.GetFilterParams("filter_", false));
	}
	else
	{
		$bVarsFromForm = true;
	}
}
if ($ID > 0)
	$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_BONUSCARD_EDIT_UPDATING"));
else
	$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_BONUSCARD_EDIT_ADDING"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$dbAccount = Vbchbbonus\BonusCardTable::getList(array('filter'=>array('ID'=>$ID)))->fetch();
if (!$dbAccount)
{
	$ID = 0;
}
if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("vbch_bonus_card", "", "str_");

$aMenu = array(
	array(
		"TEXT" => Loc::getMessage("VBCHBB_BONUSCARD_EDITN_2FLIST"),
		"LINK" => "/bitrix/admin/vbchbb_bonuscard.php?lang=".LANG.GetFilterParams("filter_"),
		"ICON"	=> "btn_list",
		"TITLE" => Loc::getMessage("VBCHBB_BONUSCARD_EDITN_2FLIST"),
	)
);

if ($ID > 0 && $bonusModulePermissions >= "U")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
		"TEXT" => Loc::getMessage("VBCHBB_BONUSCARD_EDIT_ADDING"),
		"LINK" => "/bitrix/admin/vbchb_affiliate_edit.php?lang=".LANG.GetFilterParams("filter_"),
		"ICON"	=> "btn_new",
		"TITLE" => Loc::getMessage("VBCHBB_BONUSCARD_EDIT_ADDING"),
	);

	if ($saleModulePermissions >= "W")
	{
		$aMenu[] = array(
			"TEXT" => Loc::getMessage("VBCHBB_BONUSCARD_DELETE"),
			"LINK" => "javascript:if(confirm('".Loc::getMessage("VBCHBB_BONUSCARD_EDITN_DELETE_ACCOUNT_CONFIRM")."')) window.location='/bitrix/admin/vbchbb_affiliate.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
			"WARNING" => "Y",
			"ICON"	=> "btn_delete"
		);
	}
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

	$dbResultList = \ITRound\Vbchbbonus\CVbchBonusaccountsTable::getList(array(
		'filter' => array('ACTIVE' => "Y"),
		'select' => array('ID', 'NAME'),
	))->fetchAll();
	if (sizeof($dbResultList) > 0) {
		foreach ($dbResultList as $arAc) {
			$acc_list['REFERENCE_ID'][] = $arAc['ID'];
			$acc_list['REFERENCE'][] = $arAc['NAME'];
		}

	}
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
			$title=Loc::getMessage('VBCHBB_BONUSCARD_EDIT_UPDATING');
		}else{
			$title=Loc::getMessage('VBCHBB_BONUSCARD_EDIT_ADDING');
		}

		$aTabs = array(
			array("DIV" => "edit1", "TAB" =>$title, "ICON" => "sale", "TITLE" => $title)
		);

		$tabControl = new CAdminTabControl("tabControl", $aTabs);
		$tabControl->Begin();
		?>

		<?
		$tabControl->BeginNextTab();
		?>
		<tr>
			<td valign="top"><?echo Loc::getMessage("VBCHBB_BONUSCARD_EDIT_ACTIVE")?></td>
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
				<td><?echo Loc::getMessage("VBCHBB_BONUSCARD_EDIT_TIMESTAMP")?></td>
				<td><?=$dbAccount['TIMESTAMP_X']?></td>
			</tr>
		<?endif;?>
		<tr class="adm-detail-required-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_BONUSCARD_EDIT_NUM")?></td>
			<td width="60%">
				<input type="text" name="NUM" size="50" maxlength="50" value="<?= $dbAccount['NUM'] ?>">
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td width="40%"><?echo Loc::getMessage("VBCHBB_BONUSCARD_EDIT_USERID")?></td>
			<td width="60%">
				<? echo FindUserID("USERID",  $dbAccount['USERID'] ); ?>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td><?echo GetMessage("VBCHBB_BONUSCARD_EDIT_SITE")?></td>
			<td>
				<?=CLang::SelectBox("LID", $dbAccount['LID']);?>
			</td>
		</tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_BONUSCARD_EDIT_DEFAULTBONUS")?></td>
            <td>
                <input type="text" name="DEFAULTBONUS" size="50" maxlength="50" value="<?= $dbAccount['DEFAULTBONUS']?$dbAccount['DEFAULTBONUS']: '0.00' ?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td><?echo GetMessage("VBCHBB_BONUSCARD_EDIT_BONUSACCOUNTS")?></td>
            <td>
	            <? echo SelectBoxFromArray("BONUSACCOUNTS", $acc_list, $dbAccount['BONUSACCOUNTS'], "", "", false) ?>
            </td>
        </tr>
		<?
		$tabControl->EndTab();
		?>
		<?
		$tabControl->Buttons(
			array(
				"back_url" => "/bitrix/admin/vbchbb_bonuscard.php?lang=".LANG.GetFilterParams("filter_")
			)
		);
		?>
		<?
		$tabControl->End();
		?>
	</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>