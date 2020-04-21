<?php
namespace ITRound\Vbchbbonus;

class SocialsourceWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $tmp=new Vbchbbcore();
        $html='';
        $html.=SelectBoxFromArray($this->getEditInputName(), $tmp->GetSocial(),$this->getValue());
        unset($tmp);
        return $html;
    }
}