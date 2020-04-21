<?php
namespace ITRound\Vbchbbonus;

use \Bitrix\Main;
use Bitrix\Main\Context;
use \Bitrix\Main\Localization\Loc;
use CUtil;

Loc::loadMessages(__FILE__);


class EditAdminOrderBonusClass
{
    public static function onInit()
    {
        return array(
            "BLOCKSET" => "\\ITRound\\Vbchbbonus\\EditAdminOrderBonusClass",
            "check" => array("\\ITRound\\Vbchbbonus\\EditAdminOrderBonusClass", "mycheck"),
            "action" => array("\\ITRound\Vbchbbonus\\EditAdminOrderBonusClass", "myaction"),
            "getScripts" => array("\\ITRound\Vbchbbonus\\EditAdminOrderBonusClass", "mygetScripts"),
            "getBlocksBrief" => array("\\ITRound\Vbchbbonus\\EditAdminOrderBonusClass", "mygetBlocksBrief"),
            "getBlockContent" => array("\\ITRound\Vbchbbonus\\EditAdminOrderBonusClass", "mygetBlockContent"),
        );
    }

    public static function mygetBlocksBrief($args)
    {
        return array(
            'itrbonusloyalty' => array("TITLE" => Loc::getMessage('ITR_CUSTOMORDERADMIN_NAMEBLOCK')),
        );
    }
    public static function myaction($args)
    {
        // при сохранении заказа

        if(array_key_exists("ORDER",$args)){
            $order=$args['ORDER'];
        }else{
            $order=\Bitrix\Sale\Order::load($args['ID']);
        }
        $userid=$order->getField("USER_ID");
        $lid=$order->getField('LID');
        unset($order);
        $refcode=intval($_REQUEST['REFERER']);
        if(\Bitrix\Main\Loader::includeModule("vbcherepanov.bonus")){
            $refres=\ITRound\Vbchbbonus\CVbchRefTable::getList(array(
                'filter'=>array('USERID'=>$userid,'LID'=>$lid),
                'select'=>array('ID','REFFROM')
            ))->fetch();
            if(!$refres){
                $arF=array(
                    'SITE_ID'=>$lid,
                    'REFERER'=>$refcode,
                    'USERID'=>$userid,
                    ''
                );
                \ITRound\Vbchbbonus\Vbchreferal::AddRef($arF,true);
            }
        }
        // заказ сохранен, сохраняем данные пользовательских блоков
        // возвращаем True в случае успеха и False - в случае ошибки
        // в случае ошибки $GLOBALS["APPLICATION"]->ThrowException("Ошибка!!!", "ERROR");
        return true;
    }



    public static function mycheck($args)
    {
        // заказ еще не сохранен, делаем проверки
        // возвращаем True, если можно все сохранять, иначе False
        // в случае ошибки $GLOBALS["APPLICATION"]->ThrowException("Ошибка!!!", "ERROR");
        return true;
    }

    public static function mygetBlockContent($blockCode, $selectedTab, $args)
    {
        $request=Context::getCurrent()->getRequest();
        $file=explode("/",$request->getScriptName());
        $view = in_array("sale_order_view.php",$file);
        $new = in_array("sale_order_create.php",$file);
        $edit = in_array("sale_order_edit.php",$file);
        $disabled = (!$edit) ? 'disabled' : '';
        $referalodatel ='';
        $result = '';
        if ($selectedTab == 'tab_order')
        {
            if ($blockCode == 'itrbonusloyalty')
            {
                $orderID = !empty($args['ORDER']) ? $args['ORDER']->getField('ID') : null;
                $user_id = !empty($args['ORDER']) ? $args['ORDER']->getField('USER_ID') : null;
                $lid = !empty($args['ORDER']) ? $args['ORDER']->getField('LID') : '';
                if($orderID) {
                    $BonusCore = new Vbchbbcore();
                    $arFields = $BonusCore->GetArrayForProfile(0, array(), 1, true, $orderID);
                    $orderBonus = $BonusCore->GetCartOrderBonus('ORDER', $arFields);
                    $orderBonusPay = $BonusCore->getMaxBonusPay($args['ORDER'], $request);
                }
                if(!is_null($user_id)){

                    $user_accounts=self::getBonusAcconts($user_id,$lid);
                    $q1='';$q2='';
                    foreach($user_accounts as $kk){

                        $q1.='<td>'.$kk['NM'].'</td><td class="separator"></td>';
                        $val=$BonusCore->ReturnCurrency($kk['BUDGET']);
                        $q2.='<td id="bnscnt">'.$val.'</td><td class="separator"></td>';
                    }
                    $str='<table class="adm-bus-pay-statuspay" style="width:100%"><thead>';
                    $str.='<tr>'.$q1.'</tr></thead>';
                    $str.='<body><tr>'.$q2;
                    $str.='</tr></tbody>
						</table>';
                    $result.='';
                }else{
                    $result = '<span id="bnstable"></span>';
                }
                if(!is_null($user_id)){?>
                    <div class="adm-bus-pay-section">
                        <div class="adm-bus-pay-section-content posr">
                            <div class="">
                                <b><?=Loc::getMessage('ITR_CUSTOMORDERADMIN_ORDER_BONUS')?></b>
                                <strong><?=$BonusCore->ReturnCurrency($orderBonus)?></strong>
                            </div>
                            <br/>

                            <div class="adm-bus-table-container caption border sale-order-props-group">
                                <div class="adm-bus-table-caption-title"><b><?=Loc::getMessage('ITR_CUSTOMORDERADMIN_MAXPAY')?></b></div>
                                <table class="adm-detail-content-table edit-table" border="0" width="100%">
                                    <? foreach($orderBonusPay['MAXPAY'] as $profID=>$pay){?>
                                        <tr>
                                            <td class="adm-detail-content-cell-l" width="40%">
                                                <?=Loc::getMessage('ITR_CUSTOMORDERADMIN_BY_PROFILE')?><?=$profID?>:
                                            </td>
                                            <td class="adm-detail-content-cell-r">
                                                <strong><?=$BonusCore->ReturnCurrency($pay)?></strong>
                                            </td>
                                        </tr>
                                    <?}?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="adm-bus-component-content-container">
                    <div class="adm-bus-pay-section">
                        <div class="adm-bus-pay-section-content posr">
                            <?=$str?>
                        </div>
                        <div class="adm-bus-pay-section-content posr">
                            <div class="adm-bus-pay-wallet">
                                <input <?=$disabled?> id="BONUS_CNT" name="BONUS_CNT" type="text" value="">
                                <label for="PAY_BONUS_ACCOUNT" class="bxr-font-color bxr-border-color" <?=$disabled?> style="border: 1px solid rgb(227, 230, 232); padding: 5px; margin-left: 10px; cursor: pointer;">
                                    <input type="checkbox" id="PAY_BONUS_ACCOUNT" name="PAY_BONUS_ACCOUNT" value="Y" <?=$disabled?> style="display: none;">
                                    <span><?=Loc::getMessage('ITR_CUSTOMORDERADMIN_BONUS_PAY_BUTTON')?></span>
                                </label>
                                <br/><br/>
                                <div>
                                    <?=Loc::getMessage('ITR_CUSTOMORDERADMIN_SMS_INPUT')?><br/>&nbsp;<input <?=$disabled?> type="text">
                                    <label for="CHECK_SMS" <?=$disabled?> class=" bxr-font-color bxr-border-color" style="border: 1px solid rgb(227, 230, 232); padding: 5px; margin-left: 10px; cursor: pointer;">
                                        <input type="checkbox" <?=$disabled?> id="CHECK_SMS" name="CHECK_SMS" value="Y" style="display: none;">
                                        <span><?=Loc::getMessage('ITR_CUSTOMORDERADMIN_SUCCESS')?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?}?>
                <div class="adm-bus-pay-section">
                    <div class="adm-bus-pay-section-content posr">
                        <div class="">
                            <?=Loc::getMessage('ITR_CUSTOMORDERADMIN_REFERAL_AFFIL')?>
                            <strong><?=$referalodatel?></strong>
                            <br/><?=Loc::getMessage('ITR_CUSTOMORDERADMIN_REF_INPUT')?>
                            <input <?=$disabled?> type="text" value="">
                        </div>
                    </div>
                </div>

                </div>
                <?
            }
        }

        return $result;
    }
    public static function getBonusAcconts($user_id,$site_id){
        $dbAccountUser=AccountTable::getList(array(
            'filter'=>array("USER_ID"=>$user_id),
        ))->fetchAll();
        $k=array();
        if(sizeof( $dbAccountUser)>0){
            foreach( $dbAccountUser as $qa){
                $k[$qa['BONUSACCOUNTSID']]=array('BUDGET'=>$qa['CURRENT_BUDGET'],'BNC'=>$qa['BONUSACCOUNTSID']);
            }
        }
        $rs=CVbchBonusaccountsTable::getList(array(
            'filter'=>array('ID'=>array_keys($k),'ACTIVE'=>"Y"),
        ))->fetchAll();
        if(sizeof($rs)>0){
            foreach($rs as $sr){
                $stng= Vbchbbcore::CheckSerialize($sr['SETTINGS']);
                $r=array();
                if($stng['SUFIX']=='NAME' && sizeof($stng['NAME'])>0){
                    foreach($stng['NAME'] as $w){
                        $r[]=$w;
                    }
                }
                $k[$sr['ID']]['NAME']=$r;
                $k[$sr['ID']]['NM']=$sr['NAME'];
                $k[$sr['ID']]['CUR']=$stng['CURRENCY'];
            }
        }
        return $k;
    }
    public static function mygetScripts($args)
    {

        $order_id = !empty($args['ORDER']) ? $args['ORDER']->getField('ID') : null;
        $lid = !empty($args['ORDER']) ? $args['ORDER']->getField('LID') : null;
        $params = [
            'action'=>'refreshOrderData',
            'sessid'=>bitrix_sessid_get(),
            'refreshOrderData'=>'Y',
        ];
        if(!is_null($order_id))
            $params['formData']['orderID']=$order_id;
        if(!is_null($lid))
            $params['formData']['SITE_ID']=$lid;

        return '<script type="text/javascript">
        BX.ready(function(){
            BX.bind(BX("PAY_BONUS_ACCOUNT"), "click", function(e) {
                var bonus = BX("BONUS_CNT").value;
                var params = [];
                //console.log(bonus);
                if(this.checked){
                    params.pay=true;
                }else{
                    params.pay=false;
                }
                //console.log(params);
            })
        });
        BX.addCustomEvent("onAjaxSuccess", function(e) {
            var order_new = '.(is_null($args['ORDER']) ? 1 : 0).';
            var user_id = "";
            var lid = "";
            var order_price = 0;
            if(e.RESULT == "OK"){  
                if(e.hasOwnProperty("ORDER_DATA")){
                    if(e.ORDER_DATA.hasOwnProperty("USER_ID")){
                        user_id = e.ORDER_DATA.USER_ID;
                    }
                    if(e.ORDER_DATA.hasOwnProperty("LID")){
                        lid = e.ORDER_DATA.LID;
                    }
                    if(e.ORDER_DATA.hasOwnProperty("PRICE")){
                        order_price = e.ORDER_DATA.PRICE;
                    }
                }else{
                   BX.Sale.Admin.OrderAjaxer.sendRequest('.CUtil::PhpToJSObject($params,false,true).');
                }
            }
        });            
            </script>';
    }
}

class ITROrderAdminHeader {

    function orderAdminHeader(Main\Event $event){
        $order = $event->getParameter("ORDER");
        $BonusCore = new Vbchbbcore();
        $bonus = $BonusCore->ReturnCurrency($BonusCore->GetBonusFromOrderID($order->getField('ID')));
        $bonuspay = $BonusCore->ReturnCurrency($BonusCore->GetPayedBonusOrder($order->getField('ID')));
        unset($BonusCore);
        return new Main\EventResult(
            Main\EventResult::SUCCESS,
            array(
                array(
                    'TITLE' => Loc::getMessage('ITR_CUSTOMORDERADMIN_BONUS'),
                    'VALUE' => $bonus,
                    'ID' => 'BONUS'
                ),
                array(
                    'TITLE' => Loc::getMessage('ITR_CUSTOMORDERADMIN_BONUSPAY'),
                    'VALUE' => $bonuspay,
                    'ID' => 'BONUSPAY'
                ),
            )
        );
    }
}