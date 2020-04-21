<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;
class PorogWidget  extends Vbchbbwidget{

    protected static $defaults = array(

    );
    protected function getEditHtml()
    {
        $html='';
        $value=$this->getValue();
        $l=array("C","D","N","M","L","ALL");
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
            'PLACEHOLDER'=>Loc::getMessage('VBCHBB_PRICE_OT'),
            'SIZE'=>'10',
            'MAXLENGHT'=>'',
        );
        $html.=$txt->showBasicEditField(true);
        $html.='&nbsp;-:&nbsp;';
        $txt->settings=array(
            'NAME'=>$this->getEditInputName().'[COUNT1]',
            'VALUE'=>$value['COUNT1'],
            'DEFAULT'=>'',
            'PLACEHOLDER'=>Loc::getMessage('VBCHBB_PRICE_DO'),
            'SIZE'=>'10',
            'MAXLENGHT'=>'',
        );
        $html.=$txt->showBasicEditField(true);
        $html.='&nbsp'.Loc::getMessage("VBCHMESS_ORDERPERIOD_ORDER_STATUS").'&nbsp;';
        $status=Vbchbbcore::GetOrderStatus();
        $html.=SelectBoxFromArray($this->getEditInputName()."[STATUS]", $status,$value['STATUS']);
        $html.='<br/>&nbsp;'.Loc::getMessage('VBCHBB_PERIODS').'&nbsp;';
        $html.=SelectBoxFromArray($this->getEditInputName().'[PERIOD]', $arrPeriod,$value['PERIOD'], Loc::getMessage("VBCHBB_DELAY_DEFAULT"));
        $html.='&nbsp;'.Loc::getMessage('VBCHBB_POROG_BONUS').'&nbsp;';
        $txt->settings=array(
            'NAME'=>$this->getEditInputName().'[BONUS]',
            'VALUE'=>$value['BONUS'],
            'DEFAULT'=>'',
            'PLACEHOLDER'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
            'SIZE'=>'10',
            'MAXLENGHT'=>'',
        );
        $html.=$txt->showBasicEditField(true);
        return $html;
    }
}