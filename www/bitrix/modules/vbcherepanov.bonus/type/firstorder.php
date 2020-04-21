<?php
use \Bitrix\Main\Localization\Loc;
use ITRound\Vbchbbonus\CheckboxWidget;
use ITRound\Vbchbbonus\HtmleditorWidget;
use ITRound\Vbchbbonus\TextboxWidget;

$profiles=array(
    "FIRSTORDER",
    Loc::getMessage('VBCHBB_TYPE_PROFILE_FIRSTORDER_NAME'),
    Loc::getMessage('VBCHBB_TYPE_PROFILE_FIRSTORDER_DSC'),
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
                    "DEFAULT"=>'',
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
                    "DEFAULT"=>"",
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
                        'TRANSACATIONMESSAGE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_TRANSACATIONMESSAGE'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                            'HELP'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGEDESC_TR'),
                        ),
                        'SENDSMS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SENDSMS'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'SMSMESSAGE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SMSMESSAGE'),
                            'DEFAULT'=>'',
                            'HEIGHT'=>'400',
                            'COLS'=>65,
                            'ROWS'=>16,
                            'BODY_TYPE'=>'text',
                            'WIDTH'=>'100%',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                            'HELP'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGEDESC_TR'),
                        ),
                        'SENDEMAIL'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SENDEMAIL'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'SENDADMIN'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SENDADMIN'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'EMAILTEMPLATE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\HtmleditorWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGE'),
                            'DEFAULT'=>'Y',
                            'HEIGHT'=>'400',
                            'BODY_TYPE'=>'html',
                            'WIDTH'=>'100%',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                            'HELP'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGEDESC_TR'),
                        ),
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
                        'ACTIONSTOP'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\ActionWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIONSTOP_BONUS'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            'SIZE'=>5,
                        ),
                        'ORDERPRICE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\PricefilterWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERPRICETITLE'),
                            'DEFAULT'=>'',
                            'SIZE'=>5,
                            "MULTIPLE"=>true,
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
                        'DELAY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\DelayWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_DELAYTITLE'),
                            'DEFAULT'=>array(),
                            "REQUIRED"=>true,
                            "TYPE"=>"",
                        ),
                        'TIMELIFE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TimelifeWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_LIVETITLE'),
                            'DEFAULT'=>array(),
                            "REQUIRED"=>true,
                            "TYPE"=>"",
                        ),
                        'BONUSIS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSISTITLE'),
                            'DEFAULT'=>1,
                            "REQUIRED"=>true,
                            'SIZE'=>10,
                            'PLACEHOLDER'=>'',
                            "MAXLENGHT"=>50,
                            "TYPE"=>"",
                        ),
                        'BONUSPARTORDER'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSPARTORDER'),
                            'DEFAULT'=>1,
                            "REQUIRED"=>false,
                            'SIZE'=>10,
                            'PLACEHOLDER'=>'',
                            "MAXLENGHT"=>50,
                            "TYPE"=>"",
                        ),
                        'ROUND'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\RoundWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ROUNDTITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                        ),
                        'ROUNDONE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_ROUND_MIN_ONE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                        ),
						 'BONUSINNERIN'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BonusInnerWidget(),
                            "TITLE"=>Loc::getMessage('VBCH_BONUSINNER_IN'),
                            'DEFAULT'=>'',
                        ),
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
                        'PROPERTYDISCOUNT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\IbpropertyWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROPERTYDISCOUNTTITLE'),
                            'DEFAULT'=>'',
                            'TYPE'=>array('S','L','N'),
                        ),
                        'DISCOUNTBONUSDISCOUNT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_DISCOUNTBONUSDISCOUNTTITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "PLACEHOLDER"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
                        ),
                        'PROPERTYBONUS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\IbpropertyWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROPERTYBONUSTITLE'),
                            'DEFAULT'=>'',
                            'TYPE'=>array('S','L','N'),
                        ),
                        'POROGBONUS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\PorogWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_POROGBONUSTITLE'),
                            'DEFAULT'=>'',
                        ),
                        'ALLPAYMENT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ALLPAYMENTTITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'BONUSCHECK'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BonuscheckWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSCHECKTITLE'),
                            'DEFAULT'=>'',
                            "TYPE"=>"string",
                        ),
                        'USECOUNT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_USECOUNTTITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'DISCOUNTWITHOUT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\DiscountWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_DISCOUNTWITHOUTTITLE'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            'SIZE'=>5,
                        ),
                        'MINOFFERPRICE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MINOFFERPRICETITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                        ),
                        'WITHOUTDELIVERYPRICE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_WITHOUTDELIVERYPRICETITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'WITHOUTPAYBONUS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_WITHOUTPAYBONUS'),
                            'DEFAULT'=>'N',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'WITHOUTBONUSORDER'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_WITHOUTBONUSORDERTITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'ORDERPROPBONUS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\OrderPropWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERPROPBONUS'),
                            'DEFAULT'=>'Y',
                            "TYPE"=>"string",
                            "MULTIPLE"=>false
                        ),
                        'REFBONUS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\ReferalWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_REFBONUSTITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                        ),
                        'REFACCOUNT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BonusInnerWidget(),
                            "TITLE"=>Loc::getMessage('VBCH_REFACCOUNT_IN'),
                            'DEFAULT'=>'',
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'sale_OnSalePaymentSetField',
            'MODULE'=>'CVbchbbEvents::OnSalePaymentSetField',
            'RULES'=>'$this->isD7();',
        ),
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
    array(),
    'GetRules'=>function ($profile,$Filter=array(),$arFields=array()){
        if($arFields['USERID'] && $this->isFirstOrder($arFields['USERID'],$arFields['ORDER_ID']) && $arFields['USERORDERCOUNT']==0){
            $fltr = array();$flds = array();$order_summ=0;
            $Settings = $this->GetSettingsProf($profile);
            if($Settings['NONBONUSPAYBONUS']=='Y'){
                if($this->GetPartPayOrder($arFields['ORDER_ID'],$arFields['USERID'],$Settings['SCOREIN'])) return false;
            }
            $payed = $this->GetOptions($this->SITE_ID,'CHECKPAYORDER');
            $payed = $payed['OPTION'];
            if($payed=='') $payed = 'N';
            if($arFields['NONE']) {
                if($Settings['BONUSCHECK']['CHECK'] == 'status'){
                    if((array_key_exists("CHANGESTATUS",$arFields) && $arFields['CHANGESTATUS'])){
                        if (array_key_exists('STATUS_ID', $arFields)) {
                            if ($arFields['STATUS_ID'] != $Settings['BONUSCHECK']['STATUS']) return false;
                        } else return false;
                    }else return false;
                }elseif($Settings['BONUSCHECK']['CHECK'] == 'pay'){
                    if($payed=='Y') {
                        if ($Settings['ALLPAYMENT'] == 'Y') {
                            if ($arFields['VALUE'] != 'Y') return false;
                        }
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
					if($Settings['USECOUNT']=='Y'){
						$order_summ+=$bask['PRICE']*$bask['QUANTITY'];
					}else{
						$order_summ+=$bask['PRICE'];
					}
                    
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
                    "ORDER_ID"=>intval($arFields['ORDER_ID']),
                )) ? 'Y' : 'N') : 'N';
            $flds['ORDERPERIOD'] = ($Filter['ORDERPERIOD']['ACTIVE'] == 'Y') ? 'Y' : 'N';

            $glFilter=$Filter['ELMNTFLTR'];
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
            if($Filter['MINBONUSPAY1']) $Filter['MINBONUSPAY1']=array_filter($Filter['MINBONUSPAY1']);
			 if($Filter['MINBONUSPAY1'] && is_array($Filter['MINBONUSPAY1']) && sizeof($Filter['MINBONUSPAY1'])>0){
			foreach($Filter['MINBONUSPAY1'] as $p=>$ll){
				if($ll[0]=='') $ll[0]=0;
				if($ll[1]=='') $ll[1]=0;
				if($ll[0]==0 && $ll[1]==0){
					
				}else{
					$l=floatval($this->GetUserBonus($arFields['USERID'],'BONUSPAY',$p));
					if($l=='' || empty($l)) $l=0;
					$fltr['MINBONUSPAY1_'.$p]='Y';
					$flds['MINBONUSPAY1_'.$p]=($l>=$ll[0] && $l<=$ll[1]) ? 'Y' : 'N';
				}
				
            }
        }
            if($Filter['ACTIONSTOP']['ACTIVE']=='Y' && sizeof($arFields['DISCOUNT_ORDER_LIST'])>0){
                $fltr['ACTIONSTOP']='Y';
                if( $this->CheckArray(array_intersect(array_keys($arFields['DISCOUNT_ORDER_LIST']),$Filter['ACTIONSTOP']['DISCOUNT']))){
                    $flds['ACTIONSTOP']='N';
                }else{
                    $flds['ACTIONSTOP']='Y';
                }
            }else{
                $fltr['ACTIONSTOP']='N';
                $flds['ACTIONSTOP']='N';
            }
            $filter=$this->GetFilterString($fltr,$flds);
        }else $filter=false;
        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $bonus=0;$innerbonus=false;
        $bonus=$profile['BONUS'];
        foreach($arFields['BASKET'] as $bask){
            $arFields['ORDER_PRICE']=$arFields['ORDER_PRICE']+($bask['PRICE']*$bask['QUANTITY']);
        }

        $Filter=$this->CheckSerialize($profile['FILTER']);
        $Settings=$this->CheckSerialize($profile['SETTINGS']);


        $glFilter=$Filter['ELMNTFLTR'];
        if($glFilter!='((1 == 1))'){
            foreach($arFields['BASKET'] as $idf=>$arItems){
                if(!eval('return ' . $glFilter . ';'))
                    unset($arFields['BASKET'][$idf]);

            }
        }
        if($Settings['POROGBONUS']['ACTIVE']=='Y'){
            $l=$this->GetUserOrderSumm($arFields['USERID'],
                array("SUMMA"=>$Settings['POROGBONUS']['COUNT'],
                    "SUMMA1"=>$Settings['POROGBONUS']['COUNT1'],
                    "PERIOD"=>$Settings['POROGBONUS']['PERIOD'],
                    "STATUS"=>$Settings['POROGBONUS']['STATUS'],
                    "ORDER_ID"=>intval($arFields['ORDER_ID']),
                )
            );
            if($l) $bonus=$Settings['POROGBONUS']['BONUS'];
        }
        if($Settings['DISCOUNTWITHOUT']['ACTIVE']=='Y'){
            $discount=$Settings['DISCOUNTWITHOUT']['DISCOUNT'];
            foreach($arFields['BASKET'] as $idof=>$arItem){
                $offer_price=$arItem['PRICE'];
                $del=false;
                if($this->CheckArray($arItem['DISCOUNT'])){
                    foreach($arItem['DISCOUNT'] as $dsk){
                        if($dsk['ACTIVE'] && in_array($dsk['ID'],$discount)){
                            $del=true;break;
                        }
                    }
                    if($del || $offer_price<$Settings['MINOFFERPRICE']) {
                        unset($arFields['BASKET'][$idof]);
                    }
                }

            }
        }
        $allPrice=0;
        foreach($arFields['BASKET'] as $idof=>$arItem){
            $cnt=1;$offer_price=0;
            $offer_price=$arItem['PRICE'];
            if($Settings['USECOUNT']=='Y'){
                $cnt = (!empty($arItem['QUANTITY']) ? $arItem['QUANTITY'] : $arFields['COUNT']);
                $offer_price = $offer_price * $cnt;
            }
            $allPrice+=$offer_price;
            if ($Settings['PROPERTYBONUS']['ACTIVE'] == 'Y') {
                $innerbonus = true;
                if ($this->CheckArray($Settings['PROPERTYBONUS']['ID'])) {
                    $cnt = ($Settings['USECOUNT'] == 'Y' ? ($arItem["QUANTITY"] ? $arItem["QUANTITY"] : ($arFields['COUNT'] ? $arFields['COUNT'] : 1)) : 1);
                    $k = $this->BonusFromProp($arItem, $Settings['PROPERTYBONUS']['ID']);
                    if ($k == '') $k = $bonus;
                    if ($Settings['WITHOUTPAYBONUS'] == 'Y') {
                        $offer_price=$offer_price-$arItem['BONUSPAY'];
                    }
                    $arFields['BASKET'][$idof]['BONUS'] = $this->GetAllBonus($k, $offer_price, $Settings['WITHOUTBONUSORDER'] == 'Y', $cnt ? $cnt : 1);

                }
            }
        }

        foreach($arFields['BASKET'] as $idof=>$arItem) {
            if ($Settings['PROPERTYDISCOUNT']['ACTIVE'] == 'Y') {
                if ($this->CheckArray($Settings['PROPERTYDISCOUNT']['ID'])) {
                    foreach ($Settings['PROPERTYDISCOUNT']['ID'] as $propID) {
                        if (isset($arItem['PROPERTY_' . $propID . '_VALUE']) && $arItem['PROPERTY_' . $propID . '_VALUE'] != "") {
                            $cnt=($Settings['USECOUNT']=='Y' ? ($arItem["QUANTITY"] ? $arItem["QUANTITY"] :($arFields['COUNT'] ? $arFields['COUNT']: 1)) : 1);
                            $arFields['BASKET'][$idof]['BONUS']=$this->GetAllBonus($arItem['PROPERTY_'.$propID.'_VALUE'],$arFields['BASKET'][$idof]['BONUS'],$Settings['WITHOUTBONUSORDER']=='Y',$cnt);
                        }
                    }
                }
            }
        }
        $arFields['ORDER_PRICE']=$allPrice;
        if(isset( $arFields['ORDER_PRICE'])){
            $ORDER_PRICE=($Settings['WITHOUTDELIVERYPRICE']=='Y') ?  floatval($arFields['ORDER_PRICE']) :  floatval($arFields['ORDER_PRICE'])+floatval($arFields['DELIVERY_PRICE']);
        }else{
            $ORDER_PRICE=$allPrice;
        }
        if($arFields['BONUSPAY']=='' || $arFields['BONUSPAY']==0)
            $arFields['BONUSPAY']=$this->GetPayedBonusOrder($arFields['ORDER_ID']);
        if($Settings['WITHOUTPAYBONUS']=='Y'){
            if(($arFields['BONUSPAY']=='' || $arFields['BONUSPAY']==0) && $arFields['ORDER_ID']!=0)
                $arFields['BONUSPAY']=$this->GetPayedBonusOrder($arFields['ORDER_ID']);
            if($arFields['BONUSPAY']==0){
                if($_REQUEST['order']['BONUS_CNT']!=0 && $_REQUEST['order']['PAY_BONUS_ACCOUNT']=='Y'){
                    $arFields['BONUSPAY']=$_REQUEST['order']['BONUS_CNT'];
                }
            }
            $ORDER_PRICE=$ORDER_PRICE-$arFields['BONUSPAY'];
        }
        if($innerbonus){
            $bonus=0;
            foreach($arFields['BASKET'] as $arItem) {
                $bonus+=$arItem['BONUS'];
            }
        }else{
            $bonus=$this->GetAllBonus($bonus,$ORDER_PRICE,$Settings['WITHOUTBONUSORDER']=='Y');
        }

        $BONUSCONFIG=$this->CheckSerialize($profile['BONUSCONFIG']);
        if(array_key_exists('BONUSPARTORDER', $BONUSCONFIG) && ($BONUSCONFIG['BONUSPARTORDER']!='' && floatval($BONUSCONFIG['BONUSPARTORDER']>1))){
            $bonus=intval($ORDER_PRICE/floatval($BONUSCONFIG['BONUSPARTORDER']))*$bonus;
        }

        if($BONUSCONFIG['ROUNDONE']=='Y'){
            if($bonus<1 && $bonus>0) $bonus=1;
        }
        if($bonus<0) $bonus=0;
        return $bonus;
    }
);