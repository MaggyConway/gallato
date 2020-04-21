<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus;

class MaxPayPropWidget extends Vbchbbwidget {

    protected function getEditHtml()
    {
        $html='';
        $value=$this->getValue();
        if(!is_array($value)) $value=['ACTIVE'=>'N','ID'=>[]];
        $type=$this->getSettings('TYPE');
        $chk=new Vbchbbonus\CheckboxWidget();
        $chk->settings=array(
            'NAME'=>$this->getEditInputName().'[ACTIVE]',
            'VALUE'=>$value['ACTIVE'],
            'TYPE'=>'string',
        );
        $html.=$chk->showBasicEditField(true).'&nbsp;';
        $html.=Vbchbbcore::SetFunctionList(str_replace("[]","",$this->getEditInputName()).'[ID]',$value['ID'],true,10,$type);
        return $html;

    }
}