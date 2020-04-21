<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$id = $_POST["id"];
//var_dump($id);

global $USER;
$rsUsers = CUser::GetByID($USER->GetID());
$arUser = $rsUsers->Fetch();
$fav_products = $arUser["UF_WISHLIST"];

$added = "false";
if(!in_array($id, $fav_products)){
    $fav_products[] = $id;
    $added = "true";
}else{
    $key = array_search($id,$fav_products);
    unset($fav_products[$key]);
}
$user = new CUser;
$fields = Array(
    "UF_WISHLIST" => $fav_products,
);
$user->Update($USER->GetID(), $fields);

echo $added;