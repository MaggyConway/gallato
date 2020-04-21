<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class BonuscheckWidget  extends Vbchbbwidget{
    protected static $defaults = array(
        'VALUE' => '',
    );
    const TYPE_PAYED = 'pay';
    const TYPE_STATUS = 'status';
    protected function getEditHtml()
    {
        $html = '';
        $val=$this->getValue();
        if(!is_array($val)){
            $val=array("CHECK"=>"pay","STATUS"=>"F");
        }
        $pay = $val['CHECK'] == self::TYPE_PAYED ? 'checked' : '';
        $status = $val['CHECK'] == self::TYPE_STATUS ? 'checked' : '';
        $html.='<input type="radio" name="' . $this->getEditInputName() . '[CHECK]" value="pay" '.$pay.'/>'.Loc::getMessage('VBCH_WIDGET_PAY_ORDER').'<br/>';
        $html.='<input type="radio" name="' . $this->getEditInputName() . '[CHECK]" value="status" '.$status.'/>'.Loc::getMEssage('VBCH_WIDGET_PAY_STATUS').'&nbsp;';
        $status=Vbchbbcore::GetOrderStatus();
        $html.=SelectBoxFromArray($this->getEditInputName()."[STATUS]", $status,$val['STATUS']);
        return $html;
    }


   
}