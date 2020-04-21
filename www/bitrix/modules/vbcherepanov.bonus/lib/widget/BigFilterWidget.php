<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus;
class BigFilterWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        global $APPLICATION;
        $html="";
        $tmp=new Vbchbbcore();
        $html.='<div id="PROFILE_CONDITION">';
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        $obCond = new Vbchbbonus\CITRBBFilterCatalogCondTree();
        $boolCond = $obCond->Init(0, 0, array(
            'FORM_NAME' => $this->getSettings('FORMNAME'),
            'CONT_ID' => 'PROFILE_CONDITION',
            'JS_NAME' => 'JSCatCond', 'PREFIX' => $this->getEditInputName($ss))
        );
        if (!$boolCond){
            if ($ex = $APPLICATION->GetException()){
                echo $ex->GetString() . "<br>";
            }
        }
        $val=$this->getValue();
        if($tmp->CheckArray($val) && array_key_exists("OPTION",$val)){
            $val=$val['OPTION'];
        }
        $obCond->Show($val);
        $html.='</div>';
        return $html;
    }
}