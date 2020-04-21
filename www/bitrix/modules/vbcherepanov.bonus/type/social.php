<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "SOCIAL",
    Loc::getMessage('VBCHBB_TYPE_PROFILE_SOCIAL_NAME'),
    Loc::getMessage('VBCHBB_TYPE_PROFILE_SOCIAL_DSC'),
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
						 'SENDSOCIAL'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_SOCIAL_MESSAGE'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
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
                            'WIDGET'=>new ITRound\Vbchbbonus\SocialsourceWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SOCIALTITLE'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                            "SIZE"=>10,
                        ),
                        'COUNTINDAY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_COUNTDAYTITLE'),
                            'DEFAULT'=>1,
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "SIZE"=>5,
                        ),
                        'REVIEWLEN'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_COUNTSYMBOLTITLE'),
                            'DEFAULT'=>100,
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "SIZE"=>5,
                        ),
                        'UNIQUE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_UNIQUETITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
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
	                    'PRECISION'=>array(
		                    'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
		                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_PRECISION'),
		                    'DEFAULT'=>'0',
		                    "REQUIRED"=>false,
		                    'SIZE'=>10,
		                    'PLACEHOLDER'=>'',
		                    "MAXLENGHT"=>50,
		                    "TYPE"=>"string",
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
    array(),
    array(),
    'GetRules'=>function ($profileID,$Filter=array(),$arFields=array()) {
        $arFields['GROUP_ID']=explode(",",$arFields['GROUP_ID']);
        $Filter['COUNTINDAY']=intval($Filter['COUNTINDAY']);
        $Filter['REVIEWLEN']=intval($Filter['REVIEWLEN']);
        if($Filter['COUNTINDAY'])
            $arFields['COUNTINDAY']=$this->GetCountSocial($arFields['USERID'],$Filter['TYPESOURCE']);
        if($Filter['UNIQUE']=='Y'){
            $arFields['UNIQUE']=$this->GetUnique($arFields['TEXT']);
        }else $arFields['UNIQUE']='N';
        $logic=array('COUNTINDAY'=>'<=','REVIEWLEN'=>'>=');
        $key_replace=array("USERGROUP"=>"GROUP_ID");
        $r=\Bitrix\Main\UserTable::getList(array(
            'filter'=>array("ID"=>$arFields['USERID']),
            'select'=>array('ACTIVE'),
        ))->fetch();
        $arFields['ACTIVATE']=$r['ACTIVE'];
        $arFields['GROUP_ID']=$this->GetUserGroupByUser($arFields['USERID']);
        if(array_key_exists('ONLYAUTH',$Filter) && $Filter['ONLYAUTH']=='Y'){
            unset($arFields['GROUP_ID'][array_search(2,$arFields['GROUP_ID'])]);
        }
        $tmp=explode("_",$arFields['ID']);
        $arFields['TYPESOURCE']=$tmp[0];
        unset($Filter['REVIEWLEN'],$arFields['REVIEWLEN']);
		if($arFields['USERID']==""){
				unset($Filter['USERGROUP'],$arFields['GROUP_ID']);
			}
			unset($Filter['ONLYAUTH']);
        $filter=$this->GetFilterString($Filter,$arFields,$key_replace,$logic);
        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
	    $BonusConfig=$this->CheckSerialize($profile['BONUSCONFIG']);
	    $bonus=$profile['BONUS'];
	    $bonus=$this->GetAllBonus($bonus,$bonus,true);
	    $BONUSIS=floatval(($BonusConfig['BONUSIS']!="") ? $BonusConfig['BONUSIS'] : 1);
	    $bonus=floatval($bonus*$BONUSIS);

	    if($BonusConfig['ROUND']=='PHP_ROUND_HALF_UP') $l=PHP_ROUND_HALF_UP;
	    if($BonusConfig['ROUND']=='PHP_ROUND_HALF_DOWN') $l=PHP_ROUND_HALF_DOWN;
	    if($BonusConfig['ROUND']=='PHP_ROUND_HALF_EVEN') $l=PHP_ROUND_HALF_EVEN;
	    if($BonusConfig['ROUND']=='PHP_ROUND_HALF_ODD') $l=PHP_ROUND_HALF_ODD;
	    $bonus=round($bonus,$BonusConfig['PRECISION']?$BonusConfig['PRECISION']:0,$l);
	    if($BonusConfig['ROUNDONE']=='Y'){
		    if($bonus<1 && $bonus>0) $bonus=1;
	    }
        if($bonus<0) $bonus=0;
	    return $bonus;
    }
);
