<?php

namespace ITRound\Vbchbbonus;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class CouponTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> ACTIVE_FROM datetime optional
 * <li> ACTIVE_TO datetime optional
 * <li> COUPON string(32) mandatory
 * <li> TYPE int mandatory
 * <li> MAX_USE int mandatory
 * <li> USE_COUNT int mandatory
 * <li> USER_ID int mandatory
 * <li> TIMESTAMP_X datetime optional
 * <li> MODIFIED_BY int optional
 * <li> DATE_CREATE datetime optional
 * <li> CREATED_BY int optional
 * <li> DESCRIPTION string optional
 * <li> BONUS double mandatory default 0.0000
 * <li> BONUSLIVE string(250) optional
 * <li> BONUSACTIVE string(250) optional
 * <li> BONUSACCOUNTSID int mandatory
 * </ul>
 *
 * @package Bitrix\Bonuc
 **/
class CouponTable extends Main\Entity\DataManager
{
    const TYPE_ONE_ORDER = 2;
    const TYPE_MULTI_ORDER = 4;

    public static function getCouponTypes()
    {
        return [
            self::TYPE_ONE_ORDER => Loc::getMessage('BONUS_COUPON_TABLE_TYPE_ONE_ORDER'),
            self::TYPE_MULTI_ORDER => Loc::getMessage('BONUS_COUPON_TABLE_TYPE_MULTI_ORDER')
        ];
    }

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'itr_bonuc_coupon';
    }

    public static function prepareCouponData(&$fields)
    {
        $result = new Main\Entity\Result();
        if (!empty($fields) && is_array($fields))
        {
            if (isset($fields['ACTIVE_FROM']) && is_string($fields['ACTIVE_FROM']))
            {
                $fields['ACTIVE_FROM'] = trim($fields['ACTIVE_FROM']);
                $fields['ACTIVE_FROM'] = ($fields['ACTIVE_FROM'] !== '' ? Main\Type\DateTime::createFromUserTime($fields['ACTIVE_FROM']) : null);
            }
            if (isset($fields['ACTIVE_TO']) && is_string($fields['ACTIVE_TO']))
            {
                $fields['ACTIVE_TO'] = trim($fields['ACTIVE_TO']);
                $fields['ACTIVE_TO'] = ($fields['ACTIVE_TO'] !== '' ? Main\Type\DateTime::createFromUserTime($fields['ACTIVE_TO']) : null);
            }
        }
        return $result;
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('COUPON_ENTITY_ID_FIELD'),
            ),
            'ACTIVE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('COUPON_ENTITY_ACTIVE_FIELD'),
            ),
            'ACTIVE_FROM' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('COUPON_ENTITY_ACTIVE_FROM_FIELD'),
            ),
            'ACTIVE_TO' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('COUPON_ENTITY_ACTIVE_TO_FIELD'),
            ),
            'COUPON' => array(
                'data_type' => 'string',
                'required' => true,
                'validation' => array(__CLASS__, 'validateCoupon'),
                'title' => Loc::getMessage('COUPON_ENTITY_COUPON_FIELD'),
            ),
            'TYPE' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('COUPON_ENTITY_TYPE_FIELD'),
            ),
            'MAX_USE' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('COUPON_ENTITY_MAX_USE_FIELD'),
            ),
            'USE_COUNT' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('COUPON_ENTITY_USE_COUNT_FIELD'),
            ),
            'USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'default_value'=>0,
                'title' => Loc::getMessage('COUPON_ENTITY_USER_ID_FIELD'),
            ),
            'TIMESTAMP_X' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('COUPON_ENTITY_TIMESTAMP_X_FIELD'),
            ),
            'MODIFIED_BY' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('COUPON_ENTITY_MODIFIED_BY_FIELD'),
            ),
            'DATE_CREATE' => array(
                'data_type' => 'datetime',
                'title' => Loc::getMessage('COUPON_ENTITY_DATE_CREATE_FIELD'),
            ),
            'CREATED_BY' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('COUPON_ENTITY_CREATED_BY_FIELD'),
            ),
            'DESCRIPTION' => array(
                'data_type' => 'text',
                'title' => Loc::getMessage('COUPON_ENTITY_DESCRIPTION_FIELD'),
            ),
            'BONUS' => array(
                'data_type' => 'float',
                'required' => true,
                'title' => Loc::getMessage('COUPON_ENTITY_BONUS_FIELD'),
            ),
            'BONUSLIVE' => array(
                'data_type' => 'string',
                'serialize'=>true,
                'validation' => array(__CLASS__, 'validateBonuslive'),
                'title' => Loc::getMessage('COUPON_ENTITY_BONUSLIVE_FIELD'),
                'save_data_modification' => function () {
                    return array(
                        function ($value) {
                            return serialize($value);
                        }
                    );
                },
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            ),
            'BONUSACTIVE' => array(
                'data_type' => 'string',
                'serialize'=>true,
                'validation' => array(__CLASS__, 'validateBonusactive'),
                'title' => Loc::getMessage('COUPON_ENTITY_BONUSACTIVE_FIELD'),
                'save_data_modification' => function () {
                    return array(
                        function ($value) {
                            return serialize($value);
                        }
                    );
                },
                'fetch_data_modification' => function () {
                    return array(
                        function ($value) {
                            return unserialize($value);
                        }
                    );
                }
            ),
            'BONUSACCOUNTSID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('COUPON_ENTITY_BONUSACCOUNTSID_FIELD'),
            ),
            'BONUSACCOUNTS' => new Main\Entity\ReferenceField(
                'BONUSACCOUNTS',
                '\ITRound\Vbchbbonus\CVbchBonusaccountsTable',
                array('=this.BONUSACCOUNTSID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            )
        );
    }

    /**
     * Returns validators for COUPON field.
     *
     * @return array
     */
    public static function validateCoupon()
    {
        return array(
            new Main\Entity\Validator\Length(null, 32),
        );
    }

    /**
     * Returns validators for BONUSLIVE field.
     *
     * @return array
     */
    public static function validateBonuslive()
    {
        return array(
            new Main\Entity\Validator\Length(null, 250),
        );
    }

    /**
     * Returns validators for BONUSACTIVE field.
     *
     * @return array
     */
    public static function validateBonusactive()
    {
        return array(
            new Main\Entity\Validator\Length(null, 250),
        );
    }
    protected static function setTimestamp(array &$result, array $data, array $keys)
    {
        foreach ($keys as $oneKey)
        {
            $setField = true;
            if (array_key_exists($oneKey, $data))
                $setField = ($data[$oneKey] !== null && !is_object($data[$oneKey]));

            if ($setField)
                $result[$oneKey] = new Main\Type\DateTime();
        }
        unset($oneKey);
    }

    protected static function setUserID(array &$result, array $data, array $keys)
    {
        static $currentUserID = false;
        if ($currentUserID === false)
        {
            global $USER;
            $currentUserID = (isset($USER) && $USER instanceof \CUser ? (int)$USER->getID() : null);
        }
        foreach ($keys as $oneKey)
        {
            $setField = true;
            if (array_key_exists($oneKey, $data))
                $setField = ($data[$oneKey] !== null && (int)$data[$oneKey] <= 0);

            if ($setField)
                $result[$oneKey] = $currentUserID;
        }
        unset($oneKey);
    }

    public static function onBeforeAdd(Main\Entity\Event $event)
    {
        $result = new Main\Entity\EventResult;
        $data = $event->getParameter('fields');

        $modifyFieldList = array();
        self::setUserID($modifyFieldList, $data, array('CREATED_BY', 'MODIFIED_BY'));
        self::setTimestamp($modifyFieldList, $data, array('DATE_CREATE', 'TIMESTAMP_X'));

        if (!empty($modifyFieldList))
            $result->modifyFields($modifyFieldList);
        unset($modifyFieldList, $data);

        return $result;
    }

    public static function onAfterAdd(Main\Entity\Event $event)
    {
        $data = $event->getParameter('fields');
        $id = (int)$data['DISCOUNT_ID'];
        unset($id, $data);

    }

    public static function onBeforeUpdate(Main\Entity\Event $event)
    {
        $result = new Main\Entity\EventResult;
        $data = $event->getParameter('fields');

        $modifyFieldList = array();
        self::setUserID($modifyFieldList, $data, array('MODIFIED_BY'));
        self::setTimestamp($modifyFieldList, $data, array('TIMESTAMP_X'));

        if (!empty($modifyFieldList))
            $result->modifyFields($modifyFieldList);
        unset($modifyFieldList, $data);

        return $result;
    }

    public static function onAfterUpdate(Main\Entity\Event $event)
    {
    }

    public function ActivateCoupon($coupon,$user_id){
        $result=[
            'SUCCES'=>true,
            'ERROR'=>[],
        ];

        $error=[];
        $coupon=trim(htmlspecialcharsbx($coupon));
        $user_id=intval($user_id);
        if(strlen($coupon)==0){
            $error[]=Loc::getMessage('BONUS_COUPON_TABLE_COUPON_EMPTY');
        }
        if(intval($user_id)==0){
            $error[]=Loc::getMessage('BONUS_COUPON_TABLE_USER_ID_BAD');
        }
        $currentDatetime = new \Bitrix\Main\Type\DateTime();
        global $USER;
        $coupone=self::getList([
                'filter'=>[
                    'ACTIVE'=>'Y',
                    'COUPON'=>$coupon,
                    [
                        'LOGIC'=>'OR',
                        ['USER_ID'=>$user_id],
                        ['USER_ID'=>false],
                    ],
                    [
                        'LOGIC' => 'OR',
                        'ACTIVE_FROM' => '',
                        '<=ACTIVE_FROM' => $currentDatetime
                    ],
                    [
                        'LOGIC' => 'OR',
                        'ACTIVE_TO' => '',
                        '>=ACTIVE_TO' => $currentDatetime
                    ]
                ]
            ]
        )->fetch();
        if($coupone){
            if($USER->IsAuthorized()){

                if($coupone['USER_ID']!=0 && ($USER->GetID()!=$user_id || $USER->GetID()!=$coupone['USER_ID'] || $user_id!=$coupone['USER_ID'])){
                    $error[]=Loc::getMessage('BONUS_COUPON_TABLE_USER_ID_NOT');
                }else{
                    $BBCORE=new Vbchbbcore();
                    $bonus=$BBCORE->BonusParams(
                        $coupone['BONUS'],
                        [
                            'DELAY'=>$coupone['BONUSACTIVE'],
                            'TIMELIFE'=>$coupone['BONUSLIVE'],
                            'BONUSINNERIN'=>
                                [
                                    'BONUSINNER'=>$coupone['BONUSACCOUNTSID']
                                ]
                        ]
                    );
                    $resbon = AccountTable::getList(array(
                        'filter' => array('USER_ID' => $user_id, "BONUSACCOUNTSID" => $coupone['BONUSACCOUNTSID']),
                    ));
                    if ($acc_bon = $resbon->fetch()) {
                        $bonus_old = $acc_bon['CURRENT_BUDGET'];
                    }
                    $profile = CvbchbonusprofilesTable::getList(array(
                        'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $BBCORE->SITE_ID),
                    ))->fetch();

                    $l=$BBCORE->CheckSerialize($profile['NOTIFICATION']);
                    $l['TRANSACATIONMESSAGE']=Loc::getMessage('BONUS_COUPON_TABLE_TRANSACATION_DESCRIPTION',['#COUPON#'=>$coupon]);
                    $profile['NOTIFICATION']=base64_encode(serialize($l));
                    $BBCORE->AddBonus(
                        $bonus,
                        [
                            'BONUSACCOUNTSID'=>$coupone['BONUSACCOUNTSID'],
                            'USER_ID'=>$user_id,
                            'SITE_ID'=>$BBCORE->SITE_ID,
                            'IDUNITS'=>'ACTIVE_COUPONE_'.$coupon.'_'.date("Y-m-d_H:i:s"),
                            'DESCRIPTION'=>Loc::getMessage('BONUS_COUPON_TABLE_TRANSACATION_DESCRIPTION',['#COUPON#'=>$coupon]),

                        ],
                        $profile
                    );
                    $newCouponFields=[];
                    if($coupone['TYPE']==self::TYPE_ONE_ORDER){
                        $newCouponFields['ACTIVE']='N';
                    }elseif($coupone['TYPE']==self::TYPE_MULTI_ORDER){
                        $count_use=$coupone['USE_COUNT'];
                        $count_use=$count_use+1;
                        $newCouponFields['USE_COUNT']=$count_use;
                        if($count_use==$coupone['MAX_USE']){
                            $newCouponFields['ACTIVE']='N';
                        }
                    }
                    self::update($coupone['ID'],$newCouponFields);
                    $statisticFields=[
                        'COUPON'=>$coupon,
                        'USER_ID'=>$user_id,
                        'TIMESTAMP_X'=>$currentDatetime,
                        'CLIENT_IP'=>$_SERVER['REMOTE_ADDR'],
                        'CLIENT_BROWSER'=>$_SERVER['HTTP_USER_AGENT'],
                        'CLIENT_REFERER'=>$_SERVER['HTTP_REFERER'],
                        'CLIENT_UTM'=>'',
                    ];
                    $p=BonucCouponTable::add($statisticFields);
                    $resbon = AccountTable::getList(array(
                        'filter' => array('USER_ID' => $user_id, "BONUSACCOUNTSID" => $coupone['BONUSACCOUNTSID']),
                    ));
                    if ($acc_bon = $resbon->fetch()) {
                        $bonus_now = $acc_bon['CURRENT_BUDGET'];
                    }
                    return [
                        'SUCCES'=>true,
                        'BONUS_OLD'=>$bonus_old,
                        'BONUS_NOW'=>$bonus_now
                    ];
                }
            }
        }else{
            $error[]=Loc::getMessage('BONUS_COUPON_TABLE_COUPON_NOTFOUND');
        }
        if(sizeof($error)>0){
            $result=[
                'SUCCES'=>false,
                'ERROR'=>$error,
            ];
            return $result;
        }
    }

    public function AddCoupon($arFields){
        $settings=$arFields['DATA'];
        $uid=$arFields['USER_ID'];
        if($uid){
            $arFilter = array(
                'ID' => intval($uid)
            );
        }

        if(array_key_exists('CHECKUSERGROUP',$settings) &&  sizeof($settings['CHECKUSERGROUP'])>0){
            $arFilter['GROUPS_ID']=$settings['CHECKUSERGROUP'];
        }
        if(array_key_exists('CHECKUSERACTIVE',$settings) && $settings['CHECKUSERACTIVE']=='Y'){
            $arFilter['ACTIVE']=$settings['CHECKUSERACTIVE'];
        }
        $CouponFields=[];
        $CouponFields['COUPON']=self::GenerateCoupon(true,$settings['COUPON_MASK']);
        $CouponFields['ACTIVE']='Y';

        $CouponFields=[
            'ACTIVE'=>$settings['ACTIVE'],
            'ACTIVE_FROM'=>$settings['ACTIVE_FROM'] ? Main\Type\DateTime::createFromUserTime($settings['ACTIVE_FROM']) : '',
            'ACTIVE_TO'=> $settings['ACTIVE_TO'] ? Main\Type\DateTime::createFromUserTime($settings['ACTIVE_TO']) : '',
            'COUPON'=>self::GenerateCoupon(true,$settings['COUPON_MASK']),
            'TYPE'=>$settings['TYPE'],
            'DESCRIPTION'=>$settings['DESCRIPTION'],
            'BONUS'=>$settings['BONUS'],
            'BONUSLIVE'=>$settings['BONUSLIVE'],
            'BONUSACTIVE'=>$settings['BONUSACTIVE'],
            'BONUSACCOUNTSID'=>$settings['BONUSACCOUNTSID'],
            'USE_COUNT'=>0,
            'MAX_USE'=>$settings['MAX_USE'] ?$settings['MAX_USE'] :0,
        ];
        if($uid){
            $userID=[];
            $Utmp = \CUser::GetList($by, $order, $arFilter, array('SELECT' => array("ID")));
            while ($q = $Utmp->Fetch()) {
                $userID[] = $q['ID'];
            }
        }else{
            $userID[]=1;
        }

        foreach ($userID as $ui) {
            if (array_key_exists('CHECKUSER', $settings) && $settings['CHECKUSER'] == 'Y') {
                $CouponFields['USER_ID'] = $ui;
            }
            self::add($CouponFields);
        }
        unset($userID,$ui,$CouponFields,$arFilter,$settings);
    }


    public static function onAfterDelete(Main\Entity\Event $event)
    {

    }

    static private function returnABSNUM(){
        return self::returnABS(true).self::returnNumber();
    }

    static private function returnNumber(){
        $number='0123456789';
        return $number[mt_rand(0,strlen($number)-1)];
    }

    static private function returnABS($up=false){
        $abc='ABCDEFGHIJKLNMOPQRSTUVWXYZ';
        $abc1=strtolower($abc);
        $k=($up) ? $abc : $abc1;
        return  $k[mt_rand(0,strlen($k)-1)];
    }
    public function GenerateCoupon($check,$mask){
        $check = ($check === true);
        $result='';
        do
        {
            for($i=0;$i<strlen($mask);$i++){
                $result.=$mask[$i];
                $result=str_replace(
                    [   '[A]',
                        '[a]',
                        '[9]',
                        '[X]',
                    ],
                    [
                        self::returnABS(true),
                        self::returnABS(),
                        self::returnNumber(),
                        self::returnABSNUM(),
                    ],
                    $result
                );
            }
            if ($check)
            {
                $existCoupon = self::getList(
                    [
                        'filter'=>['COUPON'=>trim((string) $result)],
                        'select'=>['ID']
                    ]
                )->fetch();
                $resultCorrect = empty($existCoupon);
            }
        } while (!$resultCorrect);
        return $result;
    }
}