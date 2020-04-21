<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "BONUSPAY",
    Loc::getMessage('VBCHBB_TYPE_PROFILE_BONUSPAY_NAME'),
    Loc::getMessage('VBCHBB_TYPE_PROFILE_BONUSPAY_DSC'),

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
                            'DEFAULT'=>'',
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
                        'MINBONUSPAY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MINBONUSPAY'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
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
                        'MINOFFERPRICE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MINOFFERPRICETITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
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
                        'DISCOUNTWITHOUT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\DiscountWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_DISCOUNTWITHOUTTITLE'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            'SIZE'=>5,
                        ),
                        'ACTIONSTOP'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\ActionWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIONSTOP'),
                            'DEFAULT'=>'',
                            "MULTIPLE"=>true,
                            'SIZE'=>5,
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
                        'ROUND'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\RoundWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ROUNDTITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                        ),
                        'BONUSINNEROUT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BonusInnerWidget(),
                            "TITLE"=>Loc::getMessage('VBCH_BONUSINNER_OUT'),
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
                            'DEFAULT'=>'',
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
                        'SALEORDERAJAX'=>array(
                            "WIDGET"=>new ITRound\Vbchbbonus\ComboboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBONUS_OPTION_TITLE_SALEORDERAJAX'),
                            "REQUIRED"=>true,
                            "DEFAULT"=>'',
                            "TYPE"=>"string",
                            'SIZE'=>10,
                            'MULTIPLE'=>false,
                            'VARIANT'=>array(array('ID'=>'BONUSPAY','NAME'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_BONUSPAY_TYPE1')),array('ID'=>'SYSTEMPAY','NAME'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_BONUSPAY_TYPE2'))),
                        ),
                        'INPUTUSER'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBONUS_OPTION_TITLE_INPUTUSER'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'BONUSORDERPAY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            'TITLE'=>Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSORDERPAY'),
                            'DEFAULT'=>'N',
                            "TYPE"=>"string",
                        ),
	                    'BONUSFORDISCOUNT'=>array(
		                    'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
		                    'TITLE'=>Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSFORDISCOUNT'),
		                    'DEFAULT'=>'N',
		                    "TYPE"=>"string",
	                    ),
                        'WITHOUTDELIVERYPRICE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_WITHOUTDELIVERYPRICETITLE'),
                            'DEFAULT'=>'N',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'ORDERPROPBONUSPAY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\OrderPropWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ORDERPROPBONUSPAY'),
                            'DEFAULT'=>'N',
                            "TYPE"=>"string",
                            "MULTIPLE"=>false
                        ),
                        'MAXPAYPROP'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\MaxPayPropWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MAX_PAY_PROP'),
                            'DEFAULT'=>'N',
                            "REQUIRED"=>false,
                            'TYPE'=>array('S','L','N'),
                            "MULTIPLE"=>false,
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(),
    array(),
    'GetRules'=>function ($profileID,$Filter=array(),$arFields=array()){
        $fltr = array();
        $flds = array();
        if(!array_key_exists("OFFERS",$arFields)){
            $arFields['OFFERS']=$this->GetBasket();
        }
        if($arFields['USERID']){
            $fltr['USERGROUP'] = $Filter['USERGROUP'];
            $flds['USERGROUP'] = $arFields['USERGROUP'];
            $fltr['ACTIVATE'] = $Filter['ACTIVATE'];
            $flds['ACTIVATE'] = $this->UserActive($arFields['USERID']);
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
        if($arFields['ORDER_PRICE']){
            $fltr['ORDERPRICE'] = ($arFields['ORDER_PRICE'] >= $Filter['ORDERPRICE']['OT'][0] && $arFields['ORDER_PRICE'] <= $Filter['ORDERPRICE']['DO'][0]) ? 'Y' : 'N';
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
            $fltr['ELEMENTFILTER']='N';$flds['ELEMENTFILTER']="N";
        }
        $Settings = $this->GetSettingsProf($profileID);
		$config = $this->GetConfigsProf($profileID);
        if(floatval($Filter['MINBONUSPAY'])!=0 && $Filter['MINBONUSPAY']!='' && floatval($Filter['MINBONUSPAY'])>0){
            $fltr['MINBONUSPAY']=floatval($Filter['MINBONUSPAY']);
            $flds['MINBONUSPAY']=floatval($this->GetUserBonus($arFields['USERID'],$Settings['SALEORDERAJAX'],$config['BONUSINNEROUT']['BONUSINNER']));
        }
        if($Filter['MINBONUSPAY1']) $Filter['MINBONUSPAY1']=array_filter($Filter['MINBONUSPAY1']);
        if($Filter['MINBONUSPAY1'] && is_array($Filter['MINBONUSPAY1']) && sizeof($Filter['MINBONUSPAY1'])>0){

            foreach($Filter['MINBONUSPAY1'] as $p=>$ll){
				if($ll['MINBONUSPAY1'][$p]!='' || $ll['MINBONUSPAY1'][$p]!=0){
					$fltr['MINBONUSPAY1_'.$p]=floatval($ll['MINBONUSPAY1'][$p]);
					$flds['MINBONUSPAY1_'.$p]=floatval($this->GetUserBonus($arFields['USERID'],$Settings['SALEORDERAJAX'],$p));
				}else{
					$fltr['MINBONUSPAY1_'.$p]=1;
					$flds['MINBONUSPAY1_'.$p]=1;
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
        $logic=array('MINBONUSPAY'=>">=");
        $filter=$this->GetFilterString($fltr,$flds,array(),$logic);
		
        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $bonus=$profile['BONUS'];$maxpay1=0;
        $Settings=$this->CheckSerialize($profile['SETTINGS']);
		$Filter=$this->CheckSerialize($profile['FILTER']);
		$glFilter=str_replace('"',"",$Filter['ELMNTFLTR']);
        if($glFilter!='((1 == 1))'){
            foreach($arFields['BASKET'] as $idf=>$arItems){
                if(!eval('return ' . $glFilter . ';')){
                    unset($arFields['BASKET'][$idf]);
                }
            }
        }
		
		if($Settings['BONUSFORDISCOUNT']=='Y'){
			$l=0;$f=0;
			foreach($arFields['BASKET'] as $bsk){
				$f+=($bsk['PRICE'])*$bsk['QUANTITY'];
			}
			$arFields['ORDER_PRICE']=$f;
		
		}
		$orderTotalSum=$Settings['WITHOUTDELIVERYPRICE']=='Y' ? floatval($arFields['ORDER_PRICE']) : floatval(floatval($arFields['ORDER_PRICE'])+floatval($arFields['DELIVERY_PRICE']));
		
	    if($Filter['DISCOUNTWITHOUT']['ACTIVE']=='Y'){
		
            $discount=$Filter['DISCOUNTWITHOUT']['DISCOUNT'];

            foreach($arFields['BASKET'] as $idof=>$arItem){
                $offer_price=$arItem['PRICE'];
                $del=false;
                if($this->CheckArray($arItem['DISCOUNT'])){
                    foreach($arItem['DISCOUNT'] as $dsk){
                        if($dsk['ACTIVE'] && in_array($dsk['ID'],$discount)){
                            $del=true;break;
                        }
                    }
                    if($del || $offer_price<$Filter['MINOFFERPRICE']) {
                        unset($arFields['BASKET'][$idof]);
			$orderTotalSum=$orderTotalSum-($arItem['PRICE']*$arItem['QUANTITY']);
                    }
                }

            }
        }
        $maxpay=$this->GetAllBonus($bonus,$orderTotalSum,false);



        if($Settings['MAXPAYPROP']['ACTIVE']=='Y'){
            $newmax=0;
            if(sizeof($arFields['BASKET'])>0) {
                foreach ($arFields['BASKET'] as $idof=>$ofrs) {
                    if($Settings['MAXPAYPROP']['ACTIVE']=='Y') {
                        if ($this->CheckArray($Settings['MAXPAYPROP']['ID'])) {

                            $cnt = (!empty($ofrs['QUANTITY']) ? $ofrs['QUANTITY'] : $arFields['COUNT']);
                            $offer_price = $ofrs['PRICE'] * $cnt;

                            $k=$this->BonusFromProp($ofrs,$Settings['MAXPAYPROP']['ID']);
                            if($k=='') $k=$bonus;
                            $arFields['BASKET'][$idof]['BONUS']=$this->GetAllBonus($k,$offer_price,$Settings['WITHOUTBONUSORDER']=='Y',$cnt ? $cnt : 1);
                            $newmax +=$arFields['BASKET'][$idof]['BONUS'];

                        }
                    }
                }
                $maxpay1 = $newmax;
            }
        }

        $maxpay=($maxpay1>0) ? $maxpay1 : $maxpay;

        $BONUSIS=floatval(($Settings['BONUSIS']!="") ? $Settings['BONUSIS'] : 1);
        $maxpay=floatval($maxpay*$BONUSIS);

        if($Settings['ROUND']=='PHP_ROUND_HALF_UP') $l=PHP_ROUND_HALF_UP;
        if($Settings['ROUND']=='PHP_ROUND_HALF_DOWN') $l=PHP_ROUND_HALF_DOWN;
        if($Settings['ROUND']=='PHP_ROUND_HALF_EVEN') $l=PHP_ROUND_HALF_EVEN;
        if($Settings['ROUND']=='PHP_ROUND_HALF_ODD') $l=PHP_ROUND_HALF_ODD;
        $maxpay=round($maxpay,1,$l);
        if($maxpay<0) $maxpay=0;
	    return $maxpay;
    },
    'CODE'=>'BONUSPAY',
);