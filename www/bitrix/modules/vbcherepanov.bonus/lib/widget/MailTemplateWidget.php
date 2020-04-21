<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class MailTemplateWidget extends Vbchbbwidget{
    protected function getEditHtml()
    {
        $html="";
        $tmp=new Vbchbbcore();
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $mailTemp = $tmp->ReturnMailTemplate($ss);
        foreach ($mailTemp as &$MT) {
            $l=$tmp->GetStatusMailTemplate($MT['ID']['OPTION']);
            if($l)
                $MT['STATUS'] =$l;
            else {
                $p=$tmp->InstallMailTemplate($MT['TYPE'],$ss);
                $MT['STATUS']=$tmp->GetStatusMailTemplate($p);
            }
            unset($MT);
        }
        unset($tmp);
        $html.='<table width="80%" class="internal" style="margin: 0 auto">';
        $html.='<tr>';
        $html.='<td>ID</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_ACTIVE').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_NAMES').'</td>';
        $html.='<td>'.Loc::getMessage('VBCHBB_WIDGET_INOUT').'</td>';
        $html.='<td></td>';
        $html.='</tr>';
        foreach ($mailTemp as $idm=>$MT) {
            if($MT['ID']['OPTION']){
                $html.='<tr id="MT'.$MT['STATUS']['ID'].'">';
                $html.='<td>'.$MT['STATUS']['ID'].'</td>';
                $html.='<td style="color:'.($MT['STATUS']['ACTIVE']=='Y' ? 'green' :'red').'!important">'.Loc::getMessage('VBCH_MAILSTATUS_'.$MT['STATUS']['ACTIVE']).'</td>';
                $html.='<td>'.$MT['STATUS']['SUBJECT'].'</td>';
                $html.='<td><input type="checkbox" onchange="ChangeMailStatus(\''.$MT['STATUS']['ID'].'\',\''.$MT['STATUS']['ACTIVE'].'\');"'.($MT['STATUS']['ACTIVE']=='Y' ? 'checked' :'').'></td>';
                $html.='<td><span style="display:none;" id="WAIT'.$MT['STATUS']['ID'].'">wait...</span></td>';
                $html.='</tr>';
            }
        }
        $html.='</table>';
        return $html;
    }
}