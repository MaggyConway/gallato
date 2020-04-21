<?php
namespace ITRound\Vbchbbonus;

class TextWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $html='';
        $size = $this->getSettings('SIZE');
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $value=$this->getValue();
        if(is_array($value)){
            $value=$value['OPTION'];
        }
        $value=($value=='') ? $this->getDefault() : $value;
        if(is_array($value)) $value=current($value);
        $html.='<input type="text" name="' . $this->getEditInputName($ss) . '"value="' . trim($value) . '" size="' . $size . '"
                       placeholder="' . $this->getSettings("PLACEHOLDER") . '" maxlenght="'.$this->getSettings("MAXLENGHT").'"/>';
        return $html;
    }
}