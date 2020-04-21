<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class SubscribeWidget  extends Vbchbbwidget{
    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $tmp=new Vbchbbcore();
        $subscribe=$tmp->GetSubscribeList();
        unset($tmp);
        $html='';
        $multiple=$this->getSettings("MULTIPLE");
        $value=$this->getValue();
        $html.='<select name="'.$this->getEditInputName().'" '.($multiple ? "multiple size=".$this->getSettings('SIZE') .'"':'').'>';
        foreach($subscribe as $key=>$sbc){
            $html.='<optgroup label="'.Loc::getMessage("VBCHBB_SUBSC_TYPE_".$key).'">';
            foreach($sbc as $keys=>$vals){
                $id=$key."_".$vals['ID'];
                $selected=false;
                if ($id == $value) {
                    $selected = true;
                }
                if ($multiple && in_array($id, $value)) {
                    $selected = true;
                } elseif ($id === $value) {
                    $selected = true;
                }
                $html.='<option value="'.$id.'" '.($selected ? "selected" : ""). '>['.$vals['ID'].']'.$vals['NAME'].'</option>';
            }
            $html.='</optgroup>';
        }
        $html.='</select>';
        return $html;
    }
}