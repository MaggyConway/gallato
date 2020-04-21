<?
if (isset($_REQUEST['work_start']))
{
	define("NO_AGENT_STATISTIC", true);
	define("NO_KEEP_STATISTIC", true);
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use ITRound\Vbchbbonus;
IncludeModuleLangFile(__FILE__);
\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
if($REQUEST_METHOD=="POST" && strlen($importuser)>0  && check_bitrix_sessid())
{
	if($_FILES['importfile']['tmp_name'] && $_FILES['importfile']['error']==0) {
		$inputFileName = $_FILES['importfile']['tmp_name'];
		$uploaddir = \Bitrix\Main\Loader::getDocumentRoot().'/upload/' . $module_id . '/';
		if (!is_dir($uploaddir))
			mkdir($uploaddir);
		$uploadfile = $uploaddir . basename($_FILES['importfile']['name']);
		if (move_uploaded_file($inputFileName, $uploadfile))
			$inputFileName = $uploadfile;
		$settings['FILEPATH'] = $inputFileName;
		$BBCORE->SaveOption($BBCORE->SITE_ID,'BONUSCARDFILE',$settings['FILEPATH'],$module_id);
	}
}
if($_REQUEST['work_start'] && check_bitrix_sessid())
{
	$pp=$BBCORE->GetOptions($BBCORE->SITE_ID,'BONUSCARDFILE');
	$pp=$pp['OPTION'];
	$lastID = intval($_REQUEST["lastid"]);
	if($lastID==0) $lastID=1;
	$stream = new \ITRound\Vbchbbonus\FileReader($pp);
	$count=$stream->ReturnCount($pp);
	if($lastID<=$count){
		$stream->SetOffset($lastID);
		$result = $stream->Read(1);

		if(sizeof($result)>0){
			foreach($result as $res){
				$stroka=explode(";",$res);
				$arFields=array(
					'TIMESTAMP_X'=>new \Bitrix\Main\Type\DateTime(),
					'LID'=>$stroka[3],
					'ACTIVE'=>trim($stroka[4]),
					'USERID'=>$stroka[1],
					'DEFAULTBONUS'=> $stroka[5] ? $stroka[5] : 0,
					'BONUSACCOUNTS'=> $stroka[6]?$stroka[6] : 1,
					'NUM'=>$stroka[2],
				);
				Application::getConnection()->startTransaction();
				if ($ID <= 0)
				{
					$kk=\ITRound\Vbchbbonus\BonusCardTable::add($arFields);
					if($kk->isSuccess()){
						Application::getConnection()->commitTransaction();
					}
					else{
						Application::getConnection()->rollbackTransaction();
					}
				}
			}
		}

		$lastID++;
		$leftBorderCnt=$lastID;
		$allCnt=$count;
		$p = round(100*$leftBorderCnt/$allCnt, 2);
	}
	unset($stream,$count);
	echo 'CurrentStatus = Array('.$p.',"'.($p < 100 ? '&lastid='.$lastID : '').'","'.Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_CURRENTID').$lastID.'");';

	die();
}

$clean_test_table = '<table id="result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">'.
	'<tr class="heading">'.
	'<td>'.Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_CURRENT').'</td>'.
	'<td width="1%">&nbsp;</td>'.
	'</tr>'.
	'</table>';

$aTabs = array(array("DIV" => "edit1", "TAB" => \Bitrix\Main\Localization\Loc::getMessage('VBCHBB_BONUSCARD_IMPORT')));
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$APPLICATION->SetTitle( \Bitrix\Main\Localization\Loc::getMessage('VBCHBB_BONUSCARD_IMPORT'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
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
                document.getElementById('result').innerHTML = '<?=$clean_test_table?>';
                document.getElementById('status').innerHTML = '<?=Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_WORK')?>';

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

                iPercent = CurrentStatus[0];
                strNextRequest = CurrentStatus[1];
                strCurrentAction = CurrentStatus[2];

                document.getElementById('percent').innerHTML = iPercent + '%';
                document.getElementById('indicator').style.width = iPercent + '%';

                document.getElementById('status').innerHTML = 'Работаю...';

                if (strCurrentAction != 'null')
                {
                    oTable = document.getElementById('result_table');
                    oRow = oTable.insertRow(-1);
                    oCell = oRow.insertCell(-1);
                    oCell.innerHTML = strCurrentAction;
                    oCell = oRow.insertCell(-1);
                    oCell.innerHTML = '';
                }

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
                alert('<?=Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_ALRT')?>');
            }
        }

	</script>

	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">
		<?
		echo bitrix_sessid_post();

		$tabControl->Begin();
		$tabControl->BeginNextTab();
		?>
		<tr>
			<td colspan="2">
				<?echo BeginNote('width="100%"');?>
				<?=Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_HELP')?>
				<? echo EndNote();?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="file" name="importfile"/>
				<input type="submit" name="importuser" value="<?=Loc::getMessage("VBCHBONUSCARDIMPORT_OPT_READFILE")?>" title="<?=Loc::getMessage("VBCHBONUSCARDIMPORT_OPT_READFILE")?>" class="adm-btn-save"><br/><br/>
				<b><?=$settings['FILEPATH'] ?></b>

			</td>
		</tr>

		<tr>
			<td colspan="2">


				<input type=button value="<?=Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_START')?>" id="work_start" onclick="set_start(1)" />
				<input type=button value="<?=Loc::getMessage('VBCHBB_BONUSCARD_IMPORT_STOP')?>" disabled id="work_stop" onclick="bSubmit=false;set_start(0)" />
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
				<div id="result" style="padding-top:10px"></div>

			</td>
		</tr>
		<?
		$tabControl->End();
		?>
	</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>