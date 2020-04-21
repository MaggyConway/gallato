<?
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global array $FIELDS */
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    \ITRound\Vbchbbonus;
use ITRound\Vbchbbonus\CVbchBonusaccountsTable;
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

$adminListTableID = 'tbl_itr_bonuc_coupon';

$adminSort = new CAdminSorting($adminListTableID, 'ID', 'ASC');
$adminList = new CAdminUiList($adminListTableID, $adminSort);

$BonusAccountsIterator = CVbchBonusaccountsTable::getList(array(
    'select' => array('ID', 'NAME'),
    'order' => array('NAME' => 'ASC')
));
$listBonusAccounts = array();
while ($BonusAccounts = $BonusAccountsIterator->fetch())
{
    $BonusAccounts['NAME'] = (string)$BonusAccounts['NAME'];
    $title = '[' . $BonusAccounts['ID'] . ']' . ($BonusAccounts['NAME'] !== '' ? ' ' . htmlspecialcharsbx($BonusAccounts['NAME']) : '');
    $listBonusAccounts[$BonusAccounts['ID']] = $title;
}
$listCouponType = array();
$couponTypeList=ITRound\Vbchbbonus\couponTable::getCouponTypes();
foreach ($couponTypeList as $id => $title)
{
    $listCouponType[$id] = $title;
}

$filterFields = array(
    array(
        "id" => "COUPON",
        "name" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_COUPON"),
        "filterable" => "=",
        "default" => true
    ),
    array(
        "id" => "ACTIVE",
        "name" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_ACTIVE"),
        "type" => "list",
        "items" => array(
            "Y" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_ACTIVE_YES"),
            "N" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_ACTIVE_NO")
        ),
        "filterable" => "="
    ),
    array(
        "id" => "TYPE",
        "name" => Loc::getMessage("BONUS_COUPON_LIST_FILTER_TYPE"),
        "type" => "list",
        "items" => $listCouponType,
        "filterable" => "="
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

if (!$readOnly && $adminList->EditAction())
{
    if (isset($FIELDS) && is_array($FIELDS))
    {
        $conn = Main\Application::getConnection();
        foreach ($FIELDS as $couponID => $fields)
        {
            $couponID = (int)$couponID;
            if ($couponID <= 0 || !$adminList->IsUpdated($couponID))
                continue;

            $conn->startTransaction();
            $result = Vbchbbonus\CouponTable::prepareCouponData($fields);
            if ($result->isSuccess())
                $result = Vbchbbonus\CouponTable::update($couponID, $fields);

            if ($result->isSuccess())
            {
                $conn->commitTransaction();
            }
            else
            {
                $conn->rollbackTransaction();
                $adminList->AddUpdateError(implode('<br>', $result->getErrorMessages()), $couponID);
            }
            unset($result);
        }
        unset($fields, $couponID);
    }
}

if (!$readOnly && ($listID = $adminList->GroupAction()))
{
    $action = $_REQUEST['action'];
    if (!empty($_REQUEST['action_button']))
        $action = $_REQUEST['action_button'];
    $checkUseCoupons = ($action == 'delete');
    $discountList = array();

    if ($_REQUEST['action_target'] == 'selected')
    {
        $listID = array();
        $couponIterator = Vbchbbonus\CouponTable::getList(array(
            'select' => array('ID'),
            'filter' => $filter
        ));
        while ($coupon = $couponIterator->fetch())
        {
            $listID[] = $coupon['ID'];
        }
        unset($coupon, $couponIterator);
    }

    $listID = array_filter($listID);
    if (!empty($listID))
    {
        switch ($action)
        {
            case 'activate':
            case 'deactivate':
                $fields = array(
                    'ACTIVE' => ($action == 'activate' ? 'Y' : 'N')
                );
                foreach ($listID as &$couponID)
                {
                    $result = Vbchbbonus\CouponTable::update($couponID, $fields);
                    if (!$result->isSuccess())
                        $adminList->AddGroupError(implode('<br>', $result->getErrorMessages()), $couponID);
                    unset($result);
                }
                unset($couponID, $fields);
                break;
            case 'delete':
                foreach ($listID as &$couponID)
                {
                    $result = Vbchbbonus\CouponTable::delete($couponID);
                    if (!$result->isSuccess())
                        $adminList->AddGroupError(implode('<br>', $result->getErrorMessages()), $couponID);
                    unset($result);
                }
                unset($couponID);
                break;
        }
    }
    unset($discountList, $action, $listID);

    if ($adminList->hasGroupErrors())
    {
        $adminSidePanelHelper->sendJsonErrorResponse($adminList->getGroupErrors());
    }
    else
    {
        $adminSidePanelHelper->sendSuccessResponse();
    }
}

$headerList = array();
$headerList['ID'] = array(
    'id' => 'ID',
    'content' => 'ID',
    'sort' => 'ID',
    'default' => true
);
$headerList['BONUSACCOUNTS'] = array(
    'id' => 'BONUSACCOUNTS',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_BONUSACCOUNTSID'),
    'title' => Loc::getMessage('BONUS_HEADER_NAME_BONUSACCOUNTSID'),
    'sort' => 'ITROUND_VBCHBBONUS_COUPON_BONUSACCOUNTS_NAME',
    'default' => true
);
$headerList['COUPON'] = array(
    'id' => 'COUPON',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_COUPON'),
    'title' => Loc::getMessage('BONUS_HEADER_NAME_COUPON'),
    'sort' => 'COUPON',
    'default' => true
);
$headerList['ACTIVE'] = array(
    'id' => 'ACTIVE',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_ACTIVE'),
    'title' => Loc::getMessage('BONUS_HEADER_NAME_ACTIVE'),
    'sort' => 'ACTIVE',
    'default' => true
);
$headerList['ACTIVE_FROM'] = array(
    'id' => 'ACTIVE_FROM',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_ACTIVE_FROM'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_ACTIVE_FROM'),
    'sort' => 'ACTIVE_FROM',
    'default' => true
);
$headerList['ACTIVE_TO'] = array(
    'id' => 'ACTIVE_TO',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_ACTIVE_TO'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_ACTIVE_TO'),
    'sort' => 'ACTIVE_TO',
    'default' => true
);
$headerList['TYPE'] = array(
    'id' => 'TYPE',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_TYPE'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_TYPE'),
    'sort' => 'TYPE',
    'default' => true
);
$headerList['MAX_USE'] = array(
    'id' => 'MAX_USE',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_MAX_USE'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_MAX_USE'),
    'sort' => 'MAX_USE',
    'default' => true
);
$headerList['USE_COUNT'] = array(
    'id' => 'USE_COUNT',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_USE_COUNT'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_USE_COUNT'),
    'sort' => 'USE_COUNT',
    'default' => true
);
$headerList['USER_ID'] = array(
    'id' => 'USER_ID',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_USER_ID'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_USER_ID'),
    'sort' => 'USER_ID',
    'default' => true
);
$headerList['MODIFIED_BY'] = array(
    'id' => 'MODIFIED_BY',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_MODIFIED_BY'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_MODIFIED_BY'),
    'sort' => 'MODIFIED_BY',
    'default' => true
);
$headerList['TIMESTAMP_X'] = array(
    'id' => 'TIMESTAMP_X',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_TIMESTAMP_X'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_TIMESTAMP_X'),
    'sort' => 'TIMESTAMP_X',
    'default' => true
);
$headerList['CREATED_BY'] = array(
    'id' => 'CREATED_BY',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_CREATED_BY'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_CREATED_BY'),
    'sort' => 'CREATED_BY',
    'default' => false
);
$headerList['DATE_CREATE'] = array(
    'id' => 'DATE_CREATE',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_DATE_CREATE'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_DATE_CREATE'),
    'sort' => 'DATE_CREATE',
    'default' => false
);
$headerList['BONUS'] = array(
    'id' => 'BONUS',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_BONUS'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_BONUS'),
    'default' => false
);
$headerList['DESCRIPTION'] = array(
    'id' => 'DESCRIPTION',
    'content' => Loc::getMessage('BONUS_HEADER_NAME_DESCRIPTION'),
    'title' => Loc::getMessage('BONUS_HEADER_TITLE_DESCRIPTION'),
    'default' => false
);
$adminList->AddHeaders($headerList);

$selectFields = array_fill_keys($adminList->GetVisibleHeaderColumns(), true);
$selectFields['ID'] = true;
$selectFields['ACTIVE'] = true;
$selectFields['TYPE'] = true;
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

if ($selectFields['TYPE'])
    $selectFields['USE_COUNT'] = true;
if (isset($selectFields['BONUSACCOUNTSID']))
{
    unset($selectFields['BONUSACCOUNTS']);
    $selectFields['BONUSACCOUNTSID'] = true;
    $selectFields = array_keys($selectFields);
    $selectFields['BONUSACCOUNTS'] = 'ITROUND_VBCHBBONUS_COUPON_BONUSACCOUNTS_NAME';
}
else
{
    $selectFields = array_keys($selectFields);
}

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
    $totalCount = Vbchbbonus\CouponTable::getCount($getListParams['filter']);
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

$couponIterator = new CAdminUiResult(Vbchbbonus\CouponTable::getList($getListParams), $adminListTableID);
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
$adminList->SetNavigationParams($couponIterator, array("BASE_LINK" => $selfFolderUrl."vbchbb_coupone.php"));
while ($coupon = $couponIterator->Fetch())
{
    $coupon['ID'] = (int)$coupon['ID'];
    if ($selectFieldsMap['MAX_USE'])
        $coupon['MAX_USE'] = (int)$coupon['MAX_USE'];

    if ($selectFieldsMap['USE_COUNT'])
        $coupon['USE_COUNT'] = (int)$coupon['USE_COUNT'];
    if ($coupon['TYPE'] != Vbchbbonus\CouponTable::TYPE_MULTI_ORDER)
    {
        $coupon['MAX_USE'] = 0;
        $coupon['USE_COUNT'] = 0;
    }
    if ($selectFieldsMap['CREATED_BY'])
    {
        $coupon['CREATED_BY'] = (int)$coupon['CREATED_BY'];
        if ($coupon['CREATED_BY'] > 0)
            $userIDs[$coupon['CREATED_BY']] = true;
    }
    if ($selectFieldsMap['MODIFIED_BY'])
    {
        $coupon['MODIFIED_BY'] = (int)$coupon['MODIFIED_BY'];
        if ($coupon['MODIFIED_BY'] > 0)
            $userIDs[$coupon['MODIFIED_BY']] = true;
    }
    if ($selectFieldsMap['USER_ID'])
    {
        $coupon['USER_ID'] = (int)$coupon['USER_ID'];
        if ($coupon['USER_ID'] > 0)
            $userIDs[$coupon['USER_ID']] = true;
    }
    if ($selectFieldsMap['ACTIVE_FROM'])
        $coupon['ACTIVE_FROM'] = ($coupon['ACTIVE_FROM'] instanceof Main\Type\DateTime ? $coupon['ACTIVE_FROM']->toString() : '');
    if ($selectFieldsMap['ACTIVE_TO'])
        $coupon['ACTIVE_TO'] = ($coupon['ACTIVE_TO'] instanceof Main\Type\DateTime ? $coupon['ACTIVE_TO']->toString() : '');
    if ($selectFieldsMap['DATE_CREATE'])
        $coupon['DATE_CREATE'] = ($coupon['DATE_CREATE'] instanceof Main\Type\DateTime ? $coupon['DATE_CREATE']->toString() : '');
    if ($selectFieldsMap['TIMESTAMP_X'])
        $coupon['TIMESTAMP_X'] = ($coupon['TIMESTAMP_X'] instanceof Main\Type\DateTime ? $coupon['TIMESTAMP_X']->toString() : '');

    $urlEdit = $selfFolderUrl.'vbchbb_coupone_edit.php?ID='.$coupon['ID'].'&lang='.LANGUAGE_ID;
    $urlEdit = $adminSidePanelHelper->editUrlToPublicPage($urlEdit);

    $rowList[$coupon['ID']] = $row = &$adminList->AddRow(
        $coupon['ID'],
        $coupon,
        $urlEdit,
        Loc::getMessage('BT_BONUS_COUPON_LIST_MESS_EDIT_COUPON')
    );
    $row->AddViewField('ID', '<a href="'.$urlEdit.'">'.$coupon['ID'].'</a>');

    if ($selectFieldsMap['DATE_CREATE'])
        $row->AddViewField('DATE_CREATE', $coupon['DATE_CREATE']);
    if ($selectFieldsMap['TIMESTAMP_X'])
        $row->AddViewField('TIMESTAMP_X', $coupon['TIMESTAMP_X']);

    if ($selectFieldsMap['BONUSACCOUNTS'])
    {
        $discountEditUrl = $selfFolderUrl.'BONUS_edit.php?lang='.LANGUAGE_ID.'&ID='.$coupon['BONUSACCOUNTSID'];
        $discountEditUrl = $adminSidePanelHelper->editUrlToPublicPage($discountEditUrl);
        $row->AddViewField('BONUSACCOUNTS', '<a href="'.$discountEditUrl.'">['.
            $coupon['ITROUND_VBCHBBONUS_COUPON_BONUSACCOUNTS_ID'].']</a> '.htmlspecialcharsbx($coupon['ITROUND_VBCHBBONUS_COUPON_BONUSACCOUNTS_NAME']));
    }
    if ($selectFieldsMap['MAX_USE'])
        $row->AddViewField('MAX_USE', ($coupon['MAX_USE'] > 0 ? $coupon['MAX_USE'] : ''));
    if ($selectFieldsMap['USE_COUNT'])
        $row->AddViewField('USE_COUNT', ($coupon['USE_COUNT'] > 0 ? $coupon['USE_COUNT'] : ''));
    if ($selectFieldsMap['TYPE'])
        $row->AddViewField('TYPE', $couponTypeList[$coupon['TYPE']]);
    if ($selectFieldsMap['DESCRIPTION'])
        $row->AddViewField('DESCRIPTION', htmlspecialcharsbx($coupon['DESCRIPTION']));
    if ($selectFieldsMap['BONUS'])
        $row->AddViewField('BONUS', floatval($coupon['BONUS']));

    if (!$readOnly)
    {
        if ($selectFieldsMap['COUPON'])
            $row->AddInputField('COUPON', array('size' => 32));
        if ($selectFieldsMap['ACTIVE'])
            $row->AddCheckField('ACTIVE');
        if ($selectFieldsMap['ACTIVE_FROM'])
            $row->AddCalendarField('ACTIVE_FROM', array(), true);
        if ($selectFieldsMap['ACTIVE_TO'])
            $row->AddCalendarField('ACTIVE_TO', array(), true);
        if ($selectFieldsMap['BONUS'])
            $row->AddInputField('BONUS', array('size' => 15));
    }
    else
    {
        if ($selectFieldsMap['COUPON'])
            $row->AddInputField('COUPON', false);
        if ($selectFieldsMap['ACTIVE'])
            $row->AddCheckField('ACTIVE', false);
        if ($selectFieldsMap['ACTIVE_FROM'])
            $row->AddCalendarField('ACTIVE_FROM', false);
        if ($selectFieldsMap['ACTIVE_TO'])
            $row->AddCalendarField('ACTIVE_TO');
        if ($selectFieldsMap['BONUS'])
            $row->AddInputField('BONUS', array('size' => 15));
    }

    $actions = array();
    $actions[] = array(
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('BT_BONUS_COUPON_LIST_CONTEXT_EDIT'),
        'LINK' => $urlEdit,
        'DEFAULT' => true
    );
    if (!$readOnly)
    {
        $actions[] = array(
            'ICON' => 'copy',
            'TEXT' => Loc::getMessage('BT_BONUS_COUPON_LIST_CONTEXT_COPY'),
            'LINK' => CHTTP::urlAddParams($urlEdit, array("action" => "copy")),
            'DEFAULT' => false,
        );
        if ($coupon['ACTIVE'] == 'Y')
        {
            $actions[] = array(
                'ICON' => 'deactivate',
                'TEXT' => Loc::getMessage('BT_BONUS_COUPON_LIST_CONTEXT_DEACTIVATE'),
                'ACTION' => $adminList->ActionDoGroup($coupon['ID'], 'deactivate'),
                'DEFAULT' => false,
            );
        }
        else
        {
            $actions[] = array(
                'ICON' => 'activate',
                'TEXT' => Loc::getMessage('BT_BONUS_COUPON_LIST_CONTEXT_ACTIVATE'),
                'ACTION' => $adminList->ActionDoGroup($coupon['ID'], 'activate'),
                'DEFAULT' => false,
            );
        }
        $actions[] = array('SEPARATOR' => true);
        $actions[] = array(
            'ICON' =>'delete',
            'TEXT' => Loc::getMessage('BT_BONUS_COUPON_LIST_CONTEXT_DELETE'),
            'ACTION' => "if (confirm('".Loc::getMessage('BT_BONUS_COUPON_LIST_CONTEXT_DELETE_CONFIRM')."')) ".$adminList->ActionDoGroup($coupon['ID'], 'delete')
        );
    }
    $row->AddActions($actions);
    unset($actions, $row);
}
CTimeZone::Enable();

if (!empty($rowList) && ($selectFieldsMap['CREATED_BY'] || $selectFieldsMap['MODIFIED_BY'] || $selectFieldsMap['USER_ID']))
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
        if ($selectFieldsMap['CREATED_BY'])
        {
            $userName = '';
            if ($row->arRes['CREATED_BY'] > 0 && isset($userList[$row->arRes['CREATED_BY']]))
                $userName = $userList[$row->arRes['CREATED_BY']];
            $row->AddViewField('CREATED_BY', $userName);
        }
        if ($selectFieldsMap['MODIFIED_BY'])
        {
            $userName = '';
            if ($row->arRes['MODIFIED_BY'] > 0 && isset($userList[$row->arRes['MODIFIED_BY']]))
                $userName = $userList[$row->arRes['MODIFIED_BY']];
            $row->AddViewField('MODIFIED_BY', $userName);
        }
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

$adminList->AddGroupActionTable([
    'edit' => true,
    'delete' => true,
    'activate' => Loc::getMessage('MAIN_ADMIN_LIST_ACTIVATE'),
    'deactivate' => Loc::getMessage('MAIN_ADMIN_LIST_DEACTIVATE')
]);

$contextMenu = array();
if (!$readOnly)
{
    $addUrl = $selfFolderUrl."vbchbb_coupone_edit.php?lang=".LANGUAGE_ID;
    $addUrl = $adminSidePanelHelper->editUrlToPublicPage($addUrl);
    $contextMenu[] = array(
        'ICON' => 'btn_new',
        'TEXT' => Loc::getMessage('BT_BONUS_COUPONT_LIST_MESS_NEW_COUPON'),
        'TITLE' => Loc::getMessage('BT_BONUS_COUPONT_LIST_MESS_NEW_COUPON'),
        'LINK' => $addUrl
    );
}
if (!empty($contextMenu))
{
    $adminList->setContextSettings(array("pagePath" => $selfFolderUrl."vbchbb_coupone.php"));
    $adminList->AddAdminContextMenu($contextMenu);
}
$adminList->CheckListMode();
$APPLICATION->SetTitle(Loc::getMessage('BT_BONUS_COUPON_LIST_TITLE'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
$adminList->DisplayFilter($filterFields);
$adminList->DisplayList();
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');