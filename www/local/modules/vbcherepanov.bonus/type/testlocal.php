<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "TEST LOCAL BONUS PROFILE",
    "Test local",
    "Тest local profiles",
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
                        'SENDSMS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SENDSMS'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
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
                            'WIDGET'=>new ITRound\Vbchbbonus\TextboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            'HELP'=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGEDESC'),
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
                        'ACTIVATE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVUSER'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
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
                        'ROUND'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\RoundWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ROUNDTITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
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
                        'PROPERTYDISCOUNT'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\IbpropertyWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PROPERTYDISCOUNTTITLE'),
                            'DEFAULT'=>'',
                            'TYPE'=>array(),
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
                            'TYPE'=>array('S','L'),
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
                        'WITHOUTBONUSORDER'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_WITHOUTBONUSORDERTITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'REFBONUS'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\ReferalWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_REFBONUSTITLE'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(),
    array(),
    'GetRules'=>function ($ID,$Filter=array(),$arFields=array()){
        $key_replace=array("USERGROUP"=>"GROUP_ID",'ACTIVATE'=>'ACTIVE');
        $filter=$this->GetFilterString($Filter,$arFields,$key_replace);
        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $bonus=0;
        $bonus=$profile['BONUS'];
        $bonus=$this->GetAllBonus($bonus,$bonus,true);
        return $bonus;
    }
);

