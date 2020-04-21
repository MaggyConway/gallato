<?
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global array $FIELDS */
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    \ITRound\Vbchbbonus;
$module_id="vbcherepanov.bonus";
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/prolog.php');

Loc::loadMessages(__FILE__);

$publicMode = $adminPage->publicMode;
$selfFolderUrl = $adminPage->getSelfFolderUrl();

$saleModulePermissions = $APPLICATION->GetGroupRight($module_id);
$readOnly = ($saleModulePermissions < 'W');
if ($saleModulePermissions < 'R')
    $APPLICATION->AuthForm('');

Loader::includeModule($module_id);

$request = Main\Context::getCurrent()->getRequest();

$adminListTableID = 'tbl_itr_bonuc_coupon_statistic';

$adminSort = new CAdminSorting($adminListTableID, 'ID', 'ASC');
$adminList = new CAdminUiList($adminListTableID, $adminSort);

$filterFields = array(
    array(
        "id" => "COUPON",
        "name" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_COUPON"),
        "filterable" => "=",
        "default" => true
    ),
    array(
        "id" => "CLIENT_IP",
        "name" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_CLIENT_IP"),
        "filterable" => "=",
        "default" => true
    ),
    array(
        "id" => "USER_ID",
        "name" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_USER_ID"),
        "type" => "int",
        "filterable" => "="
    ),
);

$filter = array();

$adminList->AddFilter($filterFields, $filter);

$headerList = array();
$headerList['ID'] = array(
    'id' => 'ID',
    'content' => 'ID',
    'sort' => 'ID',
    'default' => true
);
$headerList['COUPON'] = array(
    'id' => 'COUPON',
    'content' => Loc::getMessage('BONUS_COUPON_LIST_FILTER_COUPON'),
    'title' => Loc::getMessage('BONUS_COUPON_LIST_FILTER_COUPON'),
    'sort' => 'COUPON',
    'default' => true
);
$headerList['USER_ID'] = array(
    'id' => 'USER_ID',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_STAT_USER_ID'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_STAT_USER_ID'),
    'sort' => 'USER_ID',
    'default' => true
);
$headerList['TIMESTAMP_X'] = array(
    'id' => 'TIMESTAMP_X',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_STAT_TIMESTAMP_X'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_STAT_TIMESTAMP_X'),
    'sort' => 'TIMESTAMP_X',
    'default' => true
);

$headerList['CLIENT_IP'] = array(
    'id' => 'CLIENT_IP',
    'content' => Loc::getMessage('BONUS_COUPON_LIST_FILTER_CLIENT_IP'),
    'title' => Loc::getMessage('BONUS_COUPON_LIST_FILTER_CLIENT_IP'),
    'sort' => 'CLIENT_IP',
    'default' => true
);
$headerList['CLIENT_BROWSER'] = array(
    'id' => 'CLIENT_BROWSER',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_STAT_CLIENT_BROWSER'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_STAT_CLIENT_BROWSER'),
    'sort' => 'CLIENT_BROWSER',
    'default' => true
);
$headerList['CLIENT_REFERER'] = array(
    'id' => 'CLIENT_REFERER',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_STAT_CLIENT_REFERER'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_STAT_CLIENT_REFERER'),
    'sort' => 'CLIENT_REFERER',
    'default' => true
);
$headerList['CLIENT_UTM'] = array(
    'id' => 'CLIENT_UTM',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_STAT_CLIENT_UTM'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_STAT_CLIENT_UTM'),
    'sort' => 'CLIENT_UTM',
    'default' => true
);
$adminList->AddHeaders($headerList);

$selectFields = array_fill_keys($adminList->GetVisibleHeaderColumns(), true);
$selectFields['ID'] = true;
$selectFieldsMap = array_fill_keys(array_keys($headerList), false);
$selectFieldsMap = array_merge($selectFieldsMap, $selectFields);

if (!isset($by))
    $by = 'ID';
if (!isset($order))
    $order = 'ASC';

$userList = array();
$userIDs = array();
$nameFormat = CSite::GetNameFormat(true);

$rowList = array();
$usePageNavigation = true;
$navyParams = array();
if ($request['mode'] == 'excel')
{
    $usePageNavigation = false;
}
else
{
    $navyParams = CDBResult::GetNavParams(CAdminUiResult::GetNavSize($adminListTableID));
    if ($navyParams['SHOW_ALL'])
    {
        $usePageNavigation = false;
    }
    else
    {
        $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
        $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
    }
}
    $selectFields = array_keys($selectFields);


global $by, $order;

$getListParams = array(
    'select' => $selectFields,
    'filter' => $filter,
    'order' => array($by => $order)
);

if ($usePageNavigation)
{
    $getListParams['limit'] = $navyParams['SIZEN'];
    $getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
}
$totalPages = 0;
if ($usePageNavigation)
{
    $totalCount = Vbchbbonus\BonucCouponTable::getCount($getListParams['filter']);
    if ($totalCount > 0)
    {
        $totalPages = ceil($totalCount/$navyParams['SIZEN']);
        if ($navyParams['PAGEN'] > $totalPages)
            $navyParams['PAGEN'] = $totalPages;
        $getListParams['limit'] = $navyParams['SIZEN'];
        $getListParams['offset'] = $navyParams['SIZEN']*($navyParams['PAGEN']-1);
    }
    else
    {
        $navyParams['PAGEN'] = 1;
        $getListParams['limit'] = $navyParams['SIZEN'];
        $getListParams['offset'] = 0;
    }
}

$couponIterator = new CAdminUiResult(Vbchbbonus\BonucCouponTable::getList($getListParams), $adminListTableID);
if ($usePageNavigation)
{
    $couponIterator->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $couponIterator->NavRecordCount = $totalCount;
    $couponIterator->NavPageCount = $totalPages;
    $couponIterator->NavPageNomer = $navyParams['PAGEN'];
}
else
{
    $couponIterator->NavStart();
}

CTimeZone::Disable();
$adminList->SetNavigationParams($couponIterator, array("BASE_LINK" => $selfFolderUrl."vbchbb_coupone_statistic.php"));
while ($coupon = $couponIterator->Fetch())
{
    $coupon['ID'] = (int)$coupon['ID'];
    if ($selectFieldsMap['USER_ID'])
    {
        $coupon['USER_ID'] = (int)$coupon['USER_ID'];
        if ($coupon['USER_ID'] > 0)
            $userIDs[$coupon['USER_ID']] = true;
    }
    $urlEdit = $selfFolderUrl.'vbchbb_coupone_edit.php?ID='.$coupon['ID'].'&lang='.LANGUAGE_ID;
    $urlEdit = $adminSidePanelHelper->editUrlToPublicPage($urlEdit);

    $rowList[$coupon['ID']] = $row = &$adminList->AddRow(
        $coupon['ID'],
        $coupon,
        $urlEdit,
        Loc::getMessage('BT_BONUS_COUPON_LIST_MESS_EDIT_COUPON')
    );
    $row->AddViewField('ID', $coupon['ID']);
    if ($selectFieldsMap['TIMESTAMP_X'])
        $row->AddViewField('TIMESTAMP_X', $coupon['TIMESTAMP_X']);
    if ($selectFieldsMap['USER_ID'])
        $row->AddViewField('USER_ID', $coupon['USER_ID']);

    if ($selectFieldsMap['COUPON'])
        $row->AddViewField('COUPON', ($coupon['COUPON'] > 0 ? $coupon['COUPON'] : ''));
    if ($selectFieldsMap['CLIENT_IP'])
        $row->AddViewField('CLIENT_IP', ($coupon['CLIENT_IP'] > 0 ? $coupon['CLIENT_IP'] : ''));
    if ($selectFieldsMap['CLIENT_BROWSER'])
        $row->AddViewField('CLIENT_BROWSER', $coupon['CLIENT_BROWSER']);
    if ($selectFieldsMap['CLIENT_REFERER'])
        $row->AddViewField('CLIENT_REFERER', $coupon['CLIENT_REFERER']);
    if ($selectFieldsMap['CLIENT_UTM'])
        $row->AddViewField('CLIENT_UTM', $coupon['CLIENT_UTM']);
}
CTimeZone::Enable();

if (!empty($rowList) && ($selectFieldsMap['USER_ID']))
{
    if (!empty($userIDs))
    {
        $userIterator = Main\UserTable::getList(array(
            'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
            'filter' => array('@ID' => array_keys($userIDs)),
        ));
        while ($oneUser = $userIterator->fetch())
        {
            $oneUser['ID'] = (int)$oneUser['ID'];
            if ($canViewUserList)
                $userList[$oneUser['ID']] = '<a href="'.$selfFolderUrl.'user_edit.php?lang='.LANGUAGE_ID.'&ID='.$oneUser['ID'].'">'.CUser::FormatName($nameFormat, $oneUser).'</a>';
            else
                $userList[$oneUser['ID']] = CUser::FormatName($nameFormat, $oneUser);
        }
        unset($oneUser, $userIterator);
    }

    /** @var CAdminListRow $row */
    foreach ($rowList as &$row)
    {
        if ($selectFieldsMap['USER_ID'])
        {
            $userName = '';
            if ($row->arRes['USER_ID'] > 0 && isset($userList[$row->arRes['USER_ID']]))
                $userName = $userList[$row->arRes['USER_ID']];
            $row->AddViewField('USER_ID', $userName);
        }
        unset($userName);
    }
    unset($row);
}

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('BT_BONUS_COUPON_STAT_LIST_TITLE'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$adminList->DisplayFilter($filterFields);
$adminList->DisplayList();

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');