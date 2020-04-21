<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class EventsSetupWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $html="";
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $tmp=new Vbchbbcore();
        $events=$tmp->ReturnEvents();
        $html.='<table width="80%" class="internal" style="margin: 0 auto">';
        $html.='<tr>';
        $html.='<td>ID</td>';
        $html.='<td>'.Loc::getMEssage('VBCHBB_WIDGET_ACTIVE').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_FROMMODULE').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_TOMODULE').$tmp->module_id.'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_INOUT').'</td>';
        $html.='<td></td>';
        $html.='</tr>';
        foreach ($events as $idm=>$EV) {
            $html.='<tr id="'.$ss.'EV'.$idm.'">';
            $html.='<td>'.($idm+1).'</td>';
            $html.='<td style="color:'.($EV['ACTIVE']=='Y' ? 'green' :'red').'!important">'.Loc::getMessage('VBCH_MAILSTATUS_'.$EV['ACTIVE']).'</td>';
            $html.='<td>'.$EV['MODULE_FROM'].'['.$EV['MESSAGE_ID'].']</td>';
            $html.='<td>'.$EV['TO_CLASS'].'['.$EV['TO_METHOD'].']</td>';
            $html.='<td><input type="checkbox" onchange="ChangeEventsStatus(\''.$ss.'\',\''.$idm.'\',\''.$EV['ACTIVE'].'\');" '.($EV['ACTIVE']=='Y' ? 'checked' : '').'/></td>';
            $html.='<td><span style="display:none;" id="WAITEV'.$idm.'">wait...</span></td>';
            $html.='</tr>';
        }
        $html.='</table>';
        return $html;
    }
}