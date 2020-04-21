<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus;
class IbpropertyWidget extends Vbchbbwidget{
    protected static $defaults = array(
        'MULTIPLE'=>true,
        'SIZE' => 10
    );
    protected function getEditHtml()
    {
        $html='';
        $value=$this->getValue();
        $type=$this->getSettings('TYPE');
        $chk=new Vbchbbonus\CheckboxWidget();
        $chk->settings=array(
            'NAME'=>str_replace("[]","",$this->getEditInputName()).'[ACTIVE]',
            'VALUE'=>$value['ACTIVE'],
            'TYPE'=>'string',
        );
        $html.=$chk->showBasicEditField(true).'&nbsp;';
        unset($chk);
        $html.=Vbchbbcore::SetFunctionList(str_replace("[]","",$this->getEditInputName()).'[ID]',$value['ID'],true,10,$type);
        return $html;
    }
}