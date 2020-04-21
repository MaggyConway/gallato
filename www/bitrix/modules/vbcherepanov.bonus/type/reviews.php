<?php
use \Bitrix\Main\Localization\Loc;
$profiles=array(
    "REVIEWS",
    Loc::getMessage('VBCHBB_TYPE_PROFILE_REVIEWS_NAME'),
    Loc::getMessage('VBCHBB_TYPE_PROFILE_REVIEWS_DSC'),
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
                            'DEFAULT'=>'Y',
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
                        'MINBONUSPAY1'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\BonusAccountsWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MINBONUS'),
                            'DEFAULT'=>'1',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                        ),
                        'TYPESOURCE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\ReviewsourceWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_TYPESOURCETITLE'),
                            'DEFAULT'=>'',
                            "REQUIRED"=>false,
                            "TYPE"=>"",
                            "MULTIPLE"=>false,
                            "SIZE"=>10,
                        ),
                        'COUNTINDAY'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\TextWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_COUNTDAYTITLE'),
                            'DEFAULT'=>'5',
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
                        'ONLYACTIVE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVEREVIEW'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'UNIQUE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_UNIQUETITLE'),
                            'DEFAULT'=>'Y',
                            "REQUIRED"=>false,
                            "TYPE"=>"string",
                            "MULTIPLE"=>false,
                        ),
                        'WITHPICTURE'=>array(
                            'WIDGET'=>new ITRound\Vbchbbonus\CheckboxWidget(),
                            "TITLE"=>Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_WITHPICTURE'),
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
    array(
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'iblock_OnAfterIBlockElementAdd',
            'MODULE'=>'CVbchbbEvents::OnAfterIBlockElementAdd',
            'RULES'=>'true;',
        ),
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'iblock_OnAfterIBlockElementUpdate',
            'MODULE'=>'CVbchbbEvents::OnAfterIBlockElementUpdate',
            'RULES'=>'true;',
        ),
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'forum_onBeforeMessageAdd',
            'MODULE'=>'CVbchbbEvents::onBeforeMessageAdd',
            'RULES'=>'true;',
        ),
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'forum_onAfterMessageUpdate',
            'MODULE'=>'CVbchbbEvents::onAfterMessageUpdate',
            'RULES'=>'true;',
        ),
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'blog_OnBeforeCommentAdd',
            'MODULE'=>'CVbchbbEvents::OnBeforeCommentAdd',
            'RULES'=>'true;',
        ),
        array(
            'ACTIVE'=>"N",
            'BITRIX'=>'blog_OnCommentUpdate',
            'MODULE'=>'CVbchbbEvents::OnCommentUpdate',
            'RULES'=>'true;',
        ),
    ),
    array(),
    'GetRules'=>function ($profileID,$Filter=array(),$arFields=array()) {

        if($Filter['ONLYACTIVE']=='Y'){
            if($arFields['ACTIVE']=='N') return false;
        }
        unset($Filter['ONLYACTIVE']);
        if(array_key_exists('ONLYAUTH',$Filter) && $Filter['ONLYAUTH']=='Y'){
            unset($arFields['USERGROUP'][array_search(2,$arFields['USERGROUP'])]);
        }

        $flds['USERGROUP']=$arFields['GROUP_ID'];
        $Filter['TYPE']=$Filter['TYPESOURCE']['TYPE'];
        foreach($Filter['TYPESOURCE'] as $key=>$val){
            if($val){
                if($key=='BLOG' ) {$key='BLOG_ID';$flds[$key]=$arFields['BLOG_ID'];}
                if($key=='IB'  ) {$key='IBLOCK_ID';$flds[$key]=$arFields['IBLOCK_ID'];}
                if($key=='FORUM') {$key='FORUM_ID';$flds[$key]=$arFields['FORUM_ID'];}
                if($key=='HL') {$key='HL_ID';$flds[$key]=$arFields['HL_ID'];}

                $Filter[$key]=$val;
            }

        }
        if($arFields['USERID']==""){
            unset($Filter['USERGROUP'],$arFields['GROUP_ID']);
        }else{
            $flds['ACTIVATE']=$this->UserActive($arFields['USERID']);
        }
        $Filter['COUNTINDAY']=intval($Filter['COUNTINDAY']);
        $Filter['REVIEWLEN']=intval($Filter['REVIEWLEN']);
        unset($Filter['TYPESOURCE'],$Filter['ONLYAUTH'],$Filter['MINBONUSPAY1']);
        $tp=0;
        $flds['TYPE']=$arFields['TYPE']=substr($arFields['TYPE'],0,strlen($arFields['TYPE'])-1);
        if($flds['TYPE']=='BLOG') {$tp=$arFields['BLOG_ID'];unset($Filter['IBLOCK_ID'],$Filter['FORUM_ID'],$Filter['HL_ID']);}
        if($flds['TYPE']=='FORUM') {$tp=$arFields['FORUM_ID'];unset($Filter['IBLOCK_ID'],$Filter['BLOG_ID'],$Filter['HL_ID']);}
        if($flds['TYPE']=='IB') {$tp=$arFields['IBLOCK_ID'];unset($Filter['BLOG_ID'],$Filter['FORUM_ID'],$Filter['HL_ID']);}
        if($flds['TYPE']=='HL') {$tp=$arFields['HL_ID'];unset($Filter['BLOG_ID'],$Filter['FORUM_ID'],$Filter['IBLOCK_ID']);}
        if($Filter['COUNTINDAY'])
            $flds['COUNTINDAY']=$this->GetCountReview($arFields['USERID'],$arFields['TYPE'],$tp,$arFields['TOPIC_ID']?$arFields['TOPIC_ID']:0);
        if($Filter['UNIQUE']=='Y'){
            $flds['UNIQUE']=$this->GetUnique($arFields['TEXT']);
        }else $flds['UNIQUE']='N';
        if($Filter['REVIEWLEN']){
            $flds['REVIEWLEN']=intval(strlen(trim($arFields['TEXT'] ? $arFields['TEXT'] : ($arFields['PREVIEW_TEXT'] ? $arFields['PREVIEW_TEXT'] : $arFields['DETAIL_TEXT']))));
        }
        $logic=array('COUNTINDAY'=>'<=','REVIEWLEN'=>'>=');
        $key_replace=array();
        if($Filter['WITHPICTURE']=='Y'){
            if((array_key_exists('PREVIEW_PICTURE',$arFields) && is_array($arFields['PREVIEW_PICTURE'])) ||
                (array_key_exists('DETAIL_PICTURE',$arFields) && is_array($arFields['DETAIL_PICTURE'])) ||
                (array_key_exists('ATTACH_IMG',$arFields) && is_array($arFields['ATTACH_IMG'])))
            {$flds['WITHPICTURE']='Y';}
            else
            {$flds['WITHPICTURE']='N';}
        }else{
            unset($Filter['WITHPICTURE']);
        }
        $filter=$this->GetFilterString($Filter,$flds,$key_replace,$logic);
        return $filter;
    },
    'GetBonus'=>function ($profile=array(),$arFields=array()){
        $bonus=0;
        $bonus=$profile['BONUS'];
        $bonus=$this->GetAllBonus($bonus,$bonus,true);
        if($bonus<0) $bonus=0;
        return $bonus;
    }
);