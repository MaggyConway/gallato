<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class UserfieldsWidget  extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $html = '';
        $Fld=array();
        $arrField=array("NONE","EMAIL","NAME","PERSONAL_PHONE","PERSONAL_MOBILE","PERSONAL_WWW","PERSONAL_ICQ",
            "PERSONAL_FAX","PERSONAL_PAGER","PERSONAL_STREET","PERSONAL_CITY","PERSONAL_STATE",
            "PERSONAL_ZIP","PERSONAL_COUNTRY","WORK_COMPANY","PERSONAL_PROFESSION","SECOND_NAME",
            "LAST_NAME","WORK_PHONE","WORK_POSITION","PERSONAL_BIRTHDAY","PERSONAL_GENDER","PERSONAL_PHOTO");
        foreach($arrField as $val){
            $Fld['REFERENCE_ID'][]=$val;
            $Fld['REFERENCE'][]=Loc::getMessage("VBCH_USERFIELD_".$val);
        }
        $rsData = \CUserTypeEntity::GetList(array(""=>""),array("ENTITY_ID"=>"USER"));
        while($arRes = $rsData->Fetch()) {
            $Fld['REFERENCE_ID'][] = $arRes['FIELD_NAME'];//"USER_" . $arRes['ID'];
            $Fld['REFERENCE'][] = $arRes['FIELD_NAME'];
        }
        $mtpl=$this->getSettings('MULTIPLE');
        if($mtpl){
            $val=$this->getValue();
            for($i=0;$i<intval($this->getSettings('COUNT'));$i++){
                $html.=SelectBoxFromArray($this->getEditInputName()."[".$i."]", $Fld,$val[$i][$i]);
            }

        }else{

            $html.=SelectBoxFromArray($this->getEditInputName(), $Fld,$this->getValue());
        }

        return $html;
    }
}