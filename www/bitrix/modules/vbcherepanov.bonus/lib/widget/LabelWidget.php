<?php
namespace ITRound\Vbchbbonus;

class LabelWidget extends Vbchbbwidget {
    protected function getEditHtml()
    {
        $html = '';
        $html.=$this->getValue();
        return $html;
    }
}