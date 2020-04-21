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
use ITRound\Vbchbbonus\CVbchBonusaccountsTable;

Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$USERID=intval(htmlspecialchars($_GET['USER_ID']));
if($_REQUEST['work_start'] && check_bitrix_sessid())
{
	global $DB;
	$qa=\Bitrix\Main\Config\Option::get('vbcherepanov.bonus','CHECKBALANCE',"");
	if($qa!=''){
		$qa=$BBCORE->CheckSerialize($qa);
	}
	if($qa['TYPE']=='checkbalance') {
        $res=\ITRound\Vbchbbonus\DoubleTable::getList([
                'filter'=>['USER_ID'=>$qa['USER'],'BONUSACCOUNTSID'=>$qa['CHECKBALANCEBONUSACCOUNTSID']],
                'select'=>['DEBIT_SUMM','CREDIT_SUMM'],
                'runtime' => array(
                    new \Bitrix\Main\Entity\ExpressionField('DEBIT_SUMM', 'SUM(DEBIT)'),
                    new \Bitrix\Main\Entity\ExpressionField('CREDIT_SUMM', 'SUM(CREDIT)'),
                )
        ])->fetch();
         $usr=Vbchbbonus\AccountTable::getList(
            [
                'filter'=>['USER_ID'=>$qa['USER'],'BONUSACCOUNTSID'=>$qa['CHECKBALANCEBONUSACCOUNTSID']],
                'select'=>['CURRENT_BUDGET']
            ]
        )->fetch();
    }

    if($res){
        if($res['CREDIT_SUMM']>$res['DEBIT_SUMM'])
            $k=$res['CREDIT_SUMM']-$res['DEBIT_SUMM'];
        else
            $k=$res['DEBIT_SUMM']-$res['CREDIT_SUMM'];
        $bal=$usr['CURRENT_BUDGET']-$k;
        if($bal==0){
            $message='<span style=\'color:green\'><b>Расхождений нет</b></span>';
        }
        if($bal<0){
            $message='<span style=\'color:red\'><b>Пользователю должны еще '.$BBCORE->ReturnCurrency((-1)*$bal).'</b></span>';
        }
        if($bal>0){
            $message = '<span style=\'color:red\'><b>у пользователя лишние '.$BBCORE->ReturnCurrency($bal).'</b></span>';
        }
    }
    $p=100;
	echo 'CurrentStatus = Array('.$p.',"'.($p < 100 ? '&lastid='.$qa['USER'] : '').'","Баланс пользователя: #'.$qa['USER'].'. '.$message.'");';
    \Bitrix\Main\Config\Option::set('vbcherepanov.bonus','CHECKBALANCE',"");
	die();
}
$bonusModulePermissions = $APPLICATION->GetGroupRight($module_id);
if ($bonusModulePermissions=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

Loader::includeModule("sale");
Loc::loadMessages(__FILE__);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle(Loc::getMessage("VBCHBB_BONUS_CHECBALANCE"));

$aTabs = array(
	array("DIV" => "edit1", "TAB" => Loc::getMessage("VBCHBB_BONUS_CHECBALANCE"), "ICON" => "", "TITLE" => Loc::getMessage("VBCHBB_BONUS_CHECBALANCE")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$saveOK=false;


if ($REQUEST_METHOD=="POST"){
	$Fields=array();
	if($_POST['checkbalance']){
		$Fields=array(
			'USER'=>$_POST['USER_ID'],
			'TYPE'=>'checkbalance',
			'STEP'=>$_POST['STEP'],
            'CHECKBALANCEBONUSACCOUNTSID'=>$_POST['CHECKBALANCEBONUSACCOUNTSID'],
		);
	}

	\Bitrix\Main\Config\Option::set('vbcherepanov.bonus','CHECKBALANCE','');
	\Bitrix\Main\Config\Option::set('vbcherepanov.bonus','CHECKBALANCE',base64_encode(serialize($Fields)));
	$saveOK=true;
}

$tabControl->Begin();
$tabControl->BeginNextTab();
$qa=\Bitrix\Main\Config\Option::get('vbcherepanov.bonus','CHECKBALANCE',"");
if($qa!=''){
	$qa=$BBCORE->CheckSerialize($qa);
}
?>
	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">

		<?
		echo bitrix_sessid_post();
        $bips = CVbchBonusaccountsTable::getList(array(
            'filter' => array('ACTIVE' => "Y"),
        ))->fetchAll();
        $val = 0;
        if (sizeof($bips) > 0) {

            foreach ($bips as $ids => $ps) {
                if ($ids == 0) $val = $ps['ID'];
                $arrBonusName['REFERENCE'][] = $ps['NAME'];
                $arrBonusName['REFERENCE_ID'][] = $ps['ID'];
            }
        }
        echo Loc::getMessage('ITR_BONUS_CHECKBALANCE');
        if(is_array($qa) && array_key_exists('CHECKBALANCEBONUSACCOUNTSID',$qa)) $chk=$qa['CHECKBALANCEBONUSACCOUNTSID'];
        echo SelectBoxFromArray("CHECKBALANCEBONUSACCOUNTSID", $arrBonusName, $chk).'<br/>';
		if(is_array($qa) && array_key_exists('USER',$qa)) $usr=$qa['USER'];
        echo Loc::getMessage('VBCHBB_CHECK_USER');
		echo FindUserID("USER_ID", $USERID ? $USERID : $usr, "", "post_form");


        if(is_array($qa) && array_key_exists('STEP',$qa) ){?>
			<?$k=$qa['STEP'];?>
		<?}?>
		<input type="submit" name="checkbalance" value="<?=Loc::getMessage('VBCHBB_MOREFUNC_SAVE_OPTION')?>" />
		<?if($saveOK && is_array($qa) && array_key_exists('TYPE',$qa) && $qa['TYPE']=='checkbalance'){?>
			<br/><br/>
			<input type=button value="<?=Loc::getMessage('VBCHBB_START')?>" id="work_start" onclick="set_start(1)" />
			<input type=button value="<?=Loc::getMessage('VBCHBB_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
		<?}?>
		<?
		$tabControl->End();
		?>
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
            try
            {
                eval(result);
                iPercent = CurrentStatus[0];
                strNextRequest = CurrentStatus[1];

                document.getElementById('percent').innerHTML = iPercent + '%';
                document.getElementById('indicator').style.width = iPercent + '%';

                document.getElementById('status').innerHTML = 'Работаю...';

                document.getElementById('rslt').innerHTML = CurrentStatus[2];


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
    <div id="rslt"></div>
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