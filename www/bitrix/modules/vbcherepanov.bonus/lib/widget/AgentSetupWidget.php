<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class AgentSetupWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $html="";
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $tmp=new Vbchbbcore();
        $agents=$tmp->ReturnAgents();
        unset($tmp);
        $html.='<table class="internal" style="width:80%;margin: 0 auto">';
        $html.='<tr>';
        $html.='<td>ID</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_ACTIVE').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_FUNCTION').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_LASTRUN').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_NEXTEXEC').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_PERIODRUN').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_INOUT').'</td>';
        $html.='<td></td>';
        $html.='</tr>';
	foreach ($agents as $idm=>$AG) {
            $html.='<tr id="'.$ss.'AG'.$AG['ID'].'">';
            $html.='<td>'.$AG['ID'].'</td>';
            $html.='<td style="color:'.($AG['ACTIVE']=='Y' ? 'green' :'red').'!important">'.Loc::getMessage('VBCH_MAILSTATUS_'.$AG['ACTIVE']).'</td>';
            $html.='<td>'.$AG['FUNC'].'</td>';
            $html.='<td>'.$AG['LAST_EXEC'].'</td>';
            $html.='<td>';
            $html.='<div class="adm-input-wrap adm-input-wrap-calendar">';
            $html.='<input class="adm-input adm-input-calendar" type="text" id="NEXT_EXEC'.$AG['ID'].'" name="NEXT_EXEC'.$AG['ID'].'" size="13" value="'.$AG['NEXT_EXEC'].'" onchange="ChangeAgentStatus(\''.$ss.'\',\''.$AG['ID'].'\',\''.$AG['ACTIVE'].'\');">';
            $html.='<span class="adm-calendar-icon" onclick="BX.calendar({node:this, field:\'NEXT_EXEC'.$AG['ID'].'\', form: \'\', bTime: true, bHideTime: false});"></span>';
            $html.='</div>';
            $html.='</td>';
            $html.='<td><input type="text" id="AGENT_INTERVAL'.$AG['ID'].'" name="AGENT_INTERVAL" size="10" value="'.$AG['AGENT_INTERVAL'].'" onchange="ChangeAgentStatus(\''.$ss.'\',\''.$AG['ID'].'\',\''.$AG['ACTIVE'].'\');"></td>';
            $html.='<td><input type="checkbox" onchange="ChangeAgentStatus(\''.$ss.'\',\''.$AG['ID'].'\',\''.$AG['ACTIVE'].'\');" '.($AG['ACTIVE']=='Y' ? 'checked' :'').'/></td>';
            $html.='<td><span style="display:none;" id="WAITAG'.$AG['ID'].'">wait...</span></td>';
            $html.='</tr>';
        }        
        $html.='</table>';
        unset($tmp);
        return $html;
    }
}