<?php
namespace Vbcherepanov\Bonus\widget;

use ITRound\Vbchbbonus\Vbchbbcore;
use ITRound\Vbchbbonus\Vbchbbwidget;

class orderstatus extends Vbchbbwidget
{

    protected function getEditHtml()
    {
        $html = '';
        $val = $this->getValue();
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $status = Vbchbbcore::GetOrderStatus();
        $html .= SelectBoxFromArray($this->getEditInputName($ss), $status, $val['OPTION']);
        return $html;
    }
}