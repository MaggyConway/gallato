<?php
namespace ITRound\Vbchbbonus;
class DatetimeWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $V=array();
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;

        $html='';
        $value=$this->getValue();
        $html.=\CAdminCalendar::CalendarDate($this->getEditInputName($ss), $value, 19, true);
        return $html;
    }
}