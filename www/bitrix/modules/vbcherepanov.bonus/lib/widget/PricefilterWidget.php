<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;
class PricefilterWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $html='';
        $txt=new Vbchbbonus\TextWidget();
        $VALUE=$this->getValue();
        $txt->settings=$this->settings;
        $l=$this->settings;
        $txt->settings['NAME']=$txt->settings['NAME'].'[OT]';
        $txt->settings['VALUE']=$VALUE['OT'][0];
        $html.=$txt->showBasicEditField(true);
        $html.="...";
        $txt->settings=$l;
        $txt->settings['NAME']=$txt->settings['NAME'].'[DO]';
        $txt->settings['VALUE']=$VALUE['DO'][0];
        $html.=$txt->showBasicEditField(true);
        return $html;
    }
}