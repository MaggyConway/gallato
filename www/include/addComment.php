<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


//отправка письма менеджеру   //////////////////////////////
if(isset($_POST)  && !empty($_POST)) {

    $event = $_POST['event'];
    
    if($event == 'COMMENT_FORM'){

        $USER_NAME = $_POST['fio'];
        $USER_COMMENT = Array("VALUE" => Array("TEXT" => $_POST['comment'], "TYPE" => "html или text"));

        $fields = array(
            'USER_NAME' => $USER_NAME,
            'USER_COMMENT' => $USER_COMMENT,
        );
    }

    if ($event) {
        CEvent::Send($event, 's1', $fields , 'N', '','' );
    }
        echo true;

} else {
    LocalRedirect("404.php", " 404 Страница не найдена");
}
/////////////////////////////////////////////////////////////



CModule::IncludeModule('iblock');
$el = new CIBlockElement;
$iblock_id = 16;

//Свойства
$PROP = array();

$PROP['PRODUCT'] = $_POST['productID'];
$PROP['FIO'] = $_POST['fio'];
$PROP["DATE"] = date("d.m.Y", time());
$PROP["COMMENT"] = Array("VALUE" => Array("TEXT" => $_POST['comment'], "TYPE" => "html или text"));

//Основные поля элемента
$fields = array(
    "DATE_CREATE" => date("Y-m-d H:i:s"), //Передаем дата создания
    "CREATED_BY" => $GLOBALS['USER']->GetID(),    //Передаем ID пользователя кто добавляет
    "IBLOCK_SECTION" => false, //ID разделов
    "IBLOCK_ID" => $iblock_id, //ID информационного блока он 24-ый
    "PROPERTY_VALUES" => $PROP, // Передаем массив значении для свойств
    "NAME" => $_POST['productNAME']."_".time(),
    "ACTIVE" => "N", //для модерации N
    "PREVIEW_TEXT" => "", //Анонс
    "DETAIL_TEXT" => "",
);

//Результат в конце отработки
if ($ID = $el->Add($fields)) {
	echo "suc";
} else {
	echo $el->LAST_ERROR;
}
?>