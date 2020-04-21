<?

AddEventHandler("main", "OnBeforeUserRegister", Array("CustomRegistration", "BeforeReg"));
AddEventHandler("main", "OnAfterUserRegister", Array("CustomRegistration", "AfterReg"));
AddEventHandler("main", "OnAfterUserLogin", Array("ClassRedirect", "OnAfterUserLoginHandler") );

class ClassRedirect {
   function OnAfterUserLoginHandler(&$fields) {
       if( !empty($fields['USER_ID']) ) LocalRedirect("/personal/");
   }
}

class CustomRegistration {
    function BeforeReg(&$arFields) {

        $arFields["LOGIN"] = $_POST["PERSONAL_PHONE"];

        $email = htmlspecialcharsbx($_POST["USER_EMAIL"]);
        $pass = htmlspecialcharsbx($_POST["USER_PASSWORD"]);
        $repass = htmlspecialcharsbx($_POST["USER_CONFIRM_PASSWORD"]);
        $flag_email = 0;

        if (!empty($_POST["PERSONAL_PHONE"])) {
            $db_check = CUser::GetList(($by = "id"), ($order = "desc"), array("PERSONAL_PHONE" => $_POST["PERSONAL_PHONE"]), array("FIELDS" => array("ID", "LOGIN", "EMAIL"), "SELECT" => array()));
            while ($db_check_result = $db_check->fetch()) {
                $flag_email = 1;
                break;
            }
            if ($flag_email) {
                $GLOBALS['APPLICATION']->ThrowException('Пользователь с таким телефоном уже зарегистрирован');
                return false;
            }

        } else {
            $GLOBALS['APPLICATION']->ThrowException('Поле "Телефон" обязательно для заполнения');
            return false;
        }

        if (strlen($pass) < 6) {
            $GLOBALS['APPLICATION']->ThrowException('Пароль должен быть не менее 6 символов длиной');
            return false;
        }

        if ($pass != $repass) {
            $GLOBALS['APPLICATION']->ThrowException('Пароли должны совпадать');
            return false;
        }

        $arFields["PERSONAL_PHONE"] = htmlspecialcharsbx($_POST["PERSONAL_PHONE"]);
        $arFields["COMPANY"] = htmlspecialcharsbx($_POST["WORK_COMPANY"]);
        $arFields["INN"] = htmlspecialcharsbx($_POST["UF_INN"]);

        // echo "<pre>"; var_dump($arFields); echo "</pre>";
        // exit;

        return $arFields;
    }

    function AfterReg(&$arFields) {
        if ($_POST["USER_TYPE"] == "ROZN") {
            if (!empty($arFields["RESULT_MESSAGE"]["ID"])) {

                $phone = htmlspecialcharsbx($_POST["PERSONAL_PHONE"]);
                
                $user = new CUser;
                $user->Update($arFields["RESULT_MESSAGE"]["ID"], array(
                        "PERSONAL_PHONE" => $phone,
                        "GROUP_ID" => array(6)
                    )
                );

                $user->Logout();
                $user->Authorize($arFields["RESULT_MESSAGE"]["ID"]);
                // LocalRedirect("/lichnyy-kabinet?user_type=rozn");
                LocalRedirect("/login/");
            }



        } elseif ($_POST["USER_TYPE"] == "OPT") {
            if (!empty($arFields["RESULT_MESSAGE"]["ID"])) {

                $phone_user = htmlspecialcharsbx($_POST["PERSONAL_PHONE"]);
                $name_company = htmlspecialcharsbx($_POST["WORK_COMPANY"]);
                $city = htmlspecialcharsbx($_POST["UF_CITY"]);
                $inn = htmlspecialcharsbx($_POST["UF_INN"]);

                $user = new CUser;
                $user->Update($arFields["RESULT_MESSAGE"]["ID"],
                    array(
                        "PERSONAL_PHONE" => $phone_user,
                        "WORK_COMPANY" => $name_company,
                        "GROUP_ID" => array(7),
                        "UF_INN" => $inn,
                        "UF_CITY" => $city,
                    ));

                $user->Logout();
                $user->Authorize($arFields["RESULT_MESSAGE"]["ID"]);
                LocalRedirect("/login/");
            }
        }
        return $arFields;
    }
}



/*Автоматичсекая установка типа покупателя (юр./физ.)*/
AddEventHandler('sale', 'OnSaleComponentOrderUserResult', "change_type_user");
function change_type_user(&$arUserResult, $request, $arParams)
{
    global $USER;
    if ($USER->IsAuthorized()) {
//        $rsUser = $USER->GetByID($USER->GetID());
        $arGroups = CUser::GetUserGroup($USER->GetID());

        $curPersonType = 1;
        if (in_array(7, $arGroups)) {
            $curPersonType = 2;
        }
    }
    //echo $curPersonType;
    $arUserResult['PERSON_TYPE_ID'] = $curPersonType;
}

// ;

// /*Уведомление о новом заказе*/
AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
    $arOrder = CSaleOrder::GetByID($orderID);

    //-- получаем телефоны и адрес
    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
    $fio = "";
    $phone = "";
    $address = "";
    $city = "";
    $inn = "";
    $company = "";

    while ($arProps = $order_props->Fetch()) {
        if ($arProps["ORDER_PROPS_ID"] == "1" || $arProps["ORDER_PROPS_ID"] == "6") {
            $fio .= $arProps["VALUE"];
        }
        if ($arProps["ORDER_PROPS_ID"] == "2" || $arProps["ORDER_PROPS_ID"] == "7") {
            $phone .= $arProps["VALUE"];
        }
        if ($arProps["ORDER_PROPS_ID"] == "5" || $arProps["ORDER_PROPS_ID"] == "10") {
            $address .= $arProps["VALUE"];
        }
        if ($arProps["ORDER_PROPS_ID"] == "4" || $arProps["ORDER_PROPS_ID"] == "9") {
            $city .= $arProps["VALUE"];
        }
        if ($arProps["ORDER_PROPS_ID"] == "11") {
            $inn .= $arProps["VALUE"];
        }
        if ($arProps["ORDER_PROPS_ID"] == "12") {
            $company .= $arProps["VALUE"];
        }
    }
    $arFields["PHONE"] = $phone;
    $arFields["FIO"] = $fio;
    $arFields["ADDRESS"] = $address;
    $arFields["INN"] = $inn;
    $arFields["CITY"] = $city;
    $arFields["COMPANY"] = $company;
    $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"];
}




?>