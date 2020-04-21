<?php
namespace ITRound\Vbchbbonus;

class ComboboxWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $VARIANT=$this->getSettings('VARIANT');
        $html='';
        $multiple=$this->getSettings("MULTIPLE");
        $value=$this->getValue();
        if($multiple && !is_array($value)){
            $value=(array)$value;
        }
		if(is_array($value) && array_key_exists("OPTION",$value)){
			$value=$value['OPTION'];
		}
        $html.='<select name="'.$this->getEditInputName($this->getSettings('SITE')).'" '.($multiple ? "multiple size=".$this->getSettings('SIZE') .'"':'').'>';
        
        foreach($VARIANT as $sid){
            $selected=false;
            if ($sid['ID'] == $value) {
                $selected = true;
            }
            if ($multiple && in_array($sid['ID'], $value)) {
                $selected = true;
            } elseif ($sid['ID'] === $value) {
                $selected = true;
            }
            $html.='<option value="'.$sid['ID'].'" '.($selected ? "selected" : ""). '>['.$sid['ID'].'] '.$sid['NAME'].'</option>';
        }
        $html.='</select>';
        return $html;
    }
}