<?
define('NO_AGENT_CHECK', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
use \Bitrix\Main;
use ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
Loc::loadMessages($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/vbcherepanov.bonus/options.php");
Main\Loader::includeModule('vbcherepanov.bonus');
Main\Loader::includeModule('main');
Main\Loader::includeModule('perfom');
global $DB;
if (isset($_POST['AJAX']) && $_POST['AJAX'] == 'Y' && isset($_POST['ACTION'])){
    if($_POST['ACTION']=='CHANGESTATUSMAIL'){
        $Active=$_POST['ACTIVE'];
        $ID=$_POST['ID'];
        $em = new CEventMessage;
        $arFields = Array(
            "ACTIVE"        => ($Active=='Y') ? 'N':'Y',
        );
        if($ID>0)
        {
            $res = $em->Update($ID, $arFields);
        }
        if($res)
        {
            $rs=$em->GetByID($ID)->Fetch();
            ?>
            <tr id="MT<?=$rs['ID']?>">
                <td><?=$rs['ID']?></td>
                <td style="color:<?=($rs['ACTIVE']=='Y' ? 'green' :'red')?>!important"><?=Loc::getMessage('VBCH_MAILSTATUS_'.$rs['ACTIVE'])?></td>
                <td><?=$rs['SUBJECT']?></td>
                <td>
                    <?$l=microtime();$l=explode(" ",$l)?>
                    <input type="checkbox" onchange="ChangeMailStatus('<?=$rs['ID']?>','<?=$rs['ACTIVE']?>');" id="designed_checkbox_<?=$l[0]?>" <?if($rs['ACTIVE']=='Y') echo 'checked';?>  class="adm-designed-checkbox"/>
                    <label class="adm-designed-checkbox-label" for="designed_checkbox_<?=$l[0]?>"></label>
                </td>
                <td><span style="display:none;" id="WAIT<?=$rs['ID']?>">wait...</span></td>
            </tr>
        <?}
    }
    elseif($_POST['ACTION']=='CHANGESTATUSAGENT'){
        $Active=htmlspecialchars($_POST['ACTIVE']);
        $Interval=htmlspecialchars($_POST['INTERVAL']);
        $Next=htmlspecialchars($_POST['NEXT']);
        $ID=htmlspecialchars($_POST['ID']);
        $SS=htmlspecialchars($_POST['SITE']);
		$format = CLang::GetDateFormat("FULL", LANG);
		$format= $DB->DateFormatToPHP($format);
		$Next=date($format,strtotime($Next));
        $arFields = Array(
            "ACTIVE"        => ($Active=='Y') ? 'N':'Y',
   		     "NEXT_EXEC"     =>$Next,
			 "AGENT_INTERVAL"=>$Interval
        );
        if($ID>0)
        {
			$res = CAgent::Update($ID, $arFields);
        }
        if($res)
        {
		$AG=CAgent::GetList(Array("ID" => "DESC"), array("ID"=>$ID))->Fetch();
		$l=microtime();$l=explode(" ",$l);
            ?>
            <tr id="<?=$SS?>AG<?=$AG['ID']?>">
                <td><?=$AG['ID']?></td>
                <td style="color:<?=($AG['ACTIVE']=='Y' ? 'green' :'red')?>!important"><?=Loc::getMessage('VBCH_MAILSTATUS_'.$AG['ACTIVE'])?></td>
                <td><?=$AG['NAME']?></td>
                <td><?=$AG['LAST_EXEC']?></td>
                <td>
                    <div class="adm-input-wrap adm-input-wrap-calendar">
                        <input class="adm-input adm-input-calendar" type="text" id="NEXT_EXEC<?=$AG['ID']?>" name="NEXT_EXEC" size="13" value="<?=$AG['NEXT_EXEC']?>" onchange="ChangeAgentStatus('<?=$SS?>','<?=$AG['ID']?>','<?=$AG['ACTIVE']?>');">
                        <span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:'NEXT_EXEC', form: '', bTime: true, bHideTime: false});"></span>
                    </div>
                </td>
                <td><input type="text" id="AGENT_INTERVAL<?=$AG['ID']?>" name="AGENT_INTERVAL" size="10" value="<? echo $AG['AGENT_INTERVAL']?>" onchange="ChangeAgentStatus('<?=$SS?>','<?=$AG['ID']?>','<?=$AG['ACTIVE']?>');"></td>
                <td>
                    <input type="checkbox" id='designed_checkbox_<?=$l[0]?>'   onchange="ChangeAgentStatus('<?=$SS?>','<?=$AG['ID']?>','<?=$AG['ACTIVE']?>');" <?if($AG['ACTIVE']=='Y') echo 'checked';?> class="adm-designed-checkbox"/>
                    <label class="adm-designed-checkbox-label" for="designed_checkbox_<?=$l[0]?>"></label>
                </td>
                <td><span style="display:none;" id="WAITAG<?=$AG['ID']?>">wait...</span></td>
            </tr>
        <?}
    }
    elseif($_POST['ACTION']=='CHANGESTATUSEVENT'){
        $BBCORE=new Vbchbbonus\Vbchbbcore();
        $Active=htmlspecialchars($_POST['ACTIVE']);
        $ID=htmlspecialchars($_POST['ID']);
        $SS=htmlspecialchars($_POST['SITE']);
        $tmpEvents=$BBCORE->ReturnEvents();
        $event=$tmpEvents[$ID];
        $module=$BBCORE->module_id;
        $sort=$event['SORT'] ? $event['SORT'] :100;
        $eventManager=EventManager::getInstance();
        if($Active=='Y'){
            $eventManager->unregisterEventHandler($event['MODULE_FROM'], $event['MESSAGE_ID'], $module, $event['TO_CLASS'], $event['TO_METHOD']);
            $ok=true;
        }elseif($Active=='N'){
            $eventManager->registerEventHandler($event['MODULE_FROM'],$event['MESSAGE_ID'], $module, $event['TO_CLASS'], $event['TO_METHOD'],$sort);
            $ok=true;
        }
        if($ok)
        {
            $tmpEvents=$BBCORE->ReturnEvents();
            $event=$tmpEvents[$ID];
            $l=microtime();$l=explode(" ",$l);
            ?>
            <tr id="<?=$SS?>EV<?=$ID?>">
                <td><?=($ID+1)?></td>
                <td style="color:<?=($event['ACTIVE']=='Y' ? 'green' :'red')?>!important"><?=Loc::getMessage('VBCH_MAILSTATUS_'.$event['ACTIVE'])?></td>
                <td><?=$event['MODULE_FROM']?>[<?=$event['MESSAGE_ID']?>]</td>
                <td><?=$event['TO_CLASS']?>[<?=$event['TO_METHOD']?>]</td>
                <td><input type="checkbox" onchange="ChangeEventsStatus('<?=$SS?>','<?=$ID?>','<?=$event['ACTIVE']?>');" id='designed_checkbox_<?=$l[0]?>' <?if($event['ACTIVE']=='Y') echo 'checked';?> class="adm-designed-checkbox"/>
                    <label class="adm-designed-checkbox-label" for="designed_checkbox_<?=$l[0]?>"></label>
                </td>
                <td><span style="display:none;" id="WAITEV<?=$ID?>">wait...</span></td>
            </tr>
        <?}
        unset($BBCORE);
    }
}
die();