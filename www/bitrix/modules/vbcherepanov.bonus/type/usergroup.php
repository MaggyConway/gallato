<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "USERGROUP",
    Loc::getMessage('VBCHBB_TYPE_USERGROUP'),
    Loc::getMessage('VBCHBB_TYPE_USERGROUP_DESC'),
    array(
        'TAB1'=>array(
            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB1_TITLE'),
            'ELEM'=>array(
                'ACTIVE'=>array(
                    'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE'),
                    'DEFAULT'=>'Y',
                    "REQUIRED"=>false,
                    "TYPE"=>"string",
                    "MULTIPLE"=>false,
                ),
                'ACTIVE_FROM' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\DatetimeWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE_FROM'),
                    "DEFAULT" => "",
                    "TYPE" => "",
                ),
                'ACTIVE_TO' => array(
                    "WIDGET" => new ITRound\Vbchbbonus\DatetimeWidget(),
                    "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE_TO'),
                    "DEFAULT" => "",
                    "TYPE" => "",
                ),
                'TIMESTAMP_X'=>array(
                    "WIDGET"=>new ITRound\Vbchbbonus\LabelWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CHANGE'),
                    "DEFAULT"=>"",
                    "TYPE"=>"",
                ),
                'SITE'=>array(
                    "WIDGET"=>new ITRound\Vbchbbonus\SiteWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SITE'),
                    "REQUIRED"=>true,
                    "DEFAULT"=>\ITRound\Vbchbbonus\Vbchbbcore::GetSiteID(),
                    "TYPE"=>"string",
                    'SIZE'=>10,
                    'MULTIPLE'=>false
                ),
                'NAME'=>array(
                    "WIDGET"=>new ITRound\Vbchbbonus\TextWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROFILENAME'),
                    "DEFAULT"=>"",
                    'REQUIRED'=>true,
                    'PLACEHOLDER'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROFILENAME'),
                    "TYPE"=>"string",
                    "SIZE"=>35,
                    "MAXLENGHT"=>50
                ),
                'BONUS'=>array(
                    "WIDGET"=>new ITRound\Vbchbbonus\TextWidget(),
                    'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSCNT').' '.Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
                    "DEFAULT"=>0,
                    'REQUIRED'=>true,
                    'PLACEHOLDER'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
                    "TYPE"=>"string",
                    "SIZE"=>35,
                    "MAXLENGHT"=>50
                ),
                'TYPE'=>array(
                    'WIDGET'=>new ITRound\Vbchbbonus\LabelWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_TYPES'),
                    "DEFAULT"=>"",
                    "TYPE"=>"",
                ),
                'SCOREIN'=>array(
                    'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SCOREIN'),
                    'DEFAULT'=>'N',
                    "REQUIRED"=>false,
                    "TYPE"=>"string",
                    "MULTIPLE"=>false,
                ),
                'ISADMIN'=>array(
                    'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_INADMIN'),
                    'DEFAULT'=>'Y',
                    "REQUIRED"=>false,
                    "TYPE"=>"string",
                    "MULTIPLE"=>false,
                ),
            ),
        ),
        'TAB2'=>array(
            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB2_TITLE'),
            "ELEM"=>array(
                'NOTIFICATION'=>array(
                    'ELEMENT'=>array(
                    ),
                ),
            ),
        ),
        'TAB3'=>array(
            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB3_TITLE'),
            "ELEM"=>array(
                'FILTER'=>array(
                    'ELEMENT'=>array(
                        'USERGROUP'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\UsergroupWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_USERGROUP'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>true,
                            "SIZE"=>6,
                        ),
                        'ONLYAUTH'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ONLYAUTH'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'ACTIVATE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVUSER'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>true,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'MINBONUSPAY1'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BonusAccountsWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MINBONUS'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                        ),
                        'ELEMENTFILTER'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BigFilterWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ELEMENTFILTERTITLE'),
                            'DEFAULT'=>'',
                            "FORMNAME"=>"form1",
                        ),
                        'PERSONTYPE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\PersontypeWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PERSONTYPETITLE'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            "SIZE"=>3,
                        ),
                        'ORDERDELIVERY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\DeliveryWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERDELIVERYTITLE'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            "SIZE"=>6,
                        ),
                        'ORDERPAYMENT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\PaymentWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERPAYMENTTITLE'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            "SIZE"=>6,
                        ),
                        'ORDERPRICE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\PricefilterWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERPRICETITLE'),
                            'DEFAULT'=>'',
                            'SIZE'=>5,
                            "MULTIPLE"=>true,
                        ),
                        'NONEFIRSTORDER'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_NONEFIRSTORDER'),
                            'DEFAULT'=>'N',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'ORDERPERIOD'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\OrderPeriodWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERPERIODTITLE'),
                            'DEFAULT'=>'',
                        ),
                    ),
                ),
            ),
        ),
        'TAB4'=>array(
            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB4_TITLE'),
            "ELEM"=>array(
                'BONUSCONFIG'=>array(
                    'ELEMENT'=>array(

                    ),
                ),
            ),
        ),
        'TAB5'=>array(
            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_TAB5_TITLE'),
            "ELEM"=>array(
                'SETTINGS'=>array(
                    'ELEMENT'=>array(
                        'DESCRIPTION'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_DESCRIPTION'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            'HELP'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_DESCRIPTION'),
                        ),
                        'CODE'=>array(
                            "WIDGET"=>new ITRound\Vbchbbonus\TextWidget(),
                            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CODE'),
                            "DEFAULT"=>"",
                            'REQUIRED'=>false,
                            'PLACEHOLDER'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CODE'),
                            "TYPE"=>"string",
                            "SIZE"=>35,
                            "MAXLENGHT"=>50
                        ),
                        'SORT'=>array(
                            "WIDGET"=>new ITRound\Vbchbbonus\TextWidget(),
                            'TITLE'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SORT'),
                            "DEFAULT"=>"500",
                            'REQUIRED'=>false,
                            'PLACEHOLDER'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SORT'),
                            "TYPE"=>"string",
                            "SIZE"=>35,
                            "MAXLENGHT"=>50
                        ),
                        'INGROUP'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\UsergroupWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_ADD_USERGROUP'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>true,
                            "SIZE"=>6,
                        ),
                        'OUTGROUP'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\UsergroupWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_DEL_USERGROUP'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>true,
                            "SIZE"=>6,
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(

        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'sale_OnSaleOrderPaid',
            'MODULE'=>'CVbchbbEvents::PayerOrder',
            'RULES'=>'$this->isD7();',
        ),


        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'sale_OnSaleStatusOrderChange',
            'MODULE'=>'CVbchbbEvents::StatusOrder',
            'RULES'=>'$this->isD7();',
        ),
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'sale_OnSaleOrderCanceled',
            'MODULE'=>'CVbchbbEvents::CancelOrder',
            'RULES'=>'1==1;',
        ),
    ),
    array(

    ),
    'GetRules'=>function ($profileID,$Filter=array(),$arFields=array()) {
        $fltr = array();$flds = array();$order_summ=0;
        $Settings = $this->GetSettingsProf($profileID);
        if($Settings['NONBONUSPAYBONUS']=='Y'){
            if($this->GetPartPayOrder($arFields['ORDER_ID'],$arFields['USERID'],$Settings['SCOREIN'])) return false;
        }
        if($arFields['NONE']) {
            if($Settings['BONUSCHECK']['CHECK'] == 'status'){
                if((array_key_exists("CHANGESTATUS",$arFields) && $arFields['CHANGESTATUS'])){
                    if (array_key_exists('STATUS_ID', $arFields)) {
                        if ($arFields['STATUS_ID'] != $Settings['BONUSCHECK']['STATUS']) return false;
                    } else return false;
                }else return false;
            }elseif($Settings['BONUSCHECK']['CHECK'] == 'pay'){
                if($Settings['ALLPAYMENT'] == 'Y'){
                    if ($arFields['VALUE']!='Y') return false;
                }
            }
        }
        if(!array_key_exists("BASKET",$arFields)){
            $arFields=$this->GetArrayForProfile(0,array(),1,false,$arFields['ORDER_ID']);
        }
        if($arFields['USERID']) {
            if(array_key_exists('ONLYAUTH',$Filter) && $Filter['ONLYAUTH']=='Y'){
                unset($arFields['USERGROUP'][array_search(2,$arFields['USERGROUP'])]);
                unset($fltr['ONLYAUTH']);
            }
            $fltr['ACTIVATE'] = $Filter['ACTIVATE'];
            $flds['ACTIVATE'] = $this->UserActive($arFields['USERID']);
            $fltr['USERGROUP'] = $Filter['USERGROUP'];
            $flds['USERGROUP'] = $arFields['USERGROUP'];
        }else{
            $fltr['USERGROUP'] = $Filter['USERGROUP'];
            $flds['USERGROUP']=array(2);
        }
        if($arFields['PERSON_TYPE_ID']){
            $fltr['PERSONTYPE'] = $Filter['PERSONTYPE'];
            $flds['PERSONTYPE'] = intval($arFields['PERSON_TYPE_ID']);
        }
        if($arFields['DELIVERY_ID']){
            $fltr['ORDERDELIVERY'] = $Filter['ORDERDELIVERY'];
            $flds['ORDERDELIVERY'] = is_array($arFields['DELIVERY_ID']) ? $arFields['DELIVERY_ID'] : intval($arFields['DELIVERY_ID']);
        }
        if($arFields['PAY_SYSTEM_ID']){
            $fltr['ORDERPAYMENT'] = $Filter['ORDERPAYMENT'];
            $flds['ORDERPAYMENT'] = is_array($arFields['PAY_SYSTEM_ID']) ? $arFields['PAY_SYSTEM_ID'] : intval($arFields['PAY_SYSTEM_ID']);
        }
        if($this->CheckArray($arFields['BASKET'])){
            $order_summ=0;
            foreach($arFields['BASKET'] as $bask){
                $order_summ+=$bask['PRICE'];
            }
        }
        if($order_summ>0){
            $fltr['ORDERPRICE'] = ($order_summ >= $Filter['ORDERPRICE']['OT'][0] && $order_summ <= $Filter['ORDERPRICE']['DO'][0]) ? 'Y' : 'N';
            $flds['ORDERPRICE'] = 'Y';
        }

        $fltr['ORDERPERIOD'] = ($Filter['ORDERPERIOD']['ACTIVE'] == 'Y') ? ($this->GetUserOrderSumm($arFields['USERID'],
            array(  'SUMMA' => $Filter['ORDERPERIOD']['SUMMA'],
                'SUMMA1' => $Filter['ORDERPERIOD']['SUMMA1'],
                'PERIOD' => $Filter['ORDERPERIOD']['PERIOD'],
                'STATUS' => $Filter['ORDERPERIOD']['STATUS'],
                //"ORDER_ID"=>intval($arFields['ORDER_ID']),
            )) ? 'Y' : 'N') : 'N';
        $flds['ORDERPERIOD'] = ($Filter['ORDERPERIOD']['ACTIVE'] == 'Y') ? 'Y' : 'N';
        $glFilter=str_replace('"',"",$Filter['ELMNTFLTR']);

        if($glFilter!='((1 == 1))'){
            $fltr['ELEMENTFILTER']="Y";
            foreach($arFields['BASKET'] as $idf=>$arItems){
                if(!eval('return ' . $glFilter . ';')){
                    unset($arFields['BASKET'][$idf]);
                }
            }

            $flds['ELEMENTFILTER']= $this->checkArray($arFields['BASKET']) ? "Y" : "N";
        }else{
            $fltr['ELEMENTFILTER']=$flds['ELEMENTFILTER']="N";
        }
        if($Filter['NONEFIRSTORDER']=='Y'){
            $fltr['NONEFIRSTORDER']=$Filter['NONEFIRSTORDER'];
            $flds['NONEFIRSTORDER']=(intval($arFields['USERORDERCOUNT'])==0) ? 'N' :'Y';
        }
        if($Filter['MINBONUSPAY1']) $Filter['MINBONUSPAY1']=array_filter($Filter['MINBONUSPAY1']);

        $Settings['SALEORDERAJAX']='BONUSPAY';
        if($Filter['MINBONUSPAY1'] && is_array($Filter['MINBONUSPAY1']) && sizeof($Filter['MINBONUSPAY1'])>0){

            foreach($Filter['MINBONUSPAY1'] as $p=>$ll){
                $l=floatval($this->GetUserBonus($arFields['USERID'],$Settings['SALEORDERAJAX'],$p));
                if($ll[0] && $ll[1]){
                    if($l>=floatval($ll[0]) && $l<=floatval($ll[1])){
                        $k='Y';
                    }else $k='N';
                    $fltr['MINBONUSPAY1_'.$p]='Y';
                    $flds['MINBONUSPAY1_'.$p]=$k;
                }

            }
        }

        $filter=$this->GetFilterString($fltr,$flds);
        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $Settings=$this->CheckSerialize($profile['SETTINGS']);
        global $USER;
        $ingroup=$Settings['INGROUP'];
        $outgroup=$Settings['OUTGROUP'];
        $user=new \CUser;
        $currentData['GROUPS_ID']=$this->GetUserGroupByUser($arFields['USERID']);
        if(sizeof($outgroup)>0){
            foreach($outgroup as $d){
                $index=array_search($d,$currentData['GROUPS_ID']);
                if($index)
                    unset($currentData['GROUPS_ID'][$index]);
            }
        }
        if(sizeof($ingroup)>0)
            $currentData['GROUPS_ID']=array_merge($currentData['GROUPS_ID'],$ingroup);
        $currentData['GROUPS_ID']=array_filter(array_unique($currentData['GROUPS_ID']));
        $user->Update($arFields['USERID'],array('GROUP_ID'=>$currentData['GROUPS_ID']));
        unset($user);
        if(intval($arFields['USERID'])==intval($USER->GetID())){
            $USER->Logout();
            $USER->Authorize($arFields['USERID'], isset($_SESSION['SESS_AUTH']['STORED_AUTH_ID']));
        }
        return 0;
    }
);
