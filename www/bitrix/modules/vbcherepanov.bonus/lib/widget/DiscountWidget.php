<?php
namespace ITRound\Vbchbbonus;

class DiscountWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $tmp=new Vbchbbcore();
        $DSC=$tmp->GetDiscount();
        unset($tmp);
        $html='';
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $val=$this->getValue();
        if(is_array($val) && array_key_exists("OPTION",$val)){
            $val=$val['OPTION'];
        }
        $chk=new CheckboxWidget();
        $chk->settings=array(
            'NAME'=>str_replace("[]","",$this->getEditInputName($ss)).'[ACTIVE]',
            'VALUE'=>$val['ACTIVE'],
            'SITE'=>'',
            'TYPE'=>'string',
        );
        $html.=$chk->showBasicEditField(true).'&nbsp;';
        $cbb=new ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['NAME']=str_replace("[]","",$this->getEditInputName($ss)).'[DISCOUNT]';
        $cbb->settings['VALUE']=$val['DISCOUNT'];
        $cbb->settings['SITE']='';
        $cbb->settings['VARIANT']=$DSC;
        $html.=$cbb->showBasicEditField(true);
        return $html;
    }
}