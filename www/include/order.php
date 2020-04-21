<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;
if ($_POST && !empty($_POST['EVENT_NAME']) && $_POST['EVENT_NAME'] == 'ADD_ORDER') {


    if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")):

        // if (!$USER->IsAuthorized()) {
        //     $useId = 1;
        //     $name = $_POST['name'] ?  $_POST['name'] : '';
        //     $city = $_POST['city'] ?  $_POST['city'] : '';
        //     $email = $_POST['email'] ?  $_POST['email'] : '';
        //     $phone = $_POST['phone'] ?  $_POST['phone'] : '';
        //     $adress = $_POST['adress'] ?  $_POST['adress'] : '';

        //     $comment = ' Имя: '.$name.'  \n  Город: '.$city.'  \n  Email: '.$email.'  \n  Телефон: '.$phone.'  \n  Адрес: '.$adress;


        $useId = IntVal($USER->GetID());
        $comment = '';

        $CUR_DATE = ConvertTimeStamp();
        
        $arBasketItems = array(
            "LID" => SITE_ID,
            "PERSON_TYPE_ID" => "1",
            "PAYED" => "N",
            "DATE_PAYED" => $CUR_DATE,
            "EMT_PAYED_ID" => $useId,
            "CANCELED" => "N",
            "STATUS_ID" => "N",
            "EMT_STATUS_ID" =>$useId,
            "PRICE" => '',
            "CURRENCY" => "RUB",
            "USER_ID" => IntVal($useId),
            "PAY_SYSTEM_ID" => "1",
            "PS_STATUS" => "Y",
            "PS_SUM" => '',
            "PS_CURRENCY" => "RUB",
            "PS_RESPONSE_DATE" => $CUR_DATE,
            "PRICE_DELIVERY" => "0",
            "SUM_PAID" => '',
            "DELIVERY_ID" => "1",
            "ALLOW_DELIVERY" => "Y",
            "DATE_ALLOW_DELIVERY" => $CUR_DATE,
            "EMT_ALLOW_DELIVERY" => $useId,
            "DISCOUNT_VALUE" => "0",
            "TAX_VALUE" => "0",
            "USER_DESCRIPTION" => $comment
        );


        if (CModule::IncludeModule("statistic"))
            $arFields["STAT_GID"] = CStatistic::GetEventParam();

        $ORDER_ID = CSaleOrder::Add($arBasketItems);
        CSaleBasket::OrderBasket($ORDER_ID,$useId, SITE_ID);
        $ORDER_ID = IntVal($ORDER_ID);
//        $ex = $APPLICATION->GetException();

        CSaleBasket::DeleteAll($USER->GetID(), false);

        echo "true";

    endif;

} ?>