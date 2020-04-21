<?php
namespace ITRound\Vbchbbonus;
use ITRound\Vbchbbonus,\Bitrix\Main\Localization\Loc;
class ReferalWidget  extends Vbchbbwidget{

    protected static $defaults = array(

    );
    protected function getEditHtml()
    {
        $html='';
        $tmp=new Vbchbbcore();
        $refOption=$tmp->GetOptions($tmp->GetSiteID(),'REFACTIVE');
        $refLINE=$tmp->GetOptions($tmp->GetSiteID(),'REFLEVELCOUNT');

        $l=array("ORDERSUMM","BONUS","FIXPRICE");
        $arrPeriod=array();
        foreach($l as $ll){
            $arrPeriod["REFERENCE"][]=Loc::getMessage("VBCHBB_REF_".$ll);
            $arrPeriod["REFERENCE_ID"][]=$ll;
        }

        if($refOption['OPTION']=='Y'){
            $value=$this->getValue();
            if(!is_array($value)){
                $l=$value;$value=array();
                $value['ACTIVE']='N';
                $value['COUNT']=$l;
                $value['TYPE']='';
            }
            for($i=0;$i<$refLINE['OPTION'];$i++){
                $html.=Loc::getMessage('VBCHBB_REF_LINES')."[".($i+1)."] ";
                $chk=new Vbchbbonus\CheckboxWidget();
                $chk->settings=array(
                    'NAME'=>$this->getEditInputName().'[ACTIVE]['.$i.']',
                    'VALUE'=>$value['ACTIVE'][$i],
                    'TYPE'=>'string',
                );
                $html.=$chk->showBasicEditField(true);
                unset($chk);
                $html.='&nbsp;';
                $txt=new Vbchbbonus\TextWidget();
                $txt->settings=array(
                    'NAME'=>$this->getEditInputName().'[COUNT]['.$i.']',
                    'VALUE'=>$value['COUNT'][$i],
                    'DEFAULT'=>'',
                    'PLACEHOLDER'=>'',
                    'SIZE'=>'10',
                    'MAXLENGHT'=>'',
                );

                $html.=$txt->showBasicEditField(true);
                $html.=SelectBoxFromArray($this->getEditInputName().'[TYPE]['.$i.']', $arrPeriod,$value['TYPE'][$i]);
                $html.="<br/>";
            }

        }
        return $html;
    }
}