<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class RoundWidget extends Vbchbbwidget {

    protected function getEditHtml()
    {
        $html='';
        $l=array("PHP_ROUND_HALF_UP","PHP_ROUND_HALF_DOWN","PHP_ROUND_HALF_EVEN","PHP_ROUND_HALF_ODD");
        $arrPeriod=array();
        foreach($l as $ll){
            $arrPeriod["REFERENCE"][]=Loc::getMessage("VBCHBB_ROUND_".$ll);
            $arrPeriod["REFERENCE_ID"][]=$ll;
        }
        $html.=SelectBoxFromArray($this->getEditInputName(), $arrPeriod,$this->getValue(), Loc::getMessage("VBCHBB_ROUND_DEFAULT"));
        return $html;

    }
}