<?php
namespace ITRound\Vbchbbonus;

class DeliveryWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $tmp=new Vbchbbcore();
        $SITES=$tmp->GetDelivery();
        unset($tmp);
        $html='';
        $val=$this->getValue();
        if(is_array($val) && array_key_exists("OPTION",$val)){
            $val=$val['OPTION'];
        }
        $cbb=new ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['VALUE']=$val;
        $cbb->settings['VARIANT']=$SITES;
        $html.=$cbb->showBasicEditField(true);
        return $html;
    }
}