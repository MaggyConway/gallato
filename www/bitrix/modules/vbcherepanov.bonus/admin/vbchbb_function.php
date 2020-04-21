<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if (isset($_REQUEST['work_start']))
{
	define("NO_AGENT_STATISTIC", true);
	define("NO_KEEP_STATISTIC", true);
}

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use ITRound\Vbchbbonus;
Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
if($_REQUEST['work_start'] && check_bitrix_sessid())
{
	global $DB;
	$qa=\Bitrix\Main\Config\Option::get('vbcherepanov.bonus','MOREFUNCTION',"");
	if($qa!=''){
		$qa=$BBCORE->CheckSerialize($qa);
	}
	$lastID = intval($_REQUEST["lastid"]);
	$table = "b_user";
	$step=$qa['STEP'] ? $qa['STEP'] : 10;
	if($qa['TYPE']!='frominner'){
		$rs = $DB->Query("select * from $table where ID>$lastID order by ID asc limit $step;");
		while ($ar = $rs->Fetch())
		{
			if($qa['TYPE']=='addbonus'){
				$BBCORE->AddALLBonus($ar['ID'],array('BONUS'=>$qa['BONUS'],'USERGROUP'=>$qa['USERGROUP'],'ACTIVE'=>$qa['ACTIVATE'],'DESCRIPTION'=>$qa['DESCRIPTION']));
			}
			if($qa['TYPE']=='addaccount'){
				$BBCORE->AddAccounts($ar['ID'],array('SCOREIN'=>$qa['SCOREIN'],'BONUSACCOUNTSID'=>$qa['BONUSACCOUNTSID']));
			}
			if($qa['TYPE']=='addreferal'){
				$BBCORE->SITE_ID=$qa['SITE_ID'];
				$BBCORE->AddAllReferalCode($ar['ID'],array('SITE_ID'=>$qa['SITE_ID']));
			}
			if($qa['TYPE']=='replacerefphone'){
                $BBCORE->SITE_ID=$qa['SITE_ID'];
                $BBCORE->ReplaceRefPhone($ar['ID'],array('SITE_ID'=>$qa['SITE_ID']));
            }
			$lastID = intval($ar["ID"]);
		}

		$rsLeftBorder = $DB->Query("select ID from $table where ID <= $lastID order by ID asc", false, "FILE: ".__FILE__."<br /> LINE: ".__LINE__);
		$leftBorderCnt = $rsLeftBorder->SelectedRowsCount();

		$rsAll = $DB->Query("select ID from $table;", false, "FILE: ".__FILE__."<br /> LINE: ".__LINE__);
		$allCnt = $rsAll->SelectedRowsCount();

		$p = round(100*$leftBorderCnt/$allCnt, 2);
	}else{
		if($qa['TYPE']=='frominner'){
			$rs = $DB->Query("select * from b_sale_user_account where ID>$lastID order by ID asc limit $step;");
			while ($ar = $rs->Fetch())
			{
				$BBCORE->FromInner($ar,array('BONUSACCOUNTSID'=>$qa['BONUSACCOUNTSID'],'DESC'=>Loc::getMessage('VBCHBB_BONUS_FROM_INNER_DESC')));

				$lastID = intval($ar["ID"]);
			}

			$rsLeftBorder = $DB->Query("select ID from b_sale_user_account  where ID <= $lastID order by ID asc", false, "FILE: ".__FILE__."<br /> LINE: ".__LINE__);
			$leftBorderCnt = $rsLeftBorder->SelectedRowsCount();

			$rsAll = $DB->Query("select ID from b_sale_user_account;", false, "FILE: ".__FILE__."<br /> LINE: ".__LINE__);
			$allCnt = $rsAll->SelectedRowsCount();

			$p = round(100*$leftBorderCnt/$allCnt, 2);
		}
	}

	echo 'CurrentStatus = Array('.$p.',"'.($p < 100 ? '&lastid='.$lastID : '').'","Обрабатываю запись с ID #'.$lastID.'");';

	die();
}
$bonusModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($bonusModulePermissions=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

Loader::includeModule("sale");
Loc::loadMessages(__FILE__);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_BONUS_ADDFUNCTION"));
$ra=$BBCORE->GetOptions($BBCORE->SITE_ID,"REFACTIVE");
$rf=$BBCORE->GetOptions($BBCORE->SITE_ID,"TYPEREFCODE");
$aTabs = array(
	array("DIV" => "edit1", "TAB" => Loc::getMessage("VBCHBB_BONUS_ADDMULTIBONUS"), "ICON" => "", "TITLE" => Loc::getMessage("VBCHBB_BONUS_ADDMULTIBONUS")),
	array("DIV" => "edit2", "TAB" => Loc::getMessage("VBCHBB_BONUS_CREATE_ACCOUNT"), "ICON" => "", "TITLE" => Loc::getMessage("VBCHBB_BONUS_CREATE_ACCOUNT")),
	array("DIV" => "edit3", "TAB" => Loc::getMessage("VBCHBB_BONUS_FROM_INNER"), "ICON" => "", "TITLE" => Loc::getMessage("VBCHBB_BONUS_FROM_INNER")),
);
if($ra['OPTION']=='Y'){
	$aTabs[]=array("DIV" => "edit4", "TAB" => Loc::getMessage('VBCHBB_BONUS_CREATE_REFERALCODE'), "ICON" => "", "TITLE" => Loc::getMessage('VBCHBB_BONUS_CREATE_REFERALCODE'));
}
if($rf['OPTION']=='PHONE'){
    $aTabs[]=array("DIV" => "edit5", "TAB" => Loc::getMessage('VBCHBB_BONUS_CREATE_REFERALCODEPHONE'), "ICON" => "", "TITLE" => Loc::getMessage('VBCHBB_BONUS_CREATE_REFERALCODEPHONE'));
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);



if ($REQUEST_METHOD=="POST"){
	$Fields=array();
	$_POST['STEP'] = $_POST['STEP'] ? $_POST['STEP'] : 10;
	if($_POST['addbonus']){
		$Fields=array(
			'BONUS'=>$_POST['BONUS'],
			'USERGROUP'=>$_POST['USERGROUP'],
			'ACTIVATE'=>$_POST['ACTIVATE'],
			'DESCRIPTION'=>$_POST['DESCRIPTION'],
			'TYPE'=>'addbonus',
			'STEP'=>$_POST['STEP'],
		);


	}
	if($_POST['addaccount']){
		$Fields=array(
			'SCOREIN'=>$_POST['SCOREIN'],
			'TYPE'=>'addaccount',
			'STEP'=>$_POST['STEP'],
            'BONUSACCOUNTSID'=>$_POST['BONUSINNERIN']['BONUSINNER'],
		);


	}
	if($_POST['addreferal']){
		$Fields=array(
			'SITE_ID'=>$_POST['SITE'],
			'TYPE'=>'addreferal',
			'STEP'=>$_POST['STEP'],
		);

	}
	if($_POST['frominner']){
		$Fields=array(
			'SITE_ID'=>$_POST['SITE'],
			'TYPE'=>'frominner',
			'STEP'=>$_POST['STEP'],
			'BONUSACCOUNTSID'=>$_POST['BONUSINNERIN']['BONUSINNER'],
		);

	}
    if($_POST['replacerefphone']){
        $Fields=array(
            'SITE_ID'=>$_POST['SITE'],
            'TYPE'=>'replacerefphone',
            'STEP'=>$_POST['STEP'],
        );

    }
	\Bitrix\Main\Config\Option::set('vbcherepanov.bonus','MOREFUNCTION','');
	\Bitrix\Main\Config\Option::set('vbcherepanov.bonus','MOREFUNCTION',base64_encode(serialize($Fields)));
	$saveOK=true;
}

$tabControl->Begin();
$tabControl->BeginNextTab();
echo ShowNote(Loc::getMessage('VBCHBB_BONUS_ADDMULTIBONUS_DESC'));
$qa=\Bitrix\Main\Config\Option::get('vbcherepanov.bonus','MOREFUNCTION',"");
if($qa!=''){
	$qa=$BBCORE->CheckSerialize($qa);
}
?>
    <form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">

		<?
		echo bitrix_sessid_post();

		foreach($BBCORE->GetAddBonusOption() as $Flds=>$Fld) {
			$element = $Fld['WIDGET'];
			$set = $Fld;
			unset($set['WIDGET']);
			if(is_array($qa) && array_key_exists($Flds,$qa))
				$set['VALUE'] = $qa[$Flds];
			// $set['SITE']=$BBCORE->SITE_ID;
			$set['NAME'] = $Flds;
			$element->settings = $set;
			unset($set);
			if(strpos($Flds,"HEAD")===false)
				call_user_func_array(array($element, "showBasicEditField"), array());
			else
				call_user_func_array(array($element, "showBasicHeader"), array());
		}
		?>
        <input type="submit" name="addbonus" value="<?=Loc::getMessage('VBCHBB_MOREFUNC_SAVE_OPTION')?>" />
		<?if(is_array($qa) && array_key_exists('TYPE',$qa) && $qa['TYPE']=='addbonus'){?>
            <br/><br/>
            <input type=button value="<?=Loc::getMessage('VBCHBB_START')?>" id="work_start" onclick="set_start(1)" />
            <input type=button value="<?=Loc::getMessage('VBCHBB_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
		<?}?>
		<?
		$tabControl->BeginNextTab();
		echo ShowNote(Loc::getMessage('VBCHBB_BONUS_CREATE_ACCOUNT_DESC'));
		?><?
		foreach($BBCORE->GetAddAccountOption() as $Flds=>$Fld) {
			$element = $Fld['WIDGET'];
			$set = $Fld;
			unset($set['WIDGET']);
			$set['VALUE'] = '';
			// $set['SITE']=$BBCORE->SITE_ID;
			$set['NAME'] = $Flds;
			$element->settings = $set;
			unset($set);
			if(strpos($Flds,"HEAD")===false)
				call_user_func_array(array($element, "showBasicEditField"), array());
			else
				call_user_func_array(array($element, "showBasicHeader"), array());
		}
		?>
        <input type="submit" name="addaccount" value="<?=Loc::getMessage('VBCHBB_MOREFUNC_SAVE_OPTION')?>"/>
		<?if(is_array($qa) && array_key_exists('TYPE',$qa) && $qa['TYPE']=='addaccount'){?>
            <br/><br/>
            <input type=button value="<?=Loc::getMessage('VBCHBB_START')?>" id="work_start" onclick="set_start(1)" />
            <input type=button value="<?=Loc::getMessage('VBCHBB_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
            <?
            $k=new  Vbchbbonus\BonusInnerWidget();
            $k->setSetting("NAME","BONUSINNERIN");
            $k->setSetting('TITLE','Счет для зачисления:');
            echo $k->showBasicEditField();
            ?>
		<?}?>
		<?
		$tabControl->BeginNextTab();
		echo ShowNote(Loc::getMessage('VBCHBB_BONUS_FROM_INNER'));
		?><?
		foreach($BBCORE->GetFromInnerOption() as $Flds=>$Fld) {
			$element = $Fld['WIDGET'];
			$set = $Fld;
			unset($set['WIDGET']);
			$set['VALUE'] = '';
			// $set['SITE']=$BBCORE->SITE_ID;
			$set['NAME'] = $Flds;
			$element->settings = $set;
			unset($set);
			if(strpos($Flds,"HEAD")===false)
				call_user_func_array(array($element, "showBasicEditField"), array());
			else
				call_user_func_array(array($element, "showBasicHeader"), array());
		}
		?>
        <input type="submit" name="frominner" value="<?=Loc::getMessage('VBCHBB_MOREFUNC_SAVE_OPTION')?>"/>
		<?if(is_array($qa) && array_key_exists('TYPE',$qa) && $qa['TYPE']=='frominner'){?>
            <br/><br/>
            <input type=button value="<?=Loc::getMessage('VBCHBB_START')?>" id="work_start" onclick="set_start(1)" />
            <input type=button value="<?=Loc::getMessage('VBCHBB_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
		<?}?>
		<?




		if($ra['OPTION']=='Y'){
			$tabControl->BeginNextTab();
			echo ShowNote(Loc::getMessage('VBCHBB_BONUS_CREATE_REFERALCODE'));?>
			<?foreach($BBCORE->GetAddUserReferalOption() as $Flds=>$Fld) {
				$element = $Fld['WIDGET'];
				$set = $Fld;
				unset($set['WIDGET']);
				$set['VALUE'] = '';
				// $set['SITE']=$BBCORE->SITE_ID;
				$set['NAME'] = $Flds;
				$element->settings = $set;
				unset($set);
				if(strpos($Flds,"HEAD")===false)
					call_user_func_array(array($element, "showBasicEditField"), array());
				else
					call_user_func_array(array($element, "showBasicHeader"), array());
			}
			?>

            <input type="submit" name="addreferal" value="<?=Loc::getMessage('VBCHBB_MOREFUNC_SAVE_OPTION')?>" />
			<?if(is_array($qa) && array_key_exists('TYPE',$qa) && $qa['TYPE']=='addreferal'){?>
                <br/><br/>
                <input type=button value="<?=Loc::getMessage('VBCHBB_START')?>" id="work_start" onclick="set_start(1)" />
                <input type=button value="<?=Loc::getMessage('VBCHBB_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
			<?}?>
		<?}
        if($rf['OPTION']=='PHONE'){
        $tabControl->BeginNextTab();
        echo ShowNote(Loc::getMessage('VBCHBB_BONUS_CREATE_REFERALCODEPHONE_DESC'));?>
        <?foreach($BBCORE->GetAddUserReferalOption() as $Flds=>$Fld) {
            $element = $Fld['WIDGET'];
            $set = $Fld;
            unset($set['WIDGET']);
            $set['VALUE'] = '';
            // $set['SITE']=$BBCORE->SITE_ID;
            $set['NAME'] = $Flds;
            $element->settings = $set;
            unset($set);
            if(strpos($Flds,"HEAD")===false)
                call_user_func_array(array($element, "showBasicEditField"), array());
            else
                call_user_func_array(array($element, "showBasicHeader"), array());
        }
        ?>

        <input type="submit" name="replacerefphone" value="<?=Loc::getMessage('VBCHBB_MOREFUNC_SAVE_OPTION')?>" />
        <?if(is_array($qa) && array_key_exists('TYPE',$qa) && $qa['TYPE']=='replacerefphone'){?>
            <br/><br/>
            <input type=button value="<?=Loc::getMessage('VBCHBB_START')?>" id="work_start" onclick="set_start(1)" />
            <input type=button value="<?=Loc::getMessage('VBCHBB_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
        <?}?>

    <?}?>

		<?
		$tabControl->End();
		if(is_array($qa) && array_key_exists('STEP',$qa) ){?>
            <?$k=$qa['STEP'];?>
        <?}?>
		<?=Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_STEP')?>&nbsp;<input type="text" name="STEP" value="<?=$k?>"/>
    </form>
    <script type="text/javascript">

        var bWorkFinished = false;
        var bSubmit;

        function set_start(val)
        {
            document.getElementById('work_start').disabled = val ? 'disabled' : '';
            document.getElementById('work_stop').disabled = val ? '' : 'disabled';
            document.getElementById('progress').style.display = val ? 'block' : 'none';
            if (val)
            {

                ShowWaitWindow();
                document.getElementById('status').innerHTML = 'Работаю...';
                document.getElementById('percent').innerHTML = '0%';
                document.getElementById('indicator').style.width = '0%';

                CHttpRequest.Action = work_onload;
                CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>');
            }
            else
                CloseWaitWindow();
        }

        function work_onload(result)
        {
            console.log(result);
            try
            {
                eval(result);
                console.log(result);
                iPercent = CurrentStatus[0];
                strNextRequest = CurrentStatus[1];

                document.getElementById('percent').innerHTML = iPercent + '%';
                document.getElementById('indicator').style.width = iPercent + '%';

                document.getElementById('status').innerHTML = 'Работаю...';



                if (strNextRequest && document.getElementById('work_start').disabled)
                    CHttpRequest.Send('<?= $_SERVER["PHP_SELF"]?>?work_start=Y&lang=<?=LANGUAGE_ID?>&<?=bitrix_sessid_get()?>' + strNextRequest);
                else
                {
                    set_start(0);
                    bWorkFinished = true;
                }

            }
            catch(e)
            {
                CloseWaitWindow();
                document.getElementById('work_start').disabled = '';
                alert('Сбой в получении данных');
            }
        }

    </script>
    <div id="progress" style="display:none;" width="100%">
        <br />
        <div id="status"></div>
        <table border="0" cellspacing="0" cellpadding="2" width="100%">
            <tr>
                <td height="10">
                    <div style="border:1px solid #B9CBDF">
                        <div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
                    </div>
                </td>
                <td width=30>&nbsp;<span id="percent">0%</span></td>
            </tr>
        </table>
    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>