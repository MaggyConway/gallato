<?php
namespace VBCherepanov\Bonus;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
class SetOption{
    public static function GetPropValue($POST,$prefix,$i,$field,$suffix){
        $ret="";
        if(array_key_exists($prefix."_n".$i."_".$field.$suffix,$POST))
            $ret=$POST[$prefix."_n".$i."_".$field.$suffix];
        else $ret="";
        return $ret;
    }
    public static function CheckArray($arr){
        return (is_array($arr) && sizeof($arr)>0);
    }
    public static function FilterArray($arr){
        if(self::CheckArray($arr))
            return $arr=array_filter($arr);
    }
    public static function GetWordForm($count,$singleForm,$someForm=FALSE,$manyForm=FALSE){
        if($someForm==FALSE){
            $someForm=$singleForm;
        }
        if($manyForm==FALSE){
            $manyForm=$someForm;
        }
        if(($count % 10 >=5) || ($count % 10 ==0) | in_array($count % 100,array(11,12,13,14))){
            return $count." ".$manyForm;
        }
        elseif($count%10==1){
            return $count." ".$singleForm;
        }else{
            return $count." ".$someForm;
        }
    }
    public static function SetFunctionList($name,$value="",$multiple=false){
        \Bitrix\Main\Loader::includeModule("iblock");
        $property=array();
        $properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y"));
        while ($prop_fields = $properties->GetNext())
        {
            $res=\CIBlock::GetList(array(),array("ID"=>$prop_fields["IBLOCK_ID"]), false)->Fetch();
            $property[$res["NAME"]][$prop_fields["ID"]]=$prop_fields["NAME"];
        }
        unset($properties,$res);
        if($multiple) {
            $name.="[]";
            $mpl='multiple="multiple"';
        }else{
            $mpl="";
        }
        $html="<select name='".$name."'".$mpl.">";
        foreach($property as $ibname=>$prop){
            $html.="<optgroup label='".$ibname."'>";
            foreach($prop as $id=>$name){
                if($multiple && is_array($value)){

                    $check=in_array($id,$value) ? 'selected':'';
                }else{
                    $check=($value==$id) ? 'selected' : '';
                }
                $html.="<option value='".$id."' ".$check.">".$name."</option>";
            }
            $html.="</optgroup>";
        }
        $html.="</select>";
        unset($property,$id,$name);
        echo $html;
    }
    public static function ShowElementPropertyField($name,$property_fields,$values,$bVarsFromFrom=false){
        $index = 0;
        if(!is_array($values))
            $values = array();
        $values=array_filter(array_unique($values));
        echo '<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb'.md5($name).'">';
        foreach($values as $key=>$val)
        {
            $key = $index;
            $index++;
            if(is_array($val) && is_set($val, "VALUE"))
                $val = $val["VALUE"];
            $db_res = \CIBlockElement::GetByID($val);
            $ar_res = $db_res->GetNext();
            echo '<tr><td>'.
                '<input name="'.$name.'['.$key.']" id="'.$name.'['.$key.']" value="'.htmlspecialcharsex($val).'" size="5" type="text">'.
                '<input type="button" value="..." onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$property_fields["LINK_IBLOCK_ID"].'&amp;n='.$name.'&amp;k='.$key.'\', 600, 500);">'.
                '&nbsp;<span id="sp_'.md5($name).'_'.$key.'" >'.$ar_res['NAME'].'</span>'.
                '</td></tr>';

            if($property_fields["MULTIPLE"]!="Y")
            {
                $bVarsFromForm = true;
                break;
            }
        }

        if(!$bVarsFromForm)
        {
            $MULTIPLE_CNT = IntVal($property_fields["MULTIPLE_CNT"]);
            $cnt = ($property_fields["MULTIPLE"]=="Y"? ($MULTIPLE_CNT>0 && $MULTIPLE_CNT<=5 ? $MULTIPLE_CNT : 5) : 1);
            for($i = 0; $i < $cnt; $i++)
            {
                $val = "";
                $key = $index;
                $index++;

                echo '<tr><td>'.
                    '<input name="'.$name.'['.$key.']" id="'.$name.'['.$key.']" value="'.htmlspecialcharsex($val).'" size="5" type="text">'.
                    '<input type="button" value="..." onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$property_fields["LINK_IBLOCK_ID"].'&amp;n='.$name.'&amp;k='.$key.'\', 600, 500);">'.
                    '&nbsp;<span id="sp_'.md5($name).'_'.$key.'"></span>'.
                    '</td></tr>';
            }
        }

        if($property_fields["MULTIPLE"]=="Y")
        {
            echo '<tr><td>'.
                '<input type="button" value="'.GetMessage("IBLOCK_AT_PROP_ADD").'..." onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$property_fields["LINK_IBLOCK_ID"].'&amp;n='.$name.'&amp;m=y&amp;k='.$key.'\', 600, 500);">'.
                '<span id="sp_'.md5($name).'_'.$key.'" ></span>'.
                '</td></tr>';
        }

        echo '</table>';
        echo "<script type=\"text/javascript\">\r\n";
        echo "var MV_".md5($name)." = ".$index.";\r\n";
        echo "function InS".md5($name)."(id, name){ \r\n";
        echo "	oTbl=document.getElementById('tb".md5($name)."');\r\n";
        echo "	oRow=oTbl.insertRow(oTbl.rows.length-1); \r\n";
        echo "	oCell=oRow.insertCell(-1); \r\n";
        echo "	oCell.innerHTML=".
            "'<input name=\"".$name."['+MV_".md5($name)."+']\" value=\"'+id+'\" size=\"5\" type=\"text\">'+\r\n".
            "'<input type=\"button\" value=\"...\" '+\r\n".
            "'onClick=\"jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=".LANGUAGE_ID."&amp;IBLOCK_ID=".$property_fields["LINK_IBLOCK_ID"]."&amp;n=".$name."&amp;k='+MV_".md5($name)."+'\', '+\r\n".
            "' 600, 500);\">'+".
            "'&nbsp;<span id=\"sp_".md5($name)."_'+MV_".md5($name)."+'\" >'+name+'</span>".
            "';";
        echo 'MV_'.md5($name).'++;';
        echo '}';
        echo "\r\n</script>";
    }
    public static function ShowSectionField($name,$property_fields,$values,$bVarsFromForm=false){
        $index = 0;

        if(!is_array($values))
            $values = array();

        $values=array_filter(array_unique($values));
        echo '<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb'.md5($name).'">';
        foreach($values as $key=>$val)
        {
            $key = $index;
            $index++;
            if(is_array($val) && is_set($val, "VALUE"))
                $val = $val["VALUE"];
            $db_res = \CIBlockSection::GetByID($val);
            $ar_res = $db_res->GetNext();
            echo '<tr><td>'.
                '<input name="'.$name.'['.$key.']" id="'.$name.'['.$key.']" value="'.htmlspecialcharsex($val).'" size="5" type="text">'.
                '<input type="button" value="..." onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_section_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$property_fields["LINK_IBLOCK_ID"].'&amp;n='.$name.'&amp;k='.$key.'\', 600, 500);">'.
                '&nbsp;<span id="sp_'.md5($name).'_'.$key.'" >'.$ar_res['NAME'].'</span>'.
                '</td></tr>';

            if($property_fields["MULTIPLE"]!="Y")
            {
                $bVarsFromForm = true;
                break;
            }
        }

        if(!$bVarsFromForm)
        {
            $MULTIPLE_CNT = IntVal($property_fields["MULTIPLE_CNT"]);
            $cnt = ($property_fields["MULTIPLE"]=="Y"? ($MULTIPLE_CNT>0 && $MULTIPLE_CNT<=30 ? $MULTIPLE_CNT : 5) : 1);
            for($i = 0; $i < $cnt; $i++)
            {
                $val = "";
                $key = $index;
                $index++;

                echo '<tr><td>'.
                    '<input name="'.$name.'['.$key.']" id="'.$name.'['.$key.']" value="'.htmlspecialcharsex($val).'" size="5" type="text">'.
                    '<input type="button" value="'.$property_fields["BTN_NAME"].'" onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_section_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$property_fields["LINK_IBLOCK_ID"].'&amp;n='.$name.'&amp;k='.$key.'\', 600, 500);">'.
                    '&nbsp;<span id="sp_'.md5($name).'_'.$key.'"></span>'.
                    '</td></tr>';
            }
        }

        if($property_fields["MULTIPLE"]=="Y")
        {
            echo '<tr><td>'.
                '<input type="button" value="'.$property_fields["BTN_NAME"].'" onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_section_search.php?lang='.LANGUAGE_ID.'&amp;IBLOCK_ID='.$property_fields["LINK_IBLOCK_ID"].'&amp;n='.$name.'&amp;m=y&amp;k='.$key.'\', 600, 500);">'.
                '<span id="sp_'.md5($name).'_'.$key.'" ></span>'.
                '</td></tr>';
        }

        echo '</table>';
        echo "<script type=\"text/javascript\">\r\n";
        echo "var MV_".md5($name)." = ".$index.";\r\n";
        echo "function InS".md5($name)."(id, name){ \r\n";
        echo "	oTbl=document.getElementById('tb".md5($name)."');\r\n";
        echo "	oRow=oTbl.insertRow(oTbl.rows.length-1); \r\n";
        echo "	oCell=oRow.insertCell(-1); \r\n";
        echo "	oCell.innerHTML=".
            "'<input name=\"".$name."['+MV_".md5($name)."+']\" value=\"'+id+'\" size=\"5\" type=\"text\">'+\r\n".
            "'<input type=\"button\" value=\"...\" '+\r\n".
            "'onClick=\"jsUtils.OpenWindow(\'/bitrix/admin/iblock_section_search.php?lang=".LANGUAGE_ID."&amp;IBLOCK_ID=".$property_fields["LINK_IBLOCK_ID"]."&amp;n=".$name."&amp;k='+MV_".md5($name)."+'\', '+\r\n".
            "' 600, 500);\">'+".
            "'&nbsp;<span id=\"sp_".md5($name)."_'+MV_".md5($name)."+'\" >'+name+'</span>".
            "';";
        echo 'MV_'.md5($name).'++;';
        echo '}';
        echo "\r\n</script>";
    }
    public static function AddPropCellTypeSel($intOFPropID,$strPrefix,$arPropInfo,$siteid)
    {
        $ib="";$arIblock=$arForum=$arBlog=array();
        if(\Bitrix\Main\Loader::includeModule('iblock')) {
            $res = \CIBlock::GetList(Array(), Array('SITE_ID' => str_replace("_bx_site_", "", $siteid), 'ACTIVE' => 'Y'), false);
            $arIblock = array();
            while ($ar_res = $res->Fetch()) {
                $arIblock['REFERENCE'][] = "[" . $ar_res["IBLOCK_TYPE_ID"] . "] - " . $ar_res["NAME"];
                $arIblock['REFERENCE_ID'][] = $ar_res["ID"];
            }
            array_unique($arIblock);
        }
        unset($res,$ar_res);
        if(\Bitrix\Main\Loader::includeModule("forum"))
        {
            $arForum=array();
            $arFilter["ACTIVE"] = "Y";
            $arOrder = array("SORT"=>"ASC", "NAME"=>"ASC");
            $db_Forum = \CForumNew::GetList($arOrder, $arFilter);
            while ($ar_Forum = $db_Forum->Fetch())
            {
                $arForum['REFERENCE'][]=$ar_Forum["NAME"];
                $arForum['REFERENCE_ID'][]=$ar_Forum["ID"];
            }
            array_unique($arForum);
        }
        if (\Bitrix\Main\Loader::includeModule("blog"))
        {
            $arBlog=array();
            $SORT = Array("NAME" => "ASC");
            $arFilter = Array("ACTIVE" => "Y","GROUP_SITE_ID" => str_replace("_bx_site_","",$siteid));
            $arSelectedFields = array("ID", "NAME");
            $dbBlogs = \CBlog::GetList($SORT,$arFilter,false,false,$arSelectedFields );
            while ($ar_Blog = $dbBlogs->Fetch())
            {
                $arBlog['REFERENCE'][]=$ar_Blog["NAME"];
                $arBlog['REFERENCE_ID'][]=$ar_Blog["ID"];
            }
            array_unique($arBlog);
        }
        if($arPropInfo["VALUE"]){
            if($arPropInfo["REVIEWTYPE"]=="FORUM") {$forum="";$ib="none";$blog="none";}
            if($arPropInfo["REVIEWTYPE"]=="IB") {$forum="none";$ib="";$blog="none";}
            if($arPropInfo["REVIEWTYPE"]=="BLOG") {$forum="none";$ib="none";$blog="";}
        }else{$blog=$forum=$ib="none";}
        ob_start();
        echo SelectBoxFromArray($strPrefix.$intOFPropID."_REVIEWIB".$siteid, $arIblock,$arPropInfo["REVIEWIB"], GetMessage("VBCHBONUS_OPTION_REVIEWS_IB"),
            "style='display:".$ib."' id='".$strPrefix.$intOFPropID."_REVIEWIB".$siteid."'");
        echo SelectBoxFromArray($strPrefix.$intOFPropID."_REVIEWFORUM".$siteid, $arForum,$arPropInfo["REVIEWFORUM"], GetMessage("VBCHBONUS_OPTION_REVIEW_FORUM"),
            "style='display:".$forum."' id='".$strPrefix.$intOFPropID."_REVIEWFORUM".$siteid."'");
        echo SelectBoxFromArray($strPrefix.$intOFPropID."_REVIEWBLOG".$siteid, $arBlog,$arPropInfo["REVIEWBLOG"], GetMessage("VBCHBONUS_OPTION_REVIEW_BLOG"),
            "style='display:".$blog."' id='".$strPrefix.$intOFPropID."_REVIEWBLOG".$siteid."'");
        $strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;

    }
    public function AddPropCellType($intOFPropID,$strPrefix,$arPropInfo,$siteid)
    {
        $reviewsource['REFERENCE']=array(GetMessage("VBCHBONUS_OPTION_REVIEWS_IB"),GetMessage("VBCHBONUS_OPTION_REVIEW_FORUM"),GetMessage("VBCHBONUS_OPTION_REVIEW_BLOG"));
        $reviewsource['REFERENCE_ID']=array("IB","FORUM","BLOG");
        ob_start();
        echo SelectBoxFromArray($strPrefix.$intOFPropID."_REVIEWTYPE".$siteid, $reviewsource,$arPropInfo["REVIEWTYPE"], GetMessage("VBCHBONUS_OPTION_REVIEWS_TYPE"),
            "onchange=SelectType(this.options[selectedIndex].value,\"".$siteid."\",\"".$intOFPropID."\"); id='".$strPrefix.$intOFPropID."_REVIEWTYPE".$siteid."'");
        $strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;
    }
    public static function AddPropCellID($intOFPropID,$strPrefix,$arPropInfo)
    {
        return (0 < intval($intOFPropID) ? $intOFPropID : '');
    }
    public static function AddPropCellString($intOFPropID,$strName,$strPrefix,$arPropInfo,$strPlaceholder,$siteid)
    {
        ob_start();?>
        <input type="text" size="15" maxlength="255" name="<?echo $strPrefix.$intOFPropID?>_<?=$strName.$siteid?>"
               placeholder="<?=$strPlaceholder?>" id="<?echo $strPrefix.$intOFPropID?>_NAME" value="<?echo $arPropInfo[$strName]?>">
        <?
        $strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;

    }
    public static function AddPropCellPay($intOFPropID,$strPrefix,$arPropInfo,$siteid){
        ob_start();
        echo self::SelectBoxFromArray($strPrefix.$intOFPropID."_PAY".$siteid, self::GetPaymentList(),$arPropInfo["PAY"], "","multiple",false,"frm",true);
        $strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;
    }
    public static function AddPropCellDelivery($intOFPropID,$strPrefix,$arPropInfo,$siteid){
        ob_start();
        echo self::SelectBoxFromArray($strPrefix.$intOFPropID."_DELIVERY".$siteid, self::GetDeliveryList(str_replace("_bx_site_","",$siteid)),$arPropInfo["DELIVERY"],"","multiple",false,"frm",true);
        $strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;
    }
    public static function AddPropCellDelete($intOFPropID,$strPrefix,$arPropInfo,$siteid)
    {
        $strResult = '&nbsp;';
        if ((true == isset($arPropInfo['SHOW_DEL'])) && ('Y' == $arPropInfo['SHOW_DEL']))
            $strResult = '<input type="checkbox" name="'.$strPrefix.$intOFPropID.'_DEL'.$siteid.'" id="'.$strPrefix.$intOFPropID.'_DEL" value="Y">';
        return $strResult;
    }
    public static function AddPropCellPeriod($intOFPropID,$strName,$strPrefix,$arPropInfo,$Items,$siteid)
    {
        ob_start();?>
        <select name="<?=$strPrefix.$intOFPropID."_".$strName.$siteid?>" id="<?=$strPrefix.$intOFPropID."_".$strName?>">
            <?foreach($Items["REFERENCE"] as $id=>$aritem){?>
                <option value="<?=$Items["REFERENCE_ID"][$id]?>" <?=($arPropInfo['PERIOD']==$Items["REFERENCE_ID"][$id] ? 'selected' : '');?>><?=$aritem?></option>
            <?}?>
        </select>
        <?$strResult = ob_get_contents();
        ob_end_clean();
        return $strResult;

    }
    public static function AddPropRowPorog($intOFPropID,$strPrefix,$arPropInfo,$site_id,$Items)
    {
        $strResult = '<tr id="'.$strPrefix.$intOFPropID.$site_id.'">
            <td style="vertical-align:middle;">'.self::AddPropCellID($intOFPropID,$strPrefix,$arPropInfo).'</td>
            <td>'.self::AddPropCellPeriod($intOFPropID,'PERIOD',$strPrefix,$arPropInfo,$Items,$site_id).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'SUMMA',$strPrefix,$arPropInfo,'placeholder',$site_id).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'BONUS_L',$strPrefix,$arPropInfo,'placeholder',$site_id).'</td>
            <td style="text-align: center; vertical-align:middle;">'.self::AddPropCellDelete($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            </tr>';
        return $strResult;
    }
    public static function AddPropRow($intOFPropID,$strPrefix,$arPropInfo,$site_id)
    {
        $strResult = '<tr id="'.$strPrefix.$intOFPropID.$site_id.'">
            <td style="vertical-align:middle;">'.self::AddPropCellID($intOFPropID,$strPrefix,$arPropInfo).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'ORDER_OT',$strPrefix,$arPropInfo,'placeholder',$site_id).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'ORDER_DO',$strPrefix,$arPropInfo,'placeholder',$site_id).'</td>
            <td>'.self::AddPropCellPay($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            <td>'.self::AddPropCellDelivery($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'ORDER_BONUS',$strPrefix,$arPropInfo,'placeholder',$site_id).'</td>
            <td style="text-align: center; vertical-align:middle;">'.self::AddPropCellDelete($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            </tr>';
        return $strResult;
    }
    public static function AddPropRow1($intOFPropID,$strPrefix,$arPropInfo,$site_id)
    {
        $strResult = '<tr id="'.$strPrefix.$intOFPropID.$site_id.'">
            <td style="vertical-align:middle;">'.self::AddPropCellID($intOFPropID,$strPrefix,$arPropInfo).'</td>
            <td>'.self::AddPropCellType($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            <td>'.self::AddPropCellTypeSel($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'REVIEW_BONUS',$strPrefix,$arPropInfo,GetMessage("VBCHBONUS_OPTION_ORDER_BONUS"),$site_id).'</td>
            <td>'.self::AddPropCellString($intOFPropID,'REVIEW_INDAY',$strPrefix,$arPropInfo,GetMessage("VBCH_OPTION_REVIEW_INDAY"),$site_id).'</td>
            <td style="text-align: center; vertical-align:middle;">'.self::AddPropCellDelete($intOFPropID,$strPrefix,$arPropInfo,$site_id).'</td>
            </tr>';
        return $strResult;
    }
    public static function GetPaymentList() {
        $result=array();
        if(\Bitrix\Main\Loader::includeModule('sale')){
            $res = \CSalePaySystem::GetList(array("NAME"=>"ASC"));
            $result["REFERENCE"][] ="";
            $result["REFERENCE_ID"][]="";
            while ($r = $res->Fetch()) {
                $result["REFERENCE"][] = $r['NAME'];
                $result["REFERENCE_ID"][]=$r['ID'];
            }}
        return $result;
    }
    public static function GetUserGroupList(){
        $result=array();
        if(\Bitrix\Main\Loader::includeModule('main')){
            $dbResultList = \CGroup::GetList($by = "id", $order = "asc",array("ACTIVE"=>"Y"));
            $result["REFERENCE"][] ="";
            $result["REFERENCE_ID"][]="";
            while ($r =$dbResultList->Fetch()) {
                $result["REFERENCE"][] = $r['NAME'];
                $result["REFERENCE_ID"][]=$r['ID'];
            }
        }
        return $result;
    }
    public static function GetSalePersonList($site){
        $result=array();
        if(\Bitrix\Main\Loader::includeModule('sale')){
            $dbResultList = \CSalePersonType::GetList(array("ID"=>"ASC"), Array("LID"=>$site));
            $result["REFERENCE"][] ="";
            $result["REFERENCE_ID"][]="";
            while ($r =$dbResultList->Fetch()) {
                $result["REFERENCE"][] = $r['NAME'];
                $result["REFERENCE_ID"][]=$r['ID'];
            }
        }
        return $result;
    }
    public static function GetDeliveryList($lid) {
        $result=array();
        if(\Bitrix\Main\Loader::includeModule('sale')){
            $result["REFERENCE"][] ="";
            $result["REFERENCE_ID"][]="";

            $dbResultList = \CSaleDelivery::GetList(
                array(
                    "SORT" => "ASC",
                    "NAME" => "ASC"
                ),
                array(
                    "LID" => $lid,
                    "ACTIVE" => "Y",
                ),
                false,
                false,
                array("ID","NAME")
            );

            while ($r = $dbResultList->Fetch())
            {
                $result["REFERENCE"][] = $r['NAME'];
                $result["REFERENCE_ID"][]=$r['ID'];
            }
            $res=\CSaleDeliveryHandler::GetList(array(), array("LID" => $lid,"ACTIVE" => "Y"));
            while($dev=$res->Fetch()) {
                $result["REFERENCE"][] = $dev['NAME'];
                $result["REFERENCE_ID"][]=$dev['SID'];
            }
        }
        return $result;
    }
    public static function VBSelectBoxFromArray($strBoxName,$db_array,$strSelectedVal = "", $strDetText = "", $field1="class='typeselect'",$go = false,$form="form1"){
        if($go)
        {
            $strReturnBox = "<script type=\"text/javascript\">\n".
                "function ".$strBoxName."LinkUp()\n".
                "{var number = document.".$form.".".$strBoxName.".selectedIndex;\n".
                "if(document.".$form.".".$strBoxName.".options[number].value!=\"0\"){ \n".
                "document.".$form.".".$strBoxName."_SELECTED.value=\"yes\";\n".
                "document.".$form.".submit();\n".
                "}}\n".
                "</script>\n";
            $strReturnBox .= '<input type="hidden" name="'.$strBoxName.'_SELECTED" id="'.$strBoxName.'_SELECTED" value="">';
            $strReturnBox .= '<select '.$field1.' name="'.$strBoxName.'" id="'.$strBoxName.'" onchange="'.$strBoxName.'LinkUp()" class="typeselect">';
        }
        else
        {
            $strReturnBox = '<select '.$field1.' name="'.$strBoxName.'" id="'.$strBoxName.'">';
        }

        if(isset($db_array["reference"]) && is_array($db_array["reference"]))
            $ref = $db_array["reference"];
        elseif(isset($db_array["REFERENCE"]) && is_array($db_array["REFERENCE"]))
            $ref = $db_array["REFERENCE"];
        else
            $ref = array();

        if(isset($db_array["reference_id"]) && is_array($db_array["reference_id"]))
            $ref_id = $db_array["reference_id"];
        elseif(isset($db_array["REFERENCE_ID"]) && is_array($db_array["REFERENCE_ID"]))
            $ref_id = $db_array["REFERENCE_ID"];
        else
            $ref_id = array();

        if($strDetText <> '')
            $strReturnBox .= '<option value="">'.$strDetText.'</option>';

        foreach($ref as $i => $val)
        {
            $strReturnBox .= '<option';
            if(!is_array($strSelectedVal)){
                if(strcasecmp($ref_id[$i], $strSelectedVal) == 0)
                    $strReturnBox .= ' selected';
            }elseif(is_array($strSelectedVal)){
                if(in_array($ref_id[$i],$strSelectedVal))
                    $strReturnBox .= ' selected';
            }
            $strReturnBox .= ' value="'.htmlspecialcharsbx($ref_id[$i]).'">'.htmlspecialcharsbx($val).'</option>';
        }
        return $strReturnBox.'</select>';
    }
    public function SelectBoxFromArray($strBoxName, $db_array, $strSelectedVal = "", $strDetText = "",
                                        $field1="class='typeselect'",  $go = false,$form="form1",$multi=false)
    {
        $fieldname=($multi ? $strBoxName."[]" : $strBoxName);
        if($multi){
            if(!is_array($strSelectedVal))
                $strSelectedVal=(array)$strSelectedVal;
        }
        if($go)
        {
            $strReturnBox = "<script type=\"text/javascript\">\n".
                "function ".$strBoxName."LinkUp()\n".
                "{var number = document.".$form.".".$strBoxName.".selectedIndex;\n".
                "if(document.".$form.".".$strBoxName.".options[number].value!=\"0\"){ \n".
                "document.".$form.".".$strBoxName."_SELECTED.value=\"yes\";\n".
                "document.".$form.".submit();\n".
                "}}\n".
                "</script>\n";
            $strReturnBox .= '<input type="hidden" name="'.$strBoxName.'_SELECTED" id="'.$strBoxName.'_SELECTED" value="">';
            $strReturnBox .= '<select '.$field1.' name="'.$fieldname.'" id="'.$strBoxName.'" onchange="'.$strBoxName.'LinkUp()" class="typeselect">';
        }
        else
        {
            $strReturnBox = '<select '.$field1.' name="'.$fieldname.'" id="'.$strBoxName.'">';
        }

        if(isset($db_array["reference"]) && is_array($db_array["reference"]))
            $ref = $db_array["reference"];
        elseif(isset($db_array["REFERENCE"]) && is_array($db_array["REFERENCE"]))
            $ref = $db_array["REFERENCE"];
        else
            $ref = array();

        if(isset($db_array["reference_id"]) && is_array($db_array["reference_id"]))
            $ref_id = $db_array["reference_id"];
        elseif(isset($db_array["REFERENCE_ID"]) && is_array($db_array["REFERENCE_ID"]))
            $ref_id = $db_array["REFERENCE_ID"];
        else
            $ref_id = array();

        if($strDetText <> '')
            $strReturnBox .= '<option value="">'.$strDetText.'</option>';

        foreach($ref as $i => $val)
        {
            if($multi){
                $strReturnBox .= '<option';
                if (in_array($ref_id[$i], $strSelectedVal))
                    $strReturnBox .= ' selected';
            }else {
                $strReturnBox .= '<option';
                if (strcasecmp($ref_id[$i], $strSelectedVal) == 0)
                    $strReturnBox .= ' selected';
            }
            $strReturnBox .= ' value="'.htmlspecialcharsbx($ref_id[$i]).'">'.htmlspecialcharsbx($val).'</option>';
        }
        return $strReturnBox.'</select>';
    }
}