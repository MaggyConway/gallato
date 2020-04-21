<?php
namespace ITRound\Vbchbbonus;
use \Bitrix\Main\Localization\Loc;
class SocialSetupWidget extends Vbchbbwidget{

    static protected $defaults = array(
        'SIZE' => 10,
    );
    protected function getEditHtml()
    {
        $html="";
        $value=$this->getValue();
        $ss=$this->getSettings("SITE");
        $ss=isset($ss) ? $this->getSettings("SITE") : null;
        if($value['OPTION']==''){
            $value['OPTION']=array(
                'ACTIVE'=>'',
            );
        }
        $arServices = array(
            "FB"=>array(
                "ICON"=>"facebook",
                "NAME"=>"Facebook",
                "INI"=>array("APPID"),
                "SHABLON"=>"",
                "LINK"=>"http://www.facebook.com/developers/apps.php"
            ),
            "VK"=>array(
                "ICON"=>"vk",
                "NAME"=>"Vkontakte",
                "INI"=>array("APPID"),
                "SHABLON"=>"",
                "LINK"=>"http://vk.com/editapp?act=create"
            ),
            "TW"=>array(
                "ICON"=>"twitter",
                "NAME"=>"Twitter",
                "SHABLON"=>"",
                "INI"=>false,
                "LINK"=>"https://dev.twitter.com/apps/new"
            )
        );
        $html.='<table width="80%" class="internal" style="margin: 0 auto">';
            foreach($arServices as $id=>$service){
                $html.='<tr class="heading">';
                $html.='<td colspan="2" align="center">';
                $html.='<b>'.Loc::getMessage("VBCHBONUS_OPTION_LIKE_SETUP").$service["NAME"].'</b>&nbsp;';
                $html.='<input type="hidden" name="SOCIAL_'.$ss.'['.htmlspecialchars($id).'][ACTIVE]" value="N">';
                $html.='<input type="hidden" name="SOCIAL_'.$ss.'['.htmlspecialchars($id).'][NAME]" value="'.$service['NAME'].'">';
                $html.='<input type="checkbox" name="SOCIAL_'.$ss.'['.htmlspecialchars($id).'][ACTIVE]" id="SOC_SERVICES'.$ss.htmlspecialchars($id).'" value="Y"'.
                                    ($value['OPTION'][$id]["ACTIVE"] == "Y" ? " checked " :'').
                                       'onclick="BX(\''.$id.$ss.'\').style.display=(this.checked? \'\':\'none\');">';
                $html.='</td></tr>';
                $html.='<tr id="'.$id.$ss.'" '.($value['OPTION'][$id]["ACTIVE"] <> "Y" ? ' style="display:none"' : '').'>';
                $html.='<td colspan="2" width="100%">';
                $html.='<table width="80%" class="internal" style="margin: 0 auto">';
                if($service["INI"]){
                    foreach($service['INI'] as $ns){
                        $html.='<tr>';
                        $html.='<td width="50%" class="adm-detail-content-cell-l">'.Loc::getMessage("VBCHBONUS_OPTION_LIKE_".$id."_".$ns).'</td>';
                        $html.='<td width="50%" class="adm-detail-content-cell-r">';
                        $html.='<input type="text" name="SOCIAL_'.$ss.'['.htmlspecialchars($id).']['.$ns.']" value="'.$value['OPTION'][$id][$ns].'" maxlength="255" size="40">';
                        $html.='</td></tr>';
                    }
                }
                $html.='<tr ><td colspan="2" align="center">';
                $html.=BeginNote('width="100%"');
                $html.=str_replace("#LINK#",$service["LINK"],Loc::getMessage("VBCHBONUS_OPTION_LIKE_".$id."_NOTE"));
                $html.=EndNote();
                $html.='</td></tr></table></td></tr>';
            }
        $html.='</table>';
        return $html;
    }
}