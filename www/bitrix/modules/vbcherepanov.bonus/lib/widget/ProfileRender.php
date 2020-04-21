<?php
namespace ITRound\Vbchbbonus;

class ProfileRender {
    public static function Run($Fields,$Value){

        echo self::Header();
        $lTabs=array();
        foreach($Fields as $TAB=>$TAB_E) {
            $arr=Vbchbbcore::array_filter_recursive($TAB_E['ELEM']);
            if(Vbchbbcore::CheckArray($arr))
                $lTabs[] = array("DIV" => "setup_" . $TAB, "TAB" => $TAB_E['TITLE'], "TITLE" => $TAB_E['TITLE']);
        }
        $InnerTabControl = new \CAdminViewTabControl("InnerTabControl", $lTabs);
        $InnerTabControl->Begin();
        foreach($Fields as $TABS){
            $arr=Vbchbbcore::array_filter_recursive($TABS['ELEM']);
            if(Vbchbbcore::CheckArray($arr)) {
                $InnerTabControl->BeginNextTab();
                ?>
                <table cellpadding="0" width="100%" cellspacing="0" border="0" class="edit-table">
                    <?
                    self::Rend($TABS['ELEM'], $Value);
                    ?>
                </table>
                <?
            }
        }
        $InnerTabControl->End();
        echo self::Footer();
    }
    static function Rend($arr=array(),$Value){
        $arr=Vbchbbcore::array_filter_recursive($arr);
        if(Vbchbbcore::CheckArray($arr)){
            foreach($arr as $Flds=>$Fld){
                if(array_key_exists("ELEMENT",$Fld)){
                    self::Rend($Fld['ELEMENT'],$Value);
                }else{
                   $element=$Fld['WIDGET'];
                   $set=$Fld; unset($set['WIDGET']);
                   $set['VALUE']=$Value[$Flds];
                   $set['NAME']=$Flds;
                   $element->settings=$set;
                   unset($set);
                   call_user_func_array(array($element,"showBasicEditField"),array());
                }
            }
        }
    }
    static function Header(){
        return '<tr><td colspan="2">';
    }
    static function Footer(){
        return '</td></tr>';
    }
}
