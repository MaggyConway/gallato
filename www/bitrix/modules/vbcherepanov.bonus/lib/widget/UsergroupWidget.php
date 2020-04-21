<?php
namespace ITRound\Vbchbbonus;
class UsergroupWidget extends Vbchbbwidget{

    protected static $defaults = array(
        "MULTIPLE"=>true,
        "SIZE"=>10,
    );
    protected function getEditHtml()
    {
        $UG=Vbchbbcore::GetUserGroup();
        
        $html='';
        $val=$this->getValue();
        if(is_array($val) && array_key_exists("OPTION",$val)){
            $val=$val['OPTION'];
        }
        $cbb=new ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['VALUE']=$val;
        $cbb->settings['VARIANT']=$UG;
        $html.=$cbb->showBasicEditField(true);
        return $html;
    }
}