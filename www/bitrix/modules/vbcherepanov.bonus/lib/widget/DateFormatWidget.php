<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Loader;
class DateFormatWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $V=array();
        Loader::includeModule('iblock');
        $l=\CIBlockParameters::GetDateFormat("","");
        foreach($l['VALUES'] as $key=>$val){
            $V[]=array('ID'=>$key,'NAME'=>$val);
        }
        $html='';
        $value=$this->getValue();
        $cbb=new ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['VALUE']=$value['OPTION'];
        $cbb->settings['VARIANT']=$V;
        $html.=$cbb->showBasicEditField(true);

        return $html;
    }
}