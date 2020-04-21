<?php
namespace ITRound\Vbchbbonus;

class CheckboxWidget extends Vbchbbwidget{

    const TYPE_STRING = 'string';
    const TYPE_INT = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_STRING_YES = 'Y';
    const TYPE_STRING_NO = 'N';
    const TYPE_INT_YES = 1;
    const TYPE_INT_NO = 0;
    protected static $defaults = array(
        'EDIT_IN_LIST' => true
    );
    protected function getEditHtml()
    {
        $html = '';
        $modeType='string';
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $val=$this->getValue();
        if(is_array($val) && array_key_exists("OPTION",$val)){
            $val=$val['OPTION'];
        }
        switch ($modeType) {
            case static::TYPE_STRING: {
                $checked = $val == self::TYPE_STRING_YES ? 'checked' : '';
                $html = '<input type="hidden" name="' . $this->getEditInputName($ss) . '" value="' . self::TYPE_STRING_NO . '" />';
                $html .= '<input type="checkbox" name="' . $this->getEditInputName($ss) . '" value="' . self::TYPE_STRING_YES . '" ' . $checked . ' />';
                break;
            }
            case static::TYPE_INT:
            case static::TYPE_BOOLEAN: {
                $checked = $val == self::TYPE_INT_YES ? 'checked' : '';
                $html = '<input type="hidden" name="' . $this->getEditInputName($ss) . '" value="' . self::TYPE_INT_NO . '" />';
                $html .= '<input type="checkbox" name="' . $this->getEditInputName($ss) . '" value="' . self::TYPE_INT_YES . '" ' . $checked . ' />';
                break;
            }
        }
        return $html;
    }
}