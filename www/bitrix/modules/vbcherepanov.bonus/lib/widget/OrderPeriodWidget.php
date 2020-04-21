<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;
class OrderPeriodWidget extends Vbchbbwidget {

    protected function getEditHtml()
    {
        $p=array("W","2W","M","3M","6M","Y","ALL");
        foreach($p as $pp){
            $PERIOD[]=array("ID"=>$pp,"NAME"=>Loc::getMessage("VBCHMESS_ORDERPERIOD_".$pp));
        }
        $html='';
        $value=$this->getValue();
        $chk=new Vbchbbonus\CheckboxWidget();
        $chk->settings=array(
            'NAME'=>$this->getEditInputName().'[ACTIVE]',
            'VALUE'=>$value['ACTIVE'],
            'TYPE'=>'string',
        );
        $html.=$chk->showBasicEditField(true).'&nbsp;';
        $txt=new Vbchbbonus\TextWidget();
        $txt->settings=$this->settings;
        $txt->settings['NAME']=$this->getEditInputName().'[SUMMA]';
        $txt->settings['VALUE']=$value['SUMMA'];
        $txt->settings['SIZE']=5;
        $html.=$txt->showBasicEditField(true);
        $html.='&nbsp - &nbsp;';
        $txt=new Vbchbbonus\TextWidget();
        $txt->settings=$this->settings;
        $txt->settings['NAME']=$this->getEditInputName().'[SUMMA1]';
        $txt->settings['VALUE']=$value['SUMMA1'];
        $txt->settings['SIZE']=5;
        $html.=$txt->showBasicEditField(true);
        $html.='&nbsp'.Loc::getMessage("VBCHMESS_ORDERPERIOD_ORDER_STATUS").'&nbsp;';
        $status=Vbchbbcore::GetOrderStatus();
        $html.=SelectBoxFromArray($this->getEditInputName()."[STATUS]", $status,$value['STATUS']);
        $html.='&nbsp'.Loc::getMessage("VBCHMESS_ORDERPERIOD_TTL").'&nbsp;';
        $cbb=new \ITRound\Vbchbbonus\ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['VALUE']=$value['PERIOD'];
        $cbb->settings['NAME']=$this->getEditInputName().'[PERIOD]';
        $cbb->settings['VARIANT']=$PERIOD;
        $html.=$cbb->showBasicEditField(true);
        return $html;

    }
}