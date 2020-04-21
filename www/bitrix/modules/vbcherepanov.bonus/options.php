<?php
use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Config\Option,
    \Bitrix\Main\Loader,
    \Bitrix\Main\Page\Asset,
    \ITRound\Vbchbbonus;
global $APPLICATION;
Loader::includeModule("vbcherepanov.bonus");
$BBCORE=new Vbchbbonus\Vbchbbcore();
$module_id=$BBCORE->module_id;
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($POST_RIGHT>="R") :
    Loc::loadMessages(__FILE__);
    Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
    CJSCore::Init(array('jquery','ajax','bx'));
    Asset::getInstance()->addJs('/bitrix/js/vbcherepanov.bonus/vbchbonus.js');
    $SITES=$BBCORE->GetSiteList();
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => Loc::getMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET")),
        array("DIV" => "edit2", "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    $SAVE=$BBCORE->ReturnParams();
    if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && $POST_RIGHT=="W" && check_bitrix_sessid()){
        if(strlen($RestoreDefaults)>0)
        {
            Option::delete($module_id);
        }
        else {
            $Update = $Update . $Apply;
            Option::set($module_id, "use_on_sites", serialize($_POST["use_on_sites"]));
            foreach($SITES['SL'] as $site):
                foreach($SAVE as $s):
                    if(is_null(${$s."_".$site})) ${$s."_".$site}='N';
                    if(isset(${$s."_".$site}))
                        $BBCORE->SaveOption($site,$s,${$s."_".$site});
                endforeach;
            endforeach;
        }
        ob_start();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
        ob_end_clean();
        if(strlen($_REQUEST["back_url_settings"]) > 0)
        {
            if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
                LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
            else
                LocalRedirect($_REQUEST["back_url_settings"]);
        }
        else
        {
            LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
        }
    }
    $tabControl->Begin();?>
    <form method="post" id="frmoptionbonus" name="frmoptionbonus" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
        <?$tabControl->BeginNextTab();?>
        <tr><td colspan="2">
                <?
                $STNS=$BBCORE->GetModuleOptions();
                foreach($SITES['S'] as $arSite)
                    $aSiteTabs[] = array("DIV" => "opt_site_".$arSite["LID"], "TAB" => '['.$arSite["LID"].'] '.htmlspecialchars($arSite["NAME"]),
                        'TITLE' => Loc::getMessage("VBCHBONUS_SITE_TITLE").' ['.$arSite["LID"].'] '.htmlspecialchars($arSite["NAME"]));
                $siteTabControl = new CAdminViewTabControl("siteTabControl", $aSiteTabs);
                $siteTabControl->Begin();
                $arUseOnSites = unserialize(Option::get($module_id, "use_on_sites", ""));
                foreach($SITES['SL'] as $site):
                    $suffix = ($site <> ''? '_bx_site_'.$site:'');
                    $siteTabControl->BeginNextTab();
                    if($site <> ''):?>
                        <table cellpadding="0" width="100%" cellspacing="0" border="0" class="edit-table">
                            <tr>
                                <td width="50%" class="field-name"><label for="use_on_sites<?=$suffix?>"><?echo Loc::getMessage("VBCHBONUS_SITE_APPLY")?></td>
                                <td width="50%" style="padding-left:7px;">
                                    <input type="hidden" name="use_on_sites[<?=htmlspecialchars($site)?>]" value="N">
                                    <input type="checkbox" name="use_on_sites[<?=htmlspecialchars($site)?>]" value="Y"<?if($arUseOnSites[$site] == "Y") echo ' checked'?> id="use_on_sites<?=$suffix?>" onclick="BX('site_settings<?=$suffix?>').style.display=(this.checked? '':'none');">
                                </td>
                            </tr>
                        </table>
                    <?endif?>
                    <table cellpadding="0" width="100%" cellspacing="0" border="0" class="edit-table" id="site_settings<?=$suffix?>"<?if($site <> '' && $arUseOnSites[$site] <> "Y") echo ' style="display:none"';?>>
                        <?if($BBCORE->isD7()){?>
                            <tr >
                                <td colspan="2" align="center">
                                    <? echo BeginNote('width="100%"'); ?>
                                    <?=Loc::getMessage("VBCH_BONUS_OPTION_d7")?>
                                    <? echo EndNote(); ?>
                                </td>
                            </tr>
                        <?}?>
                        <?php
                        foreach($STNS as $Flds=>$Fld) {
                            $element = $Fld['WIDGET'];
                            $set = $Fld;
                            unset($set['WIDGET']);
                            $set['VALUE'] = '';
                            $set['SITE']=$site;
                            $set['NAME'] = $Flds;
                            $set['VALUE']=$BBCORE->GetOptions($site,$Flds);
                            $element->settings = $set;
                            unset($set);
                            if(strpos($Flds,"HEAD")===false)
                                call_user_func_array(array($element, "showBasicEditField"), array());
                            else
                                call_user_func_array(array($element, "showBasicHeader"), array());
                        }
                        ?>
                        <tr >
                            <td colspan="2" align="center">
                                <? echo BeginNote('width="100%"'); ?>
                                <?=Loc::getMessage("VBCH_BONUS_OPTION_WARNING")?>
                                <? echo EndNote(); ?>
                            </td>
                        </tr>
                    </table>
                <?endforeach;?>
                <?$siteTabControl->End();?>
            </td></tr>
        <?$tabControl->BeginNextTab();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
        <?$tabControl->Buttons();?>
        <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=Loc::getMessage("MAIN_SAVE")?>" title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE")?>">
        <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=Loc::getMessage("MAIN_OPT_APPLY")?>" title="<?=Loc::getMessage("MAIN_OPT_APPLY_TITLE")?>">
        <?if(strlen($_REQUEST["back_url_settings"])>0):?>
            <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=Loc::getMessage("MAIN_OPT_CANCEL")?>" title="<?=Loc::getMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
            <input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
        <?endif?>
        <input <?if ($POST_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo Loc::getMessage("MAIN_RESTORE_DEFAULTS")?>">
        <?=bitrix_sessid_post();?>
        <?$tabControl->End();?>
    </form>
    <script  type="text/javascript">
        SelectName=function(id, site_id){
            if(id==''){
                BX.style(BX("CURRENCY"+site_id),'display','none');
                BX.style(BX("NAME"+site_id),'display','none');
            }
            else if(id == 'CURRENCY'){
                BX.style(BX("CURRENCY"+site_id),'display','table-row');
                BX.style(BX("NAME"+site_id),'display','none');
            }else{
                BX.style(BX("CURRENCY"+site_id),'display','none');
                BX.style(BX("NAME"+site_id),'display','table-row');
            }
        };
    </script>
    <?
    unset($BBCORE);
endif;