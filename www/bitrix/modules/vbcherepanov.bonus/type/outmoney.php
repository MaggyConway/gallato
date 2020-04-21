<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "OUTMONEY",
    Loc::getMessage('VBCHBB_TYPE_PROFILE_BACKMOYE_NAME'),
    Loc::getMessage('VBCHBB_TYPE_PROFILE_BACKMOYE_DSC'),

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
                'ACTIVE_FROM'=>array(
                    "WIDGET"=>new ITRound\Vbchbbonus\DatetimeWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE_FROM'),
                    "DEFAULT"=>"",
                    "TYPE"=>"",
                ),
                'ACTIVE_TO'=>array(
                    "WIDGET"=>new ITRound\Vbchbbonus\DatetimeWidget(),
                    "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVE_TO'),
                    "DEFAULT"=>"",
                    "TYPE"=>"",
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
                            'WIDGET'=>new ITRound\Vbchbbonus\HtmleditorWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SMSMESSAGE'),
                            'DEFAULT'=>'Y',
                            'HEIGHT'=>'400',
                            'BODY_TYPE'=>'text',
                            'WIDTH'=>'100%',
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
                            'WIDGET'=>new ITRound\Vbchbbonus\HtmleditorWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MESSAGE'),
                            'DEFAULT'=>'Y',
                            'HEIGHT'=>'400',
                            'BODY_TYPE'=>'html',
                            'WIDTH'=>'100%',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
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
                    ),
                ),
            ),
        ),
    ),
    array(),
    array(),
    'GetRules'=>function ($profileID,$Filter=array(),$arFields=array()) {
        $fltr = array();
        $flds = array();
        if($arFields['USERID']){
            $fltr['USERGROUP'] = $Filter['USERGROUP'];
            $flds['USERGROUP'] = $arFields['USERGROUP'];
            $fltr['ACTIVATE'] = $Filter['ACTIVATE'];
            $flds['ACTIVATE'] = $this->UserActive($arFields['USERID']);
        }

        $Settings = $this->GetSettingsProf($profileID);
        $config = $this->GetConfigsProf($profileID);
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
        $filter=$this->GetFilterString($fltr,$flds);

        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $bonus=0;
        $bonus=$profile['BONUS'];
        $bonus=$this->GetAllBonus($bonus,$arFields['ACCOUNT']);
        if($bonus<0) $bonus=0;
        return $bonus;
    },
    'CODE'=>'OUTMONEY',
);