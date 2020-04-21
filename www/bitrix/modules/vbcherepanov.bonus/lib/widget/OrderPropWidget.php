<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;

class OrderPropWidget extends Vbchbbwidget {

    protected function getEditHtml()
    {
        $html='';$PROP=array();
        $db_props = \CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(),
            false,
            false,
            array()
        );
        while($qa=$db_props->Fetch()) {
            if ($qa['TYPE'] == 'TEXT')
            {
                $PROP[]=array('ID'=>$qa['ID'],'NAME'=>$qa['NAME']);
            }
        }

        $value=$this->getValue();
        if(!is_array($value)){
            $l=$value;$value=array();
            $value['ACTIVE']='N';
            $value['ID']=$l;
        }
        $chk=new Vbchbbonus\CheckboxWidget();
        $chk->settings=array(
            'NAME'=>$this->getEditInputName().'[ACTIVE]',
            'VALUE'=>$value['ACTIVE'],
            'TYPE'=>'string',
        );
        $html.=$chk->showBasicEditField(true).'&nbsp;';
        $cbb=new ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['VALUE']=$value['ID'];
        $cbb->settings['NAME']=$this->getEditInputName().'[ID]';
        $cbb->settings['VARIANT']=$PROP;
        $cbb->settings["MULTIPLE"]=true;
        $html.=$cbb->showBasicEditField(true);
        return $html;

    }
}