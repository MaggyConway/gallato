<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;
class ModulesourceWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $reviewsource=array();
        $reviewsource['REFERENCE']=array(Loc::getMessage("VBCHBB_SUBSC_TYPE_SENDER"),Loc::getMessage("VBCHBB_SUBSC_TYPE_SUBSCRIBE"));
        $reviewsource['REFERENCE_ID']=array("SENDER","SUBSCRIBE");
        $html='';
        $html.=SelectBoxFromArray($this->getEditInputName(), $reviewsource,$this->getValue());
        return $html;
    }
}