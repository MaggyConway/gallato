<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class TimelifeWidget  extends Vbchbbwidget{
    protected function getEditHtml()
    {
        $html='';
        $tt=array("A","C","D","W","M","Y");
        $arrPeriod=array();
        foreach($tt as $ll){
            $arrPeriod["REFERENCE"][]=Loc::getMessage("VBCHBB_TIMELIFE_".$ll);
            $arrPeriod["REFERENCE_ID"][]=$ll;
        }
        $value=$this->getValue();
        $html.=SelectBoxFromArray($this->getEditInputName().'[PERIOD]', $arrPeriod,$value['PERIOD'], Loc::getMessage("VBCHBB_TIMELIFE_DEFAULT"),
            "onchange='SelectLive(this.options[selectedIndex].value);'");
        $html.='<input id="live" type="text" value="'.$value['COUNT'].'" name="'.$this->getEditInputName().'[COUNT]" size="15" style="display:'.(in_array($value['PERIOD'],array("A","")) ? "none":"").'"/>';
        $html.='<script type="text/javascript">
            SelectLive=function(id){
                if(id==\'A\' || id==\'\'){
                    BX(\'live\').style.display="none";
                }
                else{
                    BX(\'live\').style.display="";
                }
            };
        </script>';
        return $html;
    }
}