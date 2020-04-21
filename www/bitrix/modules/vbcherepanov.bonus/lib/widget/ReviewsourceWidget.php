<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class ReviewsourceWidget extends Vbchbbwidget{

    protected function getEditHtml()
    {
        $tmp=new Vbchbbcore();
        $html = '';
        $reviewsource['REFERENCE']=array(
            Loc::getMessage("VBCHBB_REVIEW_TYPE_IBLOCK"),
            Loc::getMessage("VBCHBB_REVIEW_TYPE_FORUM"),
            Loc::getMessage("VBCHBB_REVIEW_TYPE_BLOG"),
            Loc::getMessage("VBCHBB_REVIEW_TYPE_HL")
        );
        $reviewsource['REFERENCE_ID']=array("IB","FORUM","BLOG","HL");
        $ib=$tmp->GetIblockList();
        $blog=$tmp->GetBlogList();
        $forum=$tmp->GetForumList();
        $hl=$tmp->GetHlList();
        $VALUE=$this->getValue();
        if($VALUE['TYPE']){
            if($VALUE['TYPE']=="FORUM") {$forum="";$ib="none";$blog="none";$hl="none";}
            if($VALUE['TYPE']=="IB") {$forum="none";$ib="";$blog="none";$hl="none";}
            if($VALUE['TYPE']=="BLOG") {$forum="none";$ib="none";$blog="";$hl="none";}
            if($VALUE['TYPE']=="HL") {$forum="none";$ib="none";$blog="none";$hl="";}
        }else{$blog=$forum=$ib=$hl="none";}
        $html.= SelectBoxFromArray($this->getEditInputName()."[TYPE]", $reviewsource,$VALUE["TYPE"], Loc::getMessage("VBCHBB_REVIEW_TYPE_DEFAULT"),
            "onchange=SelectType(this.options[selectedIndex].value);").'&nbsp;';
        $html.=SelectBoxFromArray($this->getEditInputName().'[IB]', $tmp->GetIblockList(),$VALUE['IB'], Loc::getMessage("VBCHBB_REVIEW_TYPE_DEFAULT"),
            "style='display:".$ib."' id='IB'");
        $html.=SelectBoxFromArray($this->getEditInputName()."[FORUM]", $tmp->GetForumList(),$VALUE['FORUM'], Loc::getMessage("VBCHBB_REVIEW_TYPE_DEFAULT"),
            "style='display:".$forum."' id='FORUM'");
        $html.=SelectBoxFromArray($this->getEditInputName()."[BLOG]", $tmp->GetBlogList(),$VALUE['BLOG'], Loc::getMessage("VBCHBB_REVIEW_TYPE_DEFAULT"),
            "style='display:".$blog."' id='BLOG'");
        $html.=SelectBoxFromArray($this->getEditInputName()."[HL]", $tmp->GetHlList(),$VALUE['HL'], Loc::getMessage("VBCHBB_REVIEW_TYPE_DEFAULT"),
            "style='display:".$hl."' id='HL'");
        $html.='<script type="text/javascript">
            SelectType=function(id){
                if(id=="IB"){
                    BX(\'IB\').style.display="";
                    BX(\'FORUM\').style.display="none";
                    BX(\'HL\').style.display="none";
                    BX(\'BLOG\').style.display="none";
                }else if(id=="FORUM"){
                    BX(\'IB\').style.display="none";
                    BX(\'FORUM\').style.display="";
                    BX(\'HL\').style.display="none";
                    BX(\'BLOG\').style.display="none";
                }else if(id=="BLOG"){
                    BX(\'IB\').style.display="none";
                    BX(\'FORUM\').style.display="none";
                    BX(\'HL\').style.display="none";
                    BX(\'BLOG\').style.display="";
                }else if(id=="HL"){
                    BX(\'IB\').style.display="none";
                    BX(\'FORUM\').style.display="none";
                    BX(\'BLOG\').style.display="none";
                    BX(\'HL\').style.display="";

                }else if(id==""){
                    BX(\'IB\').style.display="none";
                    BX(\'FORUM\').style.display="none";
                    BX(\'BLOG\').style.display="none";
                    BX(\'HL\').style.display="none";
                }

            };
        </script>';
        return $html;
    }
}