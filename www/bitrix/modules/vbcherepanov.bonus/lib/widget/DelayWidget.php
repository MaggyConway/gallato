<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;
class DelayWidget  extends Vbchbbwidget{

    protected static $defaults = array(

    );
    protected function getEditHtml()
    {
        $html='';
        $value=$this->getValue();
        $l=array("C","D","N","M","L");
        $arrPeriod=array();
        foreach($l as $ll){
            $arrPeriod["REFERENCE"][]=Loc::getMessage("VBCHBB_DELAY_".$ll);
            $arrPeriod["REFERENCE_ID"][]=$ll;
        }
        $chk=new Vbchbbonus\CheckboxWidget();
        $chk->settings=array(
            'NAME'=>$this->getEditInputName().'[ACTIVE]',
            'VALUE'=>$value['ACTIVE'],
            'TYPE'=>'string',
        );
        $html.=$chk->showBasicEditField(true);
        unset($chk);
        $html.='&nbsp;';
        $txt=new Vbchbbonus\TextWidget();
        $txt->settings=array(
            'NAME'=>$this->getEditInputName().'[COUNT]',
            'VALUE'=>$value['COUNT'],
            'DEFAULT'=>'',
            'PLACEHOLDER'=>'',
            'SIZE'=>'10',
            'MAXLENGHT'=>'',
        );
        $html.=$txt->showBasicEditField(true);
        $html.='&nbsp;';
        $html.=SelectBoxFromArray($this->getEditInputName().'[PERIOD]', $arrPeriod,$value['PERIOD'], Loc::getMessage("VBCHBB_DELAY_DEFAULT"));
        return $html;
    }
}