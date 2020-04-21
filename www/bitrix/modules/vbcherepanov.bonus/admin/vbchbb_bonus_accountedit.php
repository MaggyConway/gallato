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
	if ($ID <= 0)
	{
		$USER_ID = IntVal($USER_ID);
		if ($USER_ID <= 0)
			$errorMessage .= Loc::getMessage("VBCHBB_ACCOUNT_EDIT_EMPTY_USER").".<br>";
		if (strlen($errorMessage) <= 0)
		{
			$num=Vbchbbonus\AccountTable::getList(array(
				'filter'=>array('USER_ID'=>$USER_ID,'=BONUSACCOUNTSID'=>$BONUSACCOUNTSID),
			))->getSelectedRowsCount();
			if (IntVal($num) > 0)
				$errorMessage .= str_replace("#USER#", $USER_ID, Loc::getMessage("VBCHBB_ACCOUNT_EDIT_ALREADY_EXISTS")).".<br>";
		}

		if (strlen($errorMessage) <= 0)
		{
			$OLD_BUDGET = 0.0;
		}
	}
	else
	{
		if (!($arOldUserAccount = Vbchbbonus\AccountTable::getList(array('filter'=>array('ID'=>$ID)))->fetch()))
			$errorMessage .= str_replace("#ID#", $ID, Loc::getMessage("VBCHBB_ACCOUNT_EDIT_NO_ACCOUNT")).".<br>";

		if (strlen($errorMessage) <= 0)
		{
			$USER_ID = $arOldUserAccount["USER_ID"];
			$CURRENCY = $arOldUserAccount["CURRENCY"];
			$OLD_BUDGET = DoubleVal($arOldUserAccount["CURRENT_BUDGET"]);
		}
	}
	if (strlen($errorMessage) <= 0)
	{
    	$arUserAccount = Vbchbbonus\AccountTable::getList(array('filter'=>array('USER_ID'=>$USER_ID)))->fetch();;
		$CURRENT_BUDGET = str_replace(",", ".", $CURRENT_BUDGET);
		$CURRENT_BUDGET = DoubleVal($CURRENT_BUDGET);
		$updateSum = $CURRENT_BUDGET - $OLD_BUDGET;
		$lp=$BBCORE->INSTALL_PROFILE['BONUS'];
		$lp->setDesc($NOTES);
		$res = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
			'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $BBCORE->SITE_ID),
		))->fetchAll();
		if ($BBCORE->CheckArray($res)) {
			foreach ($res as $prof) {
				$l=$BBCORE->CheckSerialize($prof['BONUSCONFIG']);
				$l['BONUSINNERIN']['BONUSINNER']=$BONUSACCOUNTSID;
				$prof['BONUSCONFIG']=base64_encode(serialize($l));
                $l=$BBCORE->CheckSerialize($prof['NOTIFICATION']);
                $l['TRANSACATIONMESSAGE']=$NOTES;
                $prof['NOTIFICATION']=base64_encode(serialize($l));
				$check = ($prof['ISADMIN'] == 'Y');
				$check = ($check) ? $USER->isAdmin() : $check;
				if ($check) {
					$BBCORE->AddBonus(array('bonus'=>$updateSum,
						'ACTIVE'=>'Y',
						'ACTIVE_FROM'=>'',
						'ACTIVE_TO'=>'',
						'CURRENCY'=>''),
						array('SITE_ID'=>$BBCORE->SITE_ID,
							   'USER_ID'=>$USER_ID,
                               'IDUNITS'=>'EDIT_ACCOUNT'.$ID.'_'.$updateSum.'_'.time(),
                                'DESCRIPTION' => $NOTES
                         ),$prof,true);
						Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
				}
			}
		}


	}
	if (strlen($errorMessage) <= 0)
	{
		if (strlen($apply) <= 0)
			LocalRedirect("/bitrix/admin/vbchbb_account.php?lang=".LANG.GetFilterParams("filter_", false));
	}
	else
	{
		$bVarsFromForm = true;
	}
}
if ($ID > 0)
	$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_ACCOUNT_EDIT_UPDATING"));
else
	$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_ACCOUNT_EDIT_ADDING"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$dbAccount = Vbchbbonus\AccountTable::getList(array('filter'=>array('ID'=>$ID)))->fetch();
if (!$dbAccount)
{
	$ID = 0;
}
if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("vbch_bonus_account", "", "str_");

$aMenu = array(
		array(
				"TEXT" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_2FLIST"),
				"LINK" => "/bitrix/admin/vbchbb_account.php?lang=".LANG.GetFilterParams("filter_"),
				"ICON"	=> "btn_list",
				"TITLE" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_2FLIST_TITLE"),
			)
	);

if ($ID > 0 && $saleModulePermissions >= "U")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_NEW_ACCOUNT"),
			"LINK" => "/bitrix/admin/sale_account_edit.php?lang=".LANG.GetFilterParams("filter_"),
			"ICON"	=> "btn_new",
			"TITLE" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_NEW_ACCOUNT_TITLE"),
		);

	if ($saleModulePermissions >= "W")
	{
		$aMenu[] = array(
				"TEXT" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_DELETE_ACCOUNT"),
				"LINK" => "javascript:if(confirm('".Loc::getMessage("VBCHBB_ACCOUNT_EDITN_DELETE_ACCOUNT_CONFIRM")."')) window.location='/bitrix/admin/vbchbb_account.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."#tb';",
				"WARNING" => "Y",
				"ICON"	=> "btn_delete"
			);
	}
}
if($ID<=0){
	 $dbResultList=Vbchbbonus\CVbchBonusaccountsTable::getList(array(
            'filter'=>array('ACTIVE'=>"Y"),
     ))->fetchAll();
	 if(sizeof($dbResultList)>0){
		 foreach($dbResultList as $arAc){
			 $acc_list['REFERENCE_ID'][]=$arAc['ID'];
			 $acc_list['REFERENCE'][]=$arAc['NAME'];
		 }
		 
	 }
}

$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?if(strlen($errorMessage)>0)
	echo CAdminMessage::ShowMessage(Array("DETAILS"=>$errorMessage, "TYPE"=>"ERROR", "MESSAGE"=>Loc::getMessage("VBCHBB_ACCOUNT_EDIT_ERROR"), "HTML"=>true));?>


<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<input type="hidden" name="BONUSACCOUNTSID" value="<?echo $dbAccount['BONUSACCOUNTSID'] ?>">
<?=bitrix_sessid_post()?>

<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_TAB_ACCOUNT"), "ICON" => "sale", "TITLE" => Loc::getMessage("VBCHBB_ACCOUNT_EDITN_TAB_ACCOUNT_DESCR"))
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
			<td><?echo Loc::getMessage("VBCHBB_ACCOUNT_EDIT_TIMESTAMP")?></td>
			<td><?=$dbAccount['TIMESTAMP_X']?></td>
		</tr>
	<?endif;?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?echo Loc::getMessage("VBCHBB_ACCOUNT_EDIT_USER1")?></td>
		<td width="60%">
			<?if ($ID > 0):
				$usr=$BBCORE->GetUserInfo($dbAccount['USER_ID']);
				?>
				<input type="hidden" name="USER_ID" value="<?=$dbAccount['USER_ID']?>">
				[<a title="<?echo Loc::getMessage("VBCHBB_ACCOUNT_EDIT_USER_PROFILE")?>" href="/bitrix/admin/user_edit.php?lang=<?=LANGUAGE_ID?>&ID=<?=$dbAccount['USER_ID']?>"><?=$dbAccount['USER_ID']?></a>] (<?=$usr['EMAIL']?>) <?=$usr['FIO']?>
			<?else:?>
			<?echo FindUserID("USER_ID", $dbAccount['USER_ID']);?>
			<?endif;?>
			</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage("VBCHBB_ACCOUNT_EDIT_SUM")?></td>
		<td>
			<input type="text" name="CURRENT_BUDGET" size="10" maxlength="20" value="<?= roundEx($dbAccount['CURRENT_BUDGET'], SALE_VALUE_PRECISION) ?>">
			<?
			if ($ID > 0)
			{
				?>
				<input type="hidden" name="CURRENCY" value="<?= $dbAccount['CURRENCY'] ?>">
				<?=$dbAccount['CURRENCY'] ?>
				<?
			}
			else
			{?>
				<input type="hidden" name="CURRENCY" value="<?=$CURLIST?>">
				<?=$CURLIST?>
			<?}
			?>
		</td>
	</tr>
	<?if($ID<=0){?>
		<tr class="adm-detail-required-field">
		<td>BONUS ACCOUNTS</td>
		<td>
				<?echo SelectBoxFromArray("BONUSACCOUNTSID", $acc_list, $dbAccount['BONUSACCOUNTSID'], "", "", false)?>
		</td>
	</tr>
	<?}?>
	
	
	
			
	<tr>
		<td valign="top"><?echo Loc::getMessage("VBCHBB_ACCOUNT_EDIT_NOTES")?></td>
		<td valign="top">
			<textarea name="NOTES" rows="3" cols="40"><?=$dbAccount['NOTES']?></textarea>
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