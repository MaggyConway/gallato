<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class BonusNameWidget extends Vbchbbwidget{

    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
		\Bitrix\Main\Loader::includeModule("currency");
        $html="";
        $arrBonusName=array();
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $arrBonusName['REFERENCE']=array(Loc::getMessage("VBCHBONUS_NAME_CURRENCY"),Loc::getMessage("VBCHBONUS_NAME_NAME"));
        $arrBonusName['REFERENCE_ID']=array("CURRENCY","NAME");
        $value=$this->getValue();
        if($value['OPTION']==''){
            $value['OPTION']=array(
                'SUFFIX'=>'',
                'CURRENCY'=>'',
                'NAME'=>array("1"=>"","2"=>"","3"=>""),
            );
        }
        $html.=SelectBoxFromArray($this->getEditInputName($ss)."[SUFIX]", $arrBonusName,$value['OPTION']['SUFIX'], Loc::getMessage("VBCHBONUS_NAME_DEFAULT"), "onchange='SelectName(this.options[selectedIndex].value,\"".$ss."\");'");
        $html.="&nbsp;";
        $html.='<div id="CURRENCY'.$ss.'" style="display:'.($value['OPTION']['SUFIX']=="CURRENCY" ? 'block' :'none').'" >';
        $html.=\CCurrency::SelectBox($this->getEditInputName($ss)."[CURRENCY]",$value['OPTION']["CURRENCY"],"",true).'</div>';
        $html.='<div id="NAME'.$ss.'" style="display:'.($value['OPTION']['SUFIX']=="NAME" ? 'block' :'none').'" >';
        $html.='<input type="text" size="20" name="'.$this->getEditInputName($ss).'[NAME][1]" value="'.$value['OPTION']["NAME"][1].'" placeholder="'.Loc::getMessage("VBCHBONUS_A").'"/><br/>';
        $html.='<input type="text" size="20" name="'.$this->getEditInputName($ss).'[NAME][2]" value="'.$value['OPTION']["NAME"][2].'" placeholder="'.Loc::getMessage("VBCHBONUS_B").'"/><br/>';
        $html.='<input type="text" size="20" name="'.$this->getEditInputName($ss).'[NAME][3]" value="'.$value['OPTION']["NAME"][3].'" placeholder="'.Loc::getMessage("VBCHBONUS_C").'"/><br/>';
        return $html;
    }
}