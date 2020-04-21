<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "SUBSCRIBE",
    Loc::getMessage('VBCHBB_TYPE_PROFILE_SUBSCRIBE_NAME'),
    Loc::getMessage('VBCHBB_TYPE_PROFILE_SUBSCRIBE_DSC'),
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
                            'BODY_TYPE'=>'text',
                            'COLS'=>65,
                            'ROWS'=>16,
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
                            "SIZE"=>10,
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
                        'TYPESOURCE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\ModulesourceWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MODULEREVIEWTITLE'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                        ),
                        'SUBSCRIBE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\SubscribeWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CHOOSESUBCRIBE'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
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
			'BITRIX'=>'subscribe_OnBeforeSubscriptionAdd',
			'MODULE'=>'CVbchbbEvents::OnBeforeSubscriptionAdd',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'subscribe_OnBeforeSubscriptionUpdate',
			'MODULE'=>'CVbchbbEvents::OnBeforeSubscriptionUpdate',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'sender_ContactOnAfterAdd',
			'MODULE'=>'CVbchbbEvents::ContactOnAfterAdd',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'sender_ContactOnAfterUpdate',
			'MODULE'=>'CVbchbbEvents::ContactOnAfterUpdate',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'sender_ContactOnAfterDelete',
			'MODULE'=>'CVbchbbEvents::ContactOnAfterDelete',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'sender_MailingSubscriptionOnAfterAdd',
			'MODULE'=>'CVbchbbEvents::MailingSubscriptionOnAfterAdd',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'sender_MailingSubscriptionOnAfterDelete',
			'MODULE'=>'CVbchbbEvents::MailingSubscriptionOnAfterDelete',
			'RULES'=>'true;',
		),
		array(
			'ACTIVE'=>"N",
			'BITRIX'=>'sender_OnAfterRecipientUnsub',
			'MODULE'=>'CVbchbbEvents::OnAfterRecipientUnsub',
			'RULES'=>'true;',
		),

	),
    array(),
    'GetRules'=>function ($profileID,$Filter=array(),$arFields=array()) {
	    if($arFields['USERID']==""){
		    return false;
	    }
	    if($arFields['ACTIVE']=='Y' && $arFields['CONFIRMED']=='Y'){
		    $tmp1=explode("_",$Filter['SUBSCRIBE']);
		    if(str_replace("_","",$arFields['TYPE'])!==$Filter['TYPESOURCE']) {return false;}
		    $Filter['RUB_ID']=array($tmp1[1]);
		    unset($Filter['TYPESOURCE'],$Filter['SUBSCRIBE']);
	    }else return false;

	    if(array_key_exists('ONLYAUTH',$Filter) && $Filter['ONLYAUTH']=='Y'){
		    unset($arFields['GROUP_ID'][array_search(2,$arFields['GROUP_ID'])]);
	    }
	    $flds=array(
		    'GROUP_ID'=>$arFields['GROUP_ID'],
		    'ACTIVE'=>$arFields['ACTIVE'],
		    'RUB_ID'=>$arFields['RUB_ID'],
	    );
	    unset($arFields['CONFIRMED'],$arFields['TYPE'],$arFields['USERID'],$arFields['ID'],$Filter['ONLYAUTH']);
	    unset($Filter['ONLYAUTH']);
	    $key_replace=array("USERGROUP"=>"GROUP_ID","ACTIVATE"=>"ACTIVE");
	    $filter=$this->GetFilterString($Filter,$flds,$key_replace);
	    return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $bonus=$profile['BONUS'];
        $bonus=$this->GetAllBonus($bonus,$bonus,true);
        if($bonus<0) $bonus=0;
        return $bonus;
    }
);