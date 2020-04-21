<?php
namespace ITRound\Vbchbbonus;

class SiteWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $BBCORE=new Vbchbbcore();
        $SITES=$BBCORE->GetSiteList();
        $SITES=$SITES['LIST'];
        $html='';
        $value=$this->getValue();
        $cbb=new ComboboxWidget();
        $cbb->settings=$this->settings;
        $cbb->settings['VALUE']=$value;
        $cbb->settings['VARIANT']=$SITES;
        $html.=$cbb->showBasicEditField(true);

       return $html;
    }
}